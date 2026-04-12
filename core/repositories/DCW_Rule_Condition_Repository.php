<?php

if (!defined('ABSPATH')) {
    exit;
}

class DCW_Rule_Condition_Repository {

    protected $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'dcw_rule_conditions';
    }

    public function get_by_rule($rule_id) {
        global $wpdb;
        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$this->table} WHERE rule_id=%d", $rule_id));

        $conditions = [];
        foreach ($rows as $row) {
            $cond = new DCW_Rule_Condition();
            foreach ($row as $k => $v) {
                $cond->$k = $v;
            }
            $conditions[] = $cond;
        }
        return $conditions;
    }

    public function save_for_rule($rule_id, $conditions = []) {
        global $wpdb;

        $this->delete_by_rule($rule_id);

        foreach ($conditions as $condition) {

            if ($condition->value === null || $condition->value === '') {
                continue;
            }

            $data = [
                'rule_id' => (int)$rule_id,
                'type'    => $condition->type ?? '',
                'value'   => (float)($condition->value ?? 0),
                'operator'=> $condition->operator ?? '',
                'extra_data'=> $condition->extra_data ?? '',
            ];

            $format = ['%d','%s','%f','%s','%s'];

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