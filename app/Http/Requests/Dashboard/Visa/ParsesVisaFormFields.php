<?php

namespace App\Http\Requests\Dashboard\Visa;

trait ParsesVisaFormFields
{
    protected function decodeJsonFields(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (! array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                $data[$field] = null;
                continue;
            }

            if (is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        return $data;
    }

    protected function booleansFromCheckboxes(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            $data[$field] = $this->filled($field);
        }

        return $data;
    }

    protected function parseFeatures(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];

        return array_values(array_filter(array_map('trim', $lines)));
    }

    protected function parseLanguages(?string $raw): ?array
    {
        return $this->parseFeatures($raw);
    }
}
