<?php

namespace App\Services\Database;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackupService
{
    public const DIRECTORY = 'backups';

    public function directory(): string
    {
        $path = storage_path('app/'.self::DIRECTORY);
        File::ensureDirectoryExists($path);

        return $path;
    }

    /**
     * @return list<array{name: string, size: int, created_at: int}>
     */
    public function list(): array
    {
        $files = File::files($this->directory());

        $backups = [];
        foreach ($files as $file) {
            if (! $this->isBackupName($file->getFilename())) {
                continue;
            }

            $backups[] = [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'formatted_size' => $this->formatBytes($file->getSize()),
                'created_at' => $file->getMTime(),
                'formatted_date' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        usort($backups, static fn (array $a, array $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    public function create(?string $prefix = null): string
    {
        $this->prepareRuntime();

        if (! function_exists('gzopen')) {
            throw new RuntimeException('The PHP zlib extension is required to create backups.');
        }

        $connection = config('database.default');
        if (config("database.connections.{$connection}.driver") !== 'mysql') {
            throw new RuntimeException('Database backups are only supported for MySQL.');
        }

        $filename = ($prefix ? $prefix.'-' : '').'backup-'.now()->format('Y-m-d_H-i-s').'.sql.gz';
        $path = $this->path($filename);

        $handle = gzopen($path, 'wb9');
        if ($handle === false) {
            throw new RuntimeException('Unable to create backup file.');
        }

        try {
            $this->writeDump($handle);
        } finally {
            gzclose($handle);
        }

        $this->prune();

        return $filename;
    }

    public function download(string $filename): BinaryFileResponse
    {
        $filename = $this->assertFilename($filename);
        $path = $this->path($filename);

        if (! is_file($path)) {
            throw new RuntimeException('Backup file not found.');
        }

        return response()->download($path, $filename);
    }

    public function delete(string $filename): void
    {
        $filename = $this->assertFilename($filename);
        $path = $this->path($filename);

        if (! is_file($path)) {
            throw new RuntimeException('Backup file not found.');
        }

        File::delete($path);
    }

    public function restoreFromUpload(UploadedFile $file): string
    {
        $this->prepareRuntime();

        $original = strtolower($file->getClientOriginalName());
        if (! str_ends_with($original, '.sql') && ! str_ends_with($original, '.sql.gz')) {
            throw new RuntimeException('Only .sql or .sql.gz backup files are allowed.');
        }

        $safetyCopy = $this->create('auto-before-restore');

        $tempPath = $file->getRealPath();
        if ($tempPath === false) {
            throw new RuntimeException('Uploaded file could not be read.');
        }

        $sql = $this->readSql($tempPath, str_ends_with($original, '.gz'));
        $this->restoreSql($sql);

        return $safetyCopy;
    }

    public function restoreStored(string $filename): string
    {
        $this->prepareRuntime();

        $filename = $this->assertFilename($filename);
        $path = $this->path($filename);

        if (! is_file($path)) {
            throw new RuntimeException('Backup file not found.');
        }

        $safetyCopy = $this->create('auto-before-restore');
        $sql = $this->readSql($path, str_ends_with(strtolower($filename), '.gz'));
        $this->restoreSql($sql);

        return $safetyCopy;
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 1).' MB';
    }

    public function path(string $filename): string
    {
        return $this->directory().DIRECTORY_SEPARATOR.$filename;
    }

    public function assertFilename(string $filename): string
    {
        $filename = basename($filename);
        if (! $this->isBackupName($filename)) {
            throw new RuntimeException('Invalid backup filename.');
        }

        return $filename;
    }

    private function isBackupName(string $filename): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9._-]+\.sql(\.gz)?$/', $filename);
    }

    /**
     * @param resource $handle
     */
    private function writeDump($handle): void
    {
        $database = (string) config('database.connections.'.config('database.default').'.database');
        $now = now()->toDateTimeString();

        $this->write($handle, "-- Visa Egypt database backup\n");
        $this->write($handle, "-- Database: {$database}\n");
        $this->write($handle, "-- Created at: {$now}\n\n");
        $this->write($handle, "SET NAMES utf8mb4;\n");
        $this->write($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        $this->write($handle, "SET UNIQUE_CHECKS=0;\n");
        $this->write($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        foreach ($this->tables() as $table) {
            $create = DB::selectOne("SHOW CREATE TABLE `{$table}`");
            $createSql = $create->{'Create Table'} ?? null;
            if (! is_string($createSql)) {
                continue;
            }

            $this->write($handle, "-- Table `{$table}`\n");
            $this->write($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            $this->write($handle, $createSql.";\n\n");

            $this->writeTableData($handle, $table);
        }

        $this->write($handle, "SET UNIQUE_CHECKS=1;\n");
        $this->write($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
    }

    /**
     * @param resource $handle
     */
    private function writeTableData($handle, string $table): void
    {
        $rows = [];
        $count = 0;

        foreach (DB::table($table)->cursor() as $row) {
            $rows[] = $this->rowValues((array) $row);
            $count++;

            if ($count === 100) {
                $this->writeInsert($handle, $table, $rows);
                $rows = [];
                $count = 0;
            }
        }

        if ($count > 0) {
            $this->writeInsert($handle, $table, $rows);
        }

        $this->write($handle, "\n");
    }

    /**
     * @param resource $handle
     * @param list<string> $rows
     */
    private function writeInsert($handle, string $table, array $rows): void
    {
        $this->write($handle, "INSERT INTO `{$table}` VALUES\n".implode(",\n", $rows).";\n");
    }

    /**
     * @param array<string, mixed> $row
     */
    private function rowValues(array $row): string
    {
        $values = [];
        foreach ($row as $value) {
            $values[] = $this->sqlValue($value);
        }

        return '('.implode(',', $values).')';
    }

    private function sqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        $string = (string) $value;
        if ($this->shouldHexEncode($string)) {
            return '0x'.bin2hex($string);
        }

        return DB::getPdo()->quote($string);
    }

    private function shouldHexEncode(string $value): bool
    {
        return $value !== '' && (
            ! mb_check_encoding($value, 'UTF-8')
            || str_contains($value, '\\')
            || str_contains($value, '--')
            || str_contains($value, '/*')
            || str_contains($value, ';')
            || str_contains($value, "'")
            || str_contains($value, "\n")
            || str_contains($value, "\r")
            || str_contains($value, "\0")
        );
    }

    /**
     * @return list<string>
     */
    private function tables(): array
    {
        $tables = [];
        foreach (DB::select('SHOW FULL TABLES WHERE Table_type = \'BASE TABLE\'') as $row) {
            $values = array_values((array) $row);
            $table = $values[0] ?? null;
            if (is_string($table) && $table !== '') {
                $tables[] = $table;
            }
        }

        return $tables;
    }

    private function readSql(string $path, bool $gzipped): string
    {
        if ($gzipped) {
            $sql = file_get_contents('compress.zlib://'.$path);
        } else {
            $sql = file_get_contents($path);
        }

        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException('Backup file is empty or unreadable.');
        }

        return $sql;
    }

    private function restoreSql(string $sql): void
    {
        try {
            if ($this->restoreWithMysqlClient($sql)) {
                return;
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        DB::connection()->unsetEventDispatcher();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');
        DB::statement("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");

        try {
            foreach ($this->splitStatements($sql) as $statement) {
                DB::unprepared($statement);
            }
        } finally {
            DB::statement('SET UNIQUE_CHECKS=1');
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function restoreWithMysqlClient(string $sql): bool
    {
        $binary = $this->mysqlBinary();
        if ($binary === null) {
            return false;
        }

        $config = config('database.connections.'.config('database.default'));
        $defaults = tempnam(sys_get_temp_dir(), 'mycnf');
        if ($defaults === false) {
            return false;
        }

        $password = str_replace(['\\', '"'], ['\\\\', '\\"'], (string) ($config['password'] ?? ''));
        $contents = "[client]\n"
            ."host=\"{$config['host']}\"\n"
            ."port=\"{$config['port']}\"\n"
            ."user=\"{$config['username']}\"\n"
            ."password=\"{$password}\"\n";

        file_put_contents($defaults, $contents);

        try {
            $command = [
                $binary,
                '--defaults-extra-file='.$defaults,
                '--default-character-set=utf8mb4',
                $config['database'],
            ];

            $process = @proc_open(
                $this->commandLine($command),
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes,
                null,
                null
            );

            if (! is_resource($process)) {
                return false;
            }

            fwrite($pipes[0], $sql);
            fclose($pipes[0]);
            $stderr = stream_get_contents($pipes[2]) ?: '';
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exit = proc_close($process);

            if ($exit !== 0) {
                throw new RuntimeException(trim($stderr) !== '' ? $stderr : 'MySQL restore command failed.');
            }

            return true;
        } finally {
            @unlink($defaults);
        }
    }

    /**
     * @param list<string> $command
     */
    private function commandLine(array $command): string
    {
        return implode(' ', array_map('escapeshellarg', $command));
    }

    private function mysqlBinary(): ?string
    {
        $configured = env('MYSQL_BINARY');
        if (is_string($configured) && $configured !== '' && is_file($configured)) {
            return $configured;
        }

        $finder = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $result = trim((string) @shell_exec($finder.' mysql'));
        if ($result === '') {
            return null;
        }

        $first = strtok($result, "\r\n");

        return $first !== false && $first !== '' ? $first : null;
    }

    /**
     * @return list<string>
     */
    private function splitStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $inString = false;
        $quote = '';
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';

            if ($inString) {
                $buffer .= $char;

                if ($quote !== '`' && $char === '\\' && $next !== '') {
                    $buffer .= $next;
                    $i++;
                    continue;
                }

                if ($char === $quote) {
                    if ($next === $quote) {
                        $buffer .= $next;
                        $i++;
                        continue;
                    }
                    $inString = false;
                    $quote = '';
                }

                continue;
            }

            if ($char === '-' && $next === '-') {
                $after = $i + 2 < $length ? $sql[$i + 2] : '';
                if ($after === '' || $after === ' ' || $after === "\t" || $after === "\n" || $after === "\r") {
                    while ($i < $length && $sql[$i] !== "\n") {
                        $i++;
                    }
                    continue;
                }
            }

            if ($char === '#') {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                continue;
            }

            if ($char === '/' && $next === '*') {
                $i += 2;
                while ($i + 1 < $length && ! ($sql[$i] === '*' && $sql[$i + 1] === '/')) {
                    $i++;
                }
                $i++;
                continue;
            }

            if ($char === "'" || $char === '"' || $char === '`') {
                $inString = true;
                $quote = $char;
                $buffer .= $char;
                continue;
            }

            if ($char === ';') {
                $statement = trim($buffer);
                if ($statement !== '') {
                    $statements[] = $statement;
                }
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        $statement = trim($buffer);
        if ($statement !== '') {
            $statements[] = $statement;
        }

        return $statements;
    }

    /**
     * @param resource $handle
     */
    private function write($handle, string $contents): void
    {
        gzwrite($handle, $contents);
    }

    private function prune(int $keep = 30): void
    {
        $backups = $this->list();
        foreach (array_slice($backups, $keep) as $backup) {
            File::delete($this->path($backup['name']));
        }
    }

    private function prepareRuntime(): void
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');
    }
}
