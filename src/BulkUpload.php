<?php


namespace Hp\EdforceDataManager\Interfaces;

interface BulkUpload {
    public function upload($csv_file, $path);
}