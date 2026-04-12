<?php
if (!defined('ABSPATH')) exit;

class DCW_Site_Manager
{

    private $rule_repository;
    private $condition_validator;

    public $settings;

    private $k_managed_free_shipping = false;

    public function __construct(DCW_Rule_Repository     $repository,
                                DCW_Condition_Validator $validator)
    {
        $this->rule_repository = $repository;
        $this->condition_validator = $validator;

        $this->settings = get_option('dcw_settings', [
            'calculate_discount_by' => 'sale_price',
            'apply_cart_discount_as' => 'fee'
        ]);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 10);

        add_action('woocommerce_checkout_create_order_line_item', [$this, 'mark_gift_in_checkout'], 10, 4);
        add_action('woocommerce_before_calculate_totals', [$this, 'calculate_totals'], 999);
        add_action('woocommerce_cart_item_quantity', [$this, 'manage_gift_quantity'], 10, 3);
        // add_action('woocommerce_get_item_data', [$this, 'manage_gift_item_data'], 10, 2);

        add_action('woocommerce_review_order_after_order_total', [$this, 'render_progress_card'], 11);
        add_action('dcw_render_progress_card', [$this, 'render_progress_card'], 20);

        add_action('init', [$this, 'register_block_progress_card']);

    }

    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'dcw-site',
            plugins_url('site/assets/script.js', DCW_PLUGIN_FILE),
            ['jquery'],
            DCW_VERSION,
            true
        );

        wp_enqueue_style(
            'dcw-site-style',
            plugins_url('site/assets/style.css', DCW_PLUGIN_FILE),
            null,
            DCW_VERSION
        );
    }

    public function mark_gift_in_checkout($item, $cart_item_key, $values, $order)
    {
        if (!empty($values['dcw_gift'])) {
            $item->add_meta_data('dcw_gift', true, true);
        }

        if (!empty($values['rule_id'])) {
            $item->add_meta_data('rule_id', $values['rule_id'], true);
        }
    }

    public function manage_gift_price($cart)
    {
        if (is_admin() && !defined('DOING_AJAX')) return;

        foreach ($cart->get_cart() as $cart_item) {
            if (!empty($cart_item['dcw_gift'])) {

                /**
                 * Change gift price
                 *
                 * @param float $price Current gift price (default 0)
                 * @param array $cart_item Cart item
                 */
                $price = apply_filters('dcw_gift_price', 0, $cart_item);

                $cart_item['data']->set_price($price);
            }
        }
    }

    public function manage_gift_quantity($product_quantity, $cart_item_key, $cart_item)
    {

        if (!empty($cart_item['dcw_gift'])) {
            return 1;
        }

        return $product_quantity;
    }

    public function manage_gift_item_data($item_data, $cart_item)
    {
        if (!empty($cart_item['dcw_gift'])) {
            $gift_data = [
                'name' => __('Gift', 'discounts-cart'),
                'value' => __('Free product', 'discounts-cart'),
            ];

            /**
             * Change data gift in cart
             *
             * @param array $gift_data Name and Value gift data
             * @param array $cart_item Cart item
             */
            $gift_data = apply_filters('dcw_gift_item_data', $gift_data, $cart_item);

            $item_data[] = $gift_data;
        }

        return $item_data;
    }

    public function calculate_totals($cart) {
        $this->manage_sort_gift($cart);
        $this->manage_gift_price($cart);
    }

    public function manage_sort_gift($cart)
    {
        if (is_admin() && !defined('DOING_AJAX')) return;

        $cart_contents = $cart->get_cart();

        $regular = [];
        $gifts = [];

        foreach ($cart_contents as $key => $item) {
            if (!empty($item['dcw_gift'])) {
                $gifts[$key] = $item;
            } else {
                $regular[$key] = $item;
            }
        }

        $sorted = $regular + $gifts;

        $cart->cart_contents = $sorted;
    }

    public function render_progress_card()
    {

        $rules = $this->rule_repository->get_enabled();

        $cart = WC()->cart;

        if (!$cart) return;

        foreach ($rules as $rule) {

            if (empty($rule->enabled)) continue;

            if (!$rule->show_progress_card) continue;

//            $condition_validated = $this->condition_validator->validate($rule->conditions, $cart);
            $cart_total_need = 0;
            foreach ($rule->conditions as $condition) {
                if ($condition->type == 'cart_total') {
                    $cart_total_need = $condition->value;
                }
            }

            if (!$cart_total_need) {
                continue;
            }

            $cart_total = $cart->get_cart_contents_total();

            $this->view_progress_card($rule, $cart_total_need, $cart_total);
        }

    }

    public function view_progress_card($rule, $cart_total_need, $cart_total)
    {
        $view = 'discount-card';

        $view_file = DCW_PLUGIN_PATH . 'site/templates/' . $view . '.php';

        if (file_exists($view_file)) {
            extract(array(
                'rule' => $rule,
                'cart_total_need' => $cart_total_need,
                'current_amount' => $cart_total,
            ));
            include $view_file;
        }
    }

    public function block_render_progress_card()
    {
        ob_start();
        $this->render_progress_card();
        return ob_get_clean();
    }

    public function register_block_progress_card()
    {
        register_block_type('discounts-cart/progress-card', [
            'render_callback' => [$this, 'block_render_progress_card'],
        ]);
    }

}