<?php
if (!defined('ABSPATH')) exit;

class DCW_Rule_Repository {

    private $condition_repo;
    private $discount_repo;
    private $gift_repo;

    protected $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'dcw_rules';

        $this->condition_repo = new DCW_Rule_Condition_Repository();
        $this->discount_repo  = new DCW_Rule_Discount_Repository();
        $this->gift_repo      = new DCW_Rule_Gift_Repository();
    }

    public function get_all() {
        return $this->get_by_sql("SELECT * FROM {$this->table} ORDER BY id DESC");
    }

    public function get_enabled() {
        return $this->get_by_sql("SELECT * FROM {$this->table} WHERE enabled = 1 ORDER BY id DESC");
    }

    private function get_by_sql($sql) {
        global $wpdb;
        $rows = $wpdb->get_results($sql, ARRAY_A);

        $rules = [];
        foreach ($rows as $row) {
            $rule = new DCW_Rule();
            $rule->id = $row['id'];
            $rule->name = $row['name'];
            $rule->type = $row['type'];
            $rule->enabled = $row['enabled'];
            $rule->show_progress_card = $row['show_progress_card'];

            $rule->conditions = $this->condition_repo->get_by_rule($rule->id);
            $rule->discounts  = $this->discount_repo->get_by_rule($rule->id);
            $rule->gifts      = $this->gift_repo->get_by_rule($rule->id);
            $rules[] = $rule;
        }
        return $rules;
    }

    public function find($id) {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id);
        $all = $this->get_by_sql($sql);
        return !empty($all) ? $all[0] : null;
    }

    public function create(DCW_Rule $rule) {
        global $wpdb;

        $wpdb->insert(
            $this->table,
            [
                'name'    => $rule->name,
                'type'    => $rule->type,
                'enabled' => !empty($rule->enabled) ? 1 : 0,
                'show_progress_card' => !empty($rule->show_progress_card) ? 1 : 0
            ],
            ['%s','%s','%d','%d']
        );

        $rule_id = $wpdb->insert_id;

        $this->condition_repo->save_for_rule($rule_id, $rule->conditions ?? []);
        $this->discount_repo->save_for_rule($rule_id, $rule->discounts ?? []);
        $this->gift_repo->save_for_rule($rule_id, $rule->gifts ?? []);

        return $rule_id;
    }

    public function update(DCW_Rule $rule) {
        global $wpdb;

        if (empty($rule->id)) return false;

        $wpdb->update(
            $this->table,
            [
                'name'    => $rule->name,
                'type'    => $rule->type,
                'enabled' => !empty($rule->enabled) ? 1 : 0,
                'show_progress_card' => !empty($rule->show_progress_card) ? 1 : 0
            ],
            ['id' => $rule->id],
            ['%s','%s','%d','%d'],
            ['%d']
        );

        $this->condition_repo->save_for_rule($rule->id, $rule->conditions ?? []);
        $this->discount_repo->save_for_rule($rule->id, $rule->discounts ?? []);
        $this->gift_repo->save_for_rule($rule->id, $rule->gifts ?? []);

        return true;
    }

    public function delete($id) {
        global $wpdb;

        $this->condition_repo->delete_by_rule($id);
        $this->discount_repo->delete_by_rule($id);
        $this->gift_repo->delete_by_rule($id);

        return $wpdb->delete($this->table, ['id' => $id], ['%d']);
    }
}