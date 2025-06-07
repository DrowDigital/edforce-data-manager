<?php
namespace Hp\EdforceDataManager;
use Hp\EdforceDataManager\Interfaces\BulkUpload;

class DataBulkUpload implements BulkUpload {

    /**
     * Handles uploading and parsing a CSV file, and optionally saving the result to a path.
     *
     * @param string $csv_file Path to the uploaded CSV file (temporary file).
     * @param string $path Optional directory path to store results or logs.
     * @return array Parsed CSV data.
     */
    public function upload($csv_file, $path) {
        $data = [];

        if (!file_exists($csv_file) || !is_readable($csv_file)) {
            throw new \RuntimeException("CSV file does not exist or is not readable: $csv_file");
        }

        if (($handle = fopen($csv_file, 'r')) !== false) {
            $header = null;
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        // Optional: save or log to $path
        if ($path && is_dir($path) && is_writable($path)) {
            $logFile = rtrim($path, '/') . '/upload_log_' . time() . '.json';
            file_put_contents($logFile, json_encode($data, JSON_PRETTY_PRINT));
        }

        return $data;
    }
}