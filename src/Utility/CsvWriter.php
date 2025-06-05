<?php

namespace App\Utility;

class CsvWriter
{
    public function __construct(
        private readonly string $projectDir
    ) {}

    public function writeCsvBatch(array $rows, string $csvPath): void
    {
        $buffer = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($buffer, $row);
        }

        rewind($buffer);
        $csvContent = stream_get_contents($buffer);
        fclose($buffer);

        file_put_contents($csvPath, $csvContent, FILE_APPEND);
    }

    public function generateCsvPath(string $prefix = 'products_export_'): string
    {
        $timestamp = (new \DateTimeImmutable())->format('Y-m-d_H-i-s');
        return sprintf('%s/var/%s%s.csv', $this->projectDir, $prefix, $timestamp);
    }
}
