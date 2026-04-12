<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once DCW_PLUGIN_PATH . 'admin/controller.php';
require_once DCW_PLUGIN_PATH . 'admin/menu.php';

add_action('admin_enqueue_scripts', function ($hook) {
    if (strpos($hook, DCW_PLUGIN_SLUG) === false && strpos($hook, 'dcw') === false) return;

    wp_enqueue_script('wc-enhanced-select');
    wp_enqueue_script('wc-product-search');

    wp_enqueue_script(
        'dcw-admin',
        plugins_url('admin/assets/script.js', DCW_PLUGIN_FILE),
        ['jquery'],
        DCW_VERSION,
        true
    );


    wp_enqueue_style('woocommerce_admin_styles');

    wp_enqueue_style(
        'dcw-admin-style',
        plugins_url('admin/assets/style.css', DCW_PLUGIN_FILE),
        null,
        DCW_VERSION
    );


    wp_localize_script('dcw-admin', 'dcw_ajax', [
        'nonce' => wp_create_nonce('dcw_toggle_rule')
    ]);
});


/**
 * Init admin area
 */
function dcw_admin_init()
{

    $controller = new DCW_Admin_Controller();
    new DCW_Admin_Menu($controller);

}

dcw_admin_init();