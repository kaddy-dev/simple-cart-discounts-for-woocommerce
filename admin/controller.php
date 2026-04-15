<?php

if (!defined('ABSPATH')) {
    exit;
}

class DCW_Admin_Controller
{

    private $rule_repo;

    public function __construct()
    {
        $this->rule_repo = new DCW_Rule_Repository();

        add_action('admin_post_dcw_store_rule', [$this, 'store_rule']);
        add_action('admin_post_dcw_delete_rule', [$this, 'delete_rule']);
        add_action('admin_post_dcw_update_rule', [$this, 'update_rule']);
        add_action('admin_post_dcw_save_settings', [$this, 'store_settings']);
        add_action('wp_ajax_dcw_toggle_rule', [$this, 'toggle_rule']);
    }

    public function store_rule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!check_admin_referer('dcw_store_rule', 'dcw_nonce')) return;

        $rule = new DCW_Rule();
        $rule->name = sanitize_text_field($_POST['rule_name'] ?? '');
        $rule->type = sanitize_text_field($_POST['discount_type'] ?? '');
        $rule->enabled = !empty($_POST['enabled']) ? 1 : 0;
        $rule->show_progress_card = !empty($_POST['show_progress_card']) ? 1 : 0;

        // --- CONDITIONS ---
        $rule->conditions = [];

        if (!empty($_POST['conditions']) && is_array($_POST['conditions'])) {
            foreach ($_POST['conditions'] as $cond) {

                $condition = new DCW_Rule_Condition();
                $condition->type = sanitize_text_field($cond['type'] ?? '');
                $condition->value = sanitize_text_field($cond['value'] ?? '');
                $condition->operator = sanitize_text_field($cond['operator'] ?? '>=');

                $rule->conditions[] = $condition;
            }
        }

        // --- DISCOUNTS ---
        $rule->discounts = [];

        if (!empty($_POST['discounts']) && is_array($_POST['discounts'])) {
            foreach ($_POST['discounts'] as $disc) {

                $discount = new DCW_Rule_Discount();
                $discount->type = sanitize_text_field($disc['type'] ?? '');
                $discount->value = floatval($disc['value'] ?? 0);

                $rule->discounts[] = $discount;
            }
        }

        // --- GIFTS ---
        $rule->gifts = [];

        if (!empty($_POST['gifts']) && is_array($_POST['gifts'])) {
            foreach ($_POST['gifts'] as $gift_data) {

                $gift = new DCW_Rule_Gift();
                $gift->product_id = intval($gift_data['product_id'] ?? 0);
                $gift->quantity = intval($gift_data['quantity'] ?? 1);

                $rule->gifts[] = $gift;
            }
        }

        $this->rule_repo->create($rule);

        wp_redirect(admin_url('admin.php?page=' . DCW_PLUGIN_SLUG));
        exit;
    }

    public function delete_rule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $rule_id = isset($_POST['rule_id']) ? intval($_POST['rule_id']) : 0;

        if (!isset($_POST['dcw_nonce']) || !wp_verify_nonce($_POST['dcw_nonce'], 'dcw_delete_rule_' . $rule_id)) {
            wp_die('Security check failed');
        }

        if ($rule_id) {
            $this->rule_repo->delete($rule_id);
        }

        wp_redirect(admin_url('admin.php?page=' . DCW_PLUGIN_SLUG));
        exit;
    }

    public function update_rule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $rule_id = isset($_POST['rule_id']) ? intval($_POST['rule_id']) : 0;
        if (!isset($_POST['dcw_nonce']) || !wp_verify_nonce($_POST['dcw_nonce'], 'dcw_update_rule_' . $rule_id)) {
            wp_die('Security check failed');
        }

        $rule = new DCW_Rule();
        $rule->id = $rule_id;
        $rule->name = sanitize_text_field($_POST['rule_name'] ?? '');
        $rule->type = sanitize_text_field($_POST['discount_type'] ?? '');
        $rule->enabled = !empty($_POST['enabled']) ? 1 : 0;
        $rule->show_progress_card = !empty($_POST['show_progress_card']) ? 1 : 0;

        // --- CONDITIONS ---
        $rule->conditions = [];

        if (!empty($_POST['conditions']) && is_array($_POST['conditions'])) {
            foreach ($_POST['conditions'] as $cond) {

                $condition = new DCW_Rule_Condition();
                $condition->type = sanitize_text_field($cond['type'] ?? '');
                $condition->value = sanitize_text_field($cond['value'] ?? '');
                $condition->operator = sanitize_text_field($cond['operator'] ?? '>=');

                $rule->conditions[] = $condition;
            }
        }

        // --- DISCOUNTS ---
        $rule->discounts = [];

        if (!empty($_POST['discounts']) && is_array($_POST['discounts'])) {
            foreach ($_POST['discounts'] as $disc) {

                $discount = new DCW_Rule_Discount();
                $discount->type = sanitize_text_field($disc['type'] ?? '');
                $discount->value = floatval($disc['value'] ?? 0);

                $rule->discounts[] = $discount;
            }
        }

        // --- GIFTS ---
        $rule->gifts = [];

        if (!empty($_POST['gifts']) && is_array($_POST['gifts'])) {
            foreach ($_POST['gifts'] as $gift_data) {

                $gift = new DCW_Rule_Gift();
                $gift->product_id = intval($gift_data['product_id'] ?? 0);
                $gift->quantity = intval($gift_data['quantity'] ?? 1);

                $rule->gifts[] = $gift;
            }
        }

        $this->rule_repo->update($rule);

        $redirect = wp_get_referer();
        if (!$redirect) {
            $redirect = admin_url('admin.php?page=' . DCW_PLUGIN_SLUG);
        }
        wp_redirect($redirect);
        exit;
    }

    public function store_settings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!isset($_POST['dcw_nonce']) || !wp_verify_nonce($_POST['dcw_nonce'], 'dcw_save_settings')) {
            wp_die('Security check failed');
        }

        $settings = isset($_POST['dcw_settings']) ? (array)$_POST['dcw_settings'] : [];
        update_option('dcw_settings', $settings);

        wp_redirect(admin_url('admin.php?page=dcw-settings&updated=true'));
        exit;
    }

    public function toggle_rule()
    {

        if (!isset($_POST['_ajax_nonce']) ||
            !wp_verify_nonce($_POST['_ajax_nonce'], 'dcw_toggle_rule')) {
            wp_send_json_error('Invalid nonce');
        }

        $rule_id = intval($_POST['rule_id']);
        $enabled = intval($_POST['enabled']);

        $rule = $this->rule_repo->find($rule_id);

        if (!$rule) {
            wp_send_json_error('Rule not found');
        }

        $rule->enabled = $enabled;

        $this->rule_repo->update($rule);

        wp_send_json_success();
    }


    public function add_rule_page()
    {
        $data = [
            'title' => 'Add New Discount Rule'
        ];

        $this->render('rules/add', $data);
    }

    public function rules_page()
    {

        $rules = $this->rule_repo->get_all();

        $data = [
            'title' => 'Cart Discounts',
            'rules' => $rules
        ];

        $this->render('rules/index', $data);
    }

    public function edit_rule_page()
    {
        $rule_id = isset($_GET['rule_id']) ? intval($_GET['rule_id']) : 0;
        $rule = $this->rule_repo->find($rule_id);

        if (!$rule) {
            wp_die('Rule not found');
        }

        $data = [
            'title' => 'Edit Discount Rule',
            'rule' => $rule
        ];

        $this->render('rules/edit', $data);
    }

    public function settings_page()
    {

        $options = get_option('dcw_settings');

        $data = [
            'title' => 'Plugin Settings',
            'options' => $options
        ];

        $this->render('settings/index', $data);
    }

    /**
     * Render view
     */
    private function render($view, $data = [])
    {

        $view_file = DCW_PLUGIN_PATH . 'admin/templates/' . $view . '.php';

        if (file_exists($view_file)) {
            extract($data);
            include $view_file;
        }
    }
}