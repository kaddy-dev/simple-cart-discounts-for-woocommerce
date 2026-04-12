<?php
if (!defined('ABSPATH')) exit;

if (is_admin()) {
    if (function_exists('register_block_type')) {
        add_action('enqueue_block_editor_assets', function () {
            wp_enqueue_script(
                'dcw-progress-card-block',
                plugins_url('admin/assets/progress-card-block.js', DCW_PLUGIN_FILE),
                ['wp-blocks', 'wp-element', 'wp-editor'],
                DCW_VERSION
            );
        });
    }
}

