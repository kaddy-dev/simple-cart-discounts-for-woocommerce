<?php

if (!defined('ABSPATH')) {
    exit;
}

class DCW_Rule_Gift_Repository {

    protected $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'dcw_rule_gifts';
    }

    public function get_by_rule($rule_id) {
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE rule_id=%d", $rule_id));

        $gifts = [];
        foreach ($rows as $row) {
            $gift = new DCW_Rule_Gift();
            foreach ($row as $k => $v) {
                $gift->$k = $v;
            }
            $gifts[] = $gift;
        }
        return $gifts;
    }

    public function save_for_rule($rule_id, $gifts = []) {
        global $wpdb;

        $this->delete_by_rule($rule_id);

        foreach ($gifts as $gift) {

            if(empty($gift->product_id)) {
                continue;
            }

            $data = [
                'rule_id' => $rule_id,
                'product_id'    => $gift->product_id ?? null,
                'quantity'   => $gift->quantity ?? 0
            ];

            $format = ['%d','%d','%d'];

            $wpdb->insert($this->table, $data, $format);

            if($wpdb->last_error) {
                error_log("DCW save_for_rule error: " . $wpdb->last_error);
            }
        }
    }

    public function delete_by_rule($rule_id) {
        global $wpdb;
        $wpdb->delete($this->table, ['rule_id' => $rule_id], ['%d']);
    }
    
}