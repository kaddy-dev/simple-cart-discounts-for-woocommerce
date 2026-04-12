<?php

if (!defined('ABSPATH')) {
    exit;
}

class DCW_Admin_Menu {

    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;

        add_action('admin_menu', [$this, 'register_menu']);
    }

    public function register_menu() {

        add_menu_page(
            'Discount Rules',
            'Discount Rules',
            'manage_woocommerce',
            DCW_PLUGIN_SLUG,
            [$this, 'rules_page'],
            'dashicons-tag',
            56
        );

        add_submenu_page(
            DCW_PLUGIN_SLUG,
            'Add Rule',
            'Add Rule',
            'manage_woocommerce',
            'dcw-add-rule',
            [$this, 'add_rule_page']
        );

       add_submenu_page(
            null,
            'Edit Rule',
            'Edit Rule',
            'manage_woocommerce',
            'dcw-edit-rule',
            [$this, 'edit_rule_page']
        );

        add_submenu_page(
            DCW_PLUGIN_SLUG,
            'Settings',
            'Settings',
            'manage_options',
            'dcw-settings',
            [$this, 'settings_page']
        );
    }

    public function rules_page() {
        $this->controller->rules_page();
    }

    public function add_rule_page() {
        $this->controller->add_rule_page();
    }

    public function edit_rule_page() {
        $this->controller->edit_rule_page();
    }

    public function settings_page() {
        $this->controller->settings_page();
    }


}