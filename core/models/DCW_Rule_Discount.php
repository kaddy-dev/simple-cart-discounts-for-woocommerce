<?php
if (!defined('ABSPATH')) exit;


class DCW_Rule_Discount {
    public $id;
    public $rule_id;
    public $type; // fixed, percent, ...
    public $value;
    public $extra_data;
}