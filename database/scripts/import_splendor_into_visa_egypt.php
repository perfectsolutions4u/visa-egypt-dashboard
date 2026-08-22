<?php

/**
 * Copy Splendor Journeys content data into visa_egypt without wiping Visa Egypt tables.
 */

$source = 'splendorjourneys_db';
$dest = 'visa_egypt';

$pdo = new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET SESSION sql_mode = ''");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("SET UNIQUE_CHECKS = 0");

$skip = [
    'users',
    'migrations',
    'jobs',
    'failed_jobs',
    'queue_monitor',
    'oauth_access_tokens',
    'oauth_auth_codes',
    'oauth_clients',
    'oauth_personal_access_clients',
    'oauth_refresh_tokens',
    'personal_access_tokens',
    'permissions',
    'roles',
    'model_has_permissions',
    'model_has_roles',
    'role_has_permissions',
    'operations',
    'password_resets',
    'sessions',
    'cache',
    'cache_locks',
    'trip_seats',
];

function columns(PDO $pdo, string $schema, string $table): array
{
    $stmt = $pdo->prepare(
        'SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, CHARACTER_MAXIMUM_LENGTH
         FROM information_schema.columns
         WHERE table_schema = ? AND table_name = ?
         ORDER BY ORDINAL_POSITION'
    );
    $stmt->execute([$schema, $table]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function tableExists(PDO $pdo, string $schema, string $table): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?'
    );
    $stmt->execute([$schema, $table]);

    return (int) $stmt->fetchColumn() > 0;
}

function quoteIdent(string $name): string
{
    return '`' . str_replace('`', '``', $name) . '`';
}

$sourceTables = $pdo->query(
    "SELECT table_name FROM information_schema.tables WHERE table_schema = " . $pdo->quote($source)
)->fetchAll(PDO::FETCH_COLUMN);

echo "Remapping Visa Egypt demo clients off IDs 1 and 2...\n";

$pdo->exec("UPDATE `$dest`.`clients` SET id = 1001 WHERE id = 1");
$pdo->exec("UPDATE `$dest`.`clients` SET id = 1002 WHERE id = 2");
$pdo->exec("UPDATE `$dest`.`visa_bookings` SET client_id = 1001 WHERE client_id = 1");
$pdo->exec("UPDATE `$dest`.`visa_bookings` SET client_id = 1002 WHERE client_id = 2");
$pdo->exec("UPDATE `$dest`.`wallets` SET client_id = 1002 WHERE client_id = 2");
$pdo->exec("UPDATE `$dest`.`visa_payments` SET client_id = 1002 WHERE client_id = 2");
$pdo->exec("UPDATE `$dest`.`app_notifications` SET client_id = 1001 WHERE client_id = 1");
$pdo->exec("UPDATE `$dest`.`app_notifications` SET client_id = 1002 WHERE client_id = 2");

echo "Widening tour_translations.run to TEXT...\n";
$pdo->exec("ALTER TABLE `$dest`.`tour_translations` MODIFY `run` TEXT NULL");

echo "Creating duration tables if missing...\n";
foreach (['durations', 'duration_translations', 'tour_durations'] as $durationTable) {
    if (! tableExists($pdo, $dest, $durationTable) && tableExists($pdo, $source, $durationTable)) {
        $pdo->exec("CREATE TABLE `$dest`.`$durationTable` LIKE `$source`.`$durationTable`");
        echo "  created $durationTable\n";
    }
}

$copied = 0;
$skipped = 0;

foreach ($sourceTables as $table) {
    if (in_array($table, $skip, true)) {
        echo "SKIP $table (keep dashboard data)\n";
        $skipped++;
        continue;
    }

    if (! tableExists($pdo, $dest, $table)) {
        echo "SKIP $table (not in visa_egypt)\n";
        $skipped++;
        continue;
    }

    $srcCols = columns($pdo, $source, $table);
    $dstCols = columns($pdo, $dest, $table);
    $dstByName = [];
    foreach ($dstCols as $col) {
        $dstByName[$col['COLUMN_NAME']] = $col;
    }

    $common = [];
    foreach ($srcCols as $col) {
        if (isset($dstByName[$col['COLUMN_NAME']])) {
            $common[] = $col['COLUMN_NAME'];
        }
    }

    if ($table === 'trip_bookings') {
        foreach (['adults_count', 'children_count'] as $extra) {
            if (isset($dstByName[$extra]) && ! in_array($extra, $common, true)) {
                $common[] = $extra;
            }
        }
    }

    if ($common === []) {
        echo "SKIP $table (no common columns)\n";
        $skipped++;
        continue;
    }

    foreach ($common as $colName) {
        if (! isset($dstByName[$colName])) {
            continue;
        }
        $src = null;
        foreach ($srcCols as $col) {
            if ($col['COLUMN_NAME'] === $colName) {
                $src = $col;
                break;
            }
        }
        $dst = $dstByName[$colName];
        if ($src && in_array($src['DATA_TYPE'], ['text', 'mediumtext', 'longtext'], true) && $dst['DATA_TYPE'] === 'varchar') {
            $pdo->exec("ALTER TABLE `$dest`.`$table` MODIFY " . quoteIdent($colName) . " TEXT NULL");
            echo "  altered $table.$colName to TEXT\n";
        }
    }

    $selectParts = [];
    foreach ($common as $colName) {
        $quoted = quoteIdent($colName);
        if ($table === 'trips' && $colName === 'enabled') {
            $selectParts[] = "IF($quoted IN ('on','1',1), 1, 0) AS $quoted";
        } elseif ($table === 'trip_bookings' && $colName === 'adults_count') {
            $selectParts[] = "COALESCE(`number_of_passengers`, 0) AS $quoted";
        } elseif ($table === 'trip_bookings' && $colName === 'children_count') {
            $selectParts[] = "0 AS $quoted";
        } else {
            $selectParts[] = $quoted;
        }
    }

    $destList = implode(', ', array_map('quoteIdent', $common));
    $selectList = implode(', ', $selectParts);

    echo "COPY $table ... ";
    if ($table === 'clients') {
        $pdo->exec("DELETE FROM `$dest`.`$table` WHERE id NOT IN (1001, 1002)");
    } else {
        $pdo->exec("DELETE FROM `$dest`.`$table`");
    }
    $inserted = $pdo->exec(
        "INSERT INTO `$dest`.`$table` ($destList) SELECT $selectList FROM `$source`.`$table`"
    );
    echo "$inserted rows\n";
    $copied++;
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
$pdo->exec("SET UNIQUE_CHECKS = 1");

echo "\nDone. Copied $copied tables, skipped $skipped.\n";

$checks = [
    'tours',
    'tour_translations',
    'trips',
    'cities',
    'clients',
    'bookings',
    'destinations',
    'categories',
    'hotels',
    'trip_bookings',
];
foreach ($checks as $table) {
    $count = $pdo->query("SELECT COUNT(*) FROM `$dest`.`$table`")->fetchColumn();
    echo "  $table: $count\n";
}
