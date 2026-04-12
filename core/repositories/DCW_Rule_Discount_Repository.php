<?php

if (!defined('ABSPATH')) {
    exit;
}

class DCW_Rule_Discount_Repository {

    protected $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'dcw_rule_discounts';
    }

    public function get_by_rule($rule_id) {
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE rule_id=%d", $rule_id));
        $discounts = [];
        foreach ($rows as $row) {
            $discount = new DCW_Rule_Discount();
            foreach ($row as $k => $v) {
                $discount->$k = $v;
            }
            $discounts[] = $discount;
        }
        return $discounts;
    }

    public function save_for_rule($rule_id, $discounts = []) {
        global $wpdb;

        $this->delete_by_rule($rule_id);

        foreach ($discounts as $discount) {

            if ($discount->value === null || $discount->value === '') {
                continue;
            }

            $data = [
                'rule_id' => (int)$rule_id,
                'type'    => $discount->type ?? '',
                'value'   => (float)($discount->value ?? 0),
                'extra_data'=> $discount->extra_data ?? '',
            ];

            $format = ['%d','%s','%f','%s'];

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