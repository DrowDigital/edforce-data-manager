<?php

namespace Hp\EdforceDataManager\Interfaces;

interface HandleCRUD {
    public function save_data_to_db($data);

    public function update_data_in_db($data);

    public function get_data_from_db();

    public function delete_data_from_db();

    public function delete_all_data_from_db();
    
}