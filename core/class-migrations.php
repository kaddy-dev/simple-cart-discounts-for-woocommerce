<?php
if (!defined('ABSPATH')) exit;

class DCW_Migrations {

    private $removeTables = false;

    public function migrate() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $this->create_rules_table();
        $this->create_rule_conditions_table();
        $this->create_rule_discounts_table();
        $this->create_rule_gifts_table();
    }

    private function create_rules_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'dcw_rules';
        $charset_collate = $wpdb->get_charset_collate();

        if($this->removeTables) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL,
            enabled TINYINT(1) DEFAULT 1,
            PRIMARY KEY (id)
        ) $charset_collate;";

        dbDelta($sql);


        $column = $wpdb->get_results("
            SHOW COLUMNS FROM $table LIKE 'show_progress_card'
        ");
        
        if (empty($column)) {
            $wpdb->query("
                ALTER TABLE $table 
                ADD COLUMN show_progress_card TINYINT(1) DEFAULT 1
            ");
        }
        
    }

    private function create_rule_conditions_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'dcw_rule_conditions';
        $charset_collate = $wpdb->get_charset_collate();

        if($this->removeTables) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            rule_id BIGINT(20) UNSIGNED NOT NULL,
            type VARCHAR(50) NOT NULL,
            value DECIMAL(10,2) DEFAULT 0,
            operator VARCHAR(10) DEFAULT '',
            extra_data TEXT DEFAULT NULL,
            PRIMARY KEY (id),
            KEY rule_id (rule_id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    private function create_rule_discounts_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'dcw_rule_discounts';
        $charset_collate = $wpdb->get_charset_collate();

        if($this->removeTables) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            rule_id BIGINT(20) UNSIGNED NOT NULL,
            type VARCHAR(50) NOT NULL, 
            value DECIMAL(10,2) DEFAULT 0,
            extra_data TEXT DEFAULT NULL,
            PRIMARY KEY (id),
            KEY rule_id (rule_id)
        ) $charset_collate;";

        dbDelta($sql);
    }

    private function create_rule_gifts_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'dcw_rule_gifts';
        $charset_collate = $wpdb->get_charset_collate();

        if($this->removeTables) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            rule_id BIGINT(20) UNSIGNED NOT NULL,
            product_id BIGINT(20) UNSIGNED NOT NULL,
            quantity INT(11) DEFAULT 1,
            PRIMARY KEY (id),
            KEY rule_id (rule_id)
        ) $charset_collate;";

        dbDelta($sql);
    }
}