<?php
namespace Hp\EdforceDataManager;
use Hp\EdforceDataManager\Interfaces\HandleCRUD;

class EdForceData implements HandleCRUD {

    private $table;

    public function __construct($table) {
        global $wpdb;
        $this->table = $wpdb->prefix . $table;
    }

    public function save_data_to_db($data) {
        global $wpdb;
        return $wpdb->insert($this->table, $data);
    }

    public function update_data_in_db($data) {
        global $wpdb;
        if (!isset($data['id'])) return false;

        $id = $data['id'];
        unset($data['id']);

        return $wpdb->update($this->table, $data, ['id' => $id]);
    }

    public function get_data_from_db() {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM {$this->table}", ARRAY_A);
    }

    public function delete_data_from_db() {
        global $wpdb;
        if (!isset($_GET['id'])) return false;

        $id = intval($_GET['id']);
        return $wpdb->delete($this->table, ['id' => $id]);
    }

    public function delete_all_data_from_db() {
        global $wpdb;
        return $wpdb->query("DELETE FROM {$this->table}");
    }
}