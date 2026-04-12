<?php
if (!defined('ABSPATH')) exit;

class DCW_Rule {
    public $id;
    public $name;
    public $type;
    public $enabled;
    public $show_progress_card;

    /** @var DCW_Rule_Condition[] */
    public $conditions = [];

    /** @var DCW_Rule_Discount[] */
    public $discounts = [];

    /** @var DCW_Rule_Gift[] */
    public $gifts = [];
}