<?php
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register a custom Display Condition for the product_category taxonomy,
 * so editors can show/hide sections on specific product category archive pages.
 */
add_action('elementor/display_conditions/register', function ($conditions_manager) {
    if (! class_exists('XclearProductCategoryCondition')) {
        return;
    }

    $conditions_manager->register_condition_instance(new XclearProductCategoryCondition());
});

if (class_exists('\ElementorPro\Modules\DisplayConditions\Conditions\Base\Archive_Condition_Base')) {
    class XclearProductCategoryCondition extends \ElementorPro\Modules\DisplayConditions\Conditions\Base\Archive_Condition_Base
    {
        public function __construct()
        {
            parent::__construct('product_categories');
        }

        public function get_name(): string
        {
            return 'archive_of_product_categories';
        }

        public function get_label(): string
        {
            return esc_html__('Of Product Categories', 'xclear');
        }

        protected function get_taxonomy(): string
        {
            return 'product_category';
        }

        public function check($args): bool
        {
            return parent::check_is_of_taxonomy($args);
        }

        protected function is_of_taxonomy($args): bool
        {
            $ids = array_column($args['product_categories'] ?? [], 'id');
            if (empty($ids)) {
                return is_tax('product_category');
            }
            return is_tax('product_category', $ids);
        }
    }
}
