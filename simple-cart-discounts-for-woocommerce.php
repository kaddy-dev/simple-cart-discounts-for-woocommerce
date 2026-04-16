<?php
/**
 * Plugin Name: Simple Cart Discounts for WooCommerce
 * Description: Build simple or advanced discount rules for your WooCommerce store — without writing code or using complicated tools.
 * Author: Kadyk Dmytro
 * Version: 1.0.5
 * Text Domain: simple-cart-discounts-for-woocommerce
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('DCW_VERSION')) {
    define('DCW_VERSION', '1.0.5');
}

/**
 * Required PHP Version
 */
if (!defined('DCW_REQUIRED_PHP_VERSION')) {
    define('DCW_REQUIRED_PHP_VERSION', 7.4);
}

/**
 * Required Woocommerce Version
 */
if (!defined('DCW_WC_REQUIRED_VERSION')) {
    define('DCW_WC_REQUIRED_VERSION', '3.0.0');
}

if (!defined('DCW_PLUGIN_FILE')) {
    define('DCW_PLUGIN_FILE', __FILE__);
}

/**
 * The plugin path
 */
if (!defined('DCW_PLUGIN_PATH')) {
    define('DCW_PLUGIN_PATH', plugin_dir_path(DCW_PLUGIN_FILE));
}

/**
 * The plugin slug
 */
if (!defined('DCW_PLUGIN_SLUG')) {
    define('DCW_PLUGIN_SLUG', 'discounts-cart');
}

define('DCW_PLUGIN_URL', plugin_dir_url(__FILE__));


/** Check requirements */

if (version_compare(PHP_VERSION, DCW_REQUIRED_PHP_VERSION, '<')) {
    add_action('admin_notices', function () {
        printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            esc_html('Simple Cart Discounts requires PHP 7.4 or higher.')
        );
    });
    return;
}

/** ----------------------------- */

add_action('init', function () {
    load_plugin_textdomain(
        'discounts-cart',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
});

register_activation_hook(DCW_PLUGIN_FILE, function () {
    $file = DCW_PLUGIN_PATH . 'core/class-migrations.php';

    if (file_exists($file)) {
        require_once $file;
        (new DCW_Migrations())->migrate();
    }
});

// Models
require_once DCW_PLUGIN_PATH . 'core/models/DCW_Rule.php';
require_once DCW_PLUGIN_PATH . 'core/models/DCW_Rule_Condition.php';
require_once DCW_PLUGIN_PATH . 'core/models/DCW_Rule_Discount.php';
require_once DCW_PLUGIN_PATH . 'core/models/DCW_Rule_Gift.php';

// Repositories
require_once DCW_PLUGIN_PATH . 'core/repositories/DCW_Rule_Repository.php';
require_once DCW_PLUGIN_PATH . 'core/repositories/DCW_Rule_Condition_Repository.php';
require_once DCW_PLUGIN_PATH . 'core/repositories/DCW_Rule_Discount_Repository.php';
require_once DCW_PLUGIN_PATH . 'core/repositories/DCW_Rule_Gift_Repository.php';

// Services
require_once DCW_PLUGIN_PATH . 'core/services/discounts/interface.php';
require_once DCW_PLUGIN_PATH . 'core/services/discounts/cart-discount.php';
require_once DCW_PLUGIN_PATH . 'core/services/discounts/free-shipping.php';
require_once DCW_PLUGIN_PATH . 'core/services/discounts/free-gift.php';
require_once DCW_PLUGIN_PATH . 'core/services/condition-validator.php';
require_once DCW_PLUGIN_PATH . 'core/services/cart-manager.php';

require_once DCW_PLUGIN_PATH . 'site/site-manager.php';

if (is_admin()) {
    require_once DCW_PLUGIN_PATH . 'admin/shortcode.php';
    require_once DCW_PLUGIN_PATH . 'admin/admin.php';
} else {
    $rule_repo = new DCW_Rule_Repository();
    $condition_validator_service = new DCW_Condition_Validator();

    new DCW_Cart_Manager($rule_repo, $condition_validator_service);
    $manager = new DCW_Site_Manager($rule_repo, $condition_validator_service);

    add_shortcode('dcw_progress_discount', function () use ($manager) {
        ob_start();
        $manager->render_progress_card();
        return ob_get_clean();
    });
}


