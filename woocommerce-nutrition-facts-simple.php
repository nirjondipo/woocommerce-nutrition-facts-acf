<?php
/**
 * Plugin Name: WooCommerce Nutrition Facts - Simple
 * Plugin URI: https://github.com/nirjondipo/woocommerce-nutrition-facts-acf/
 * Description: Simple nutrition facts fields for WooCommerce products using ACF Free
 * Version: 1.0.0
 * Author: Md Solaiman
 * Author URI: https://www.upwork.com/freelancers/~01da2982e531013221
 * Text Domain: wc-nutrition-simple
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WC_NUTRITION_SIMPLE_VERSION', '1.0.0');
define('WC_NUTRITION_SIMPLE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WC_NUTRITION_SIMPLE_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Simple Nutrition Facts ACF Class
 */
class WC_Nutrition_Facts_Simple {

    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('acf/init', array($this, 'add_nutrition_fields')); // Use acf/init as recommended
        add_shortcode('wc_nutrition_facts', array($this, 'nutrition_facts_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('body_class', array($this, 'add_nutrition_body_class'));
    }

    public function init() {
        // Check dependencies
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }

        if (!function_exists('acf_add_local_field_group')) {
            add_action('admin_notices', array($this, 'acf_missing_notice'));
            return;
        }
    }

    public function woocommerce_missing_notice() {
        echo '<div class="error"><p><strong>WooCommerce Nutrition Facts</strong> requires WooCommerce to be installed and active.</p></div>';
    }

    public function acf_missing_notice() {
        echo '<div class="error"><p><strong>WooCommerce Nutrition Facts</strong> requires Advanced Custom Fields (ACF) to be installed and active.</p></div>';
    }

    public function add_nutrition_fields() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'group_nutrition_facts_simple',
            'title' => 'Nutrition Facts',
            'fields' => array(
                // Basic Information
                array(
                    'key' => 'field_nutrition_heading',
                    'label' => 'Nutrition Table Heading',
                    'name' => 'nutrition_heading',
                    'type' => 'text',
                    'default_value' => 'Nutrition Facts',
                    'instructions' => 'Custom heading for the nutrition table',
                ),
                array(
                    'key' => 'field_serving_size',
                    'label' => 'Serving Size',
                    'name' => 'serving_size',
                    'type' => 'text',
                    'instructions' => 'Provide a serving size. E.g. 1 Cookie(20g), or 100g',
                ),
                array(
                    'key' => 'field_serving_per_container',
                    'label' => 'Serving Per Container',
                    'name' => 'serving_per_container',
                    'type' => 'text',
                    'instructions' => 'Provide serving per container. E.g. 30',
                ),
                array(
                    'key' => 'field_calories',
                    'label' => 'Calories per serving (cal)',
                    'name' => 'calories',
                    'type' => 'number',
                    'instructions' => 'Provide approximate calories (without unit) per serving. E.g. 240',
                    'min' => 0,
                    'step' => 1,
                ),

                // Macronutrients
                array(
                    'key' => 'field_total_fat',
                    'label' => 'Total Fat',
                    'name' => 'total_fat',
                    'type' => 'number',
                    'instructions' => 'The amount of Total Fat (g), without unit. Standard daily value is 78 g',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_saturated_fat',
                    'label' => 'Saturated Fat',
                    'name' => 'saturated_fat',
                    'type' => 'number',
                    'instructions' => 'The amount of Saturated Fat (g), without unit. Standard daily value is 20 g',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_trans_fat',
                    'label' => 'Trans Fat',
                    'name' => 'trans_fat',
                    'type' => 'number',
                    'instructions' => 'The amount of Trans Fat (g), without unit.',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_polyunsaturated_fat',
                    'label' => 'Polyunsaturated Fat',
                    'name' => 'polyunsaturated_fat',
                    'type' => 'number',
                    'instructions' => 'The amount of Polyunsaturated Fat (g), without unit.',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_monounsaturated_fat',
                    'label' => 'Monounsaturated Fat',
                    'name' => 'monounsaturated_fat',
                    'type' => 'number',
                    'instructions' => 'The amount of Monounsaturated Fat (g), without unit.',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_cholesterol',
                    'label' => 'Cholesterol',
                    'name' => 'cholesterol',
                    'type' => 'number',
                    'instructions' => 'The amount of Cholesterol (mg), without unit. Standard daily value is 300 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_sodium',
                    'label' => 'Sodium',
                    'name' => 'sodium',
                    'type' => 'number',
                    'instructions' => 'The amount of Sodium (mg), without unit. Standard daily value is 2300 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_carbohydrate',
                    'label' => 'Total Carbohydrate',
                    'name' => 'carbohydrate',
                    'type' => 'number',
                    'instructions' => 'The amount of Total Carbohydrate (g), without unit. Standard daily value is 275 g',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_fiber',
                    'label' => 'Dietary Fiber',
                    'name' => 'fiber',
                    'type' => 'number',
                    'instructions' => 'The amount of Dietary Fiber (g), without unit. Standard daily value is 28 g',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_sugar',
                    'label' => 'Total Sugars',
                    'name' => 'sugar',
                    'type' => 'number',
                    'instructions' => 'The amount of Total Sugars (g), without unit.',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_added_sugar',
                    'label' => 'Added Sugars',
                    'name' => 'added_sugar',
                    'type' => 'number',
                    'instructions' => 'The amount of Added Sugars (g), without unit. Standard daily value is 50 g',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_sugar_alcohol',
                    'label' => 'Sugar Alcohol',
                    'name' => 'sugar_alcohol',
                    'type' => 'number',
                    'instructions' => 'The amount of Sugar Alcohol (g), without unit.',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_protein',
                    'label' => 'Protein',
                    'name' => 'protein',
                    'type' => 'number',
                    'instructions' => 'The amount of Protein (g), without unit. Standard daily value is 50 g',
                    'min' => 0,
                    'step' => 1,
                ),

                // Vitamins
                array(
                    'key' => 'field_vitamin_d',
                    'label' => 'Vitamin D (Cholecalciferol)',
                    'name' => 'vitamin_d',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin D (Cholecalciferol) (IU), without unit. Standard daily value is 800 IU (International Units) or 20 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_vitamin_a',
                    'label' => 'Vitamin A',
                    'name' => 'vitamin_a',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin A (International Units), without unit. Standard daily value is 900 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_vitamin_c',
                    'label' => 'Vitamin C (Ascorbic Acid)',
                    'name' => 'vitamin_c',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin C (Ascorbic Acid) (mg), without unit. Standard daily value is 90 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_vitamin_e',
                    'label' => 'Vitamin E (Tocopherol)',
                    'name' => 'vitamin_e',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin E (Tocopherol) (IU), without unit. Standard daily value is 33 IU or 15 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_vitamin_k',
                    'label' => 'Vitamin K',
                    'name' => 'vitamin_k',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin K (mcg), without unit. Standard daily value is 120 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_vitamin_b1',
                    'label' => 'Vitamin B1 (Thiamin)',
                    'name' => 'vitamin_b1',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin B1 (Thiamin) (mg), without unit. Standard daily value is 1.2 mg',
                    'min' => 0,
                    'step' => 0.1,
                ),
                array(
                    'key' => 'field_vitamin_b2',
                    'label' => 'Vitamin B2 (Riboflavin)',
                    'name' => 'vitamin_b2',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin B2 (Riboflavin) (mg), without unit. Standard daily value is 1.3 mg',
                    'min' => 0,
                    'step' => 0.1,
                ),
                array(
                    'key' => 'field_vitamin_b3',
                    'label' => 'Vitamin B3 (Niacin)',
                    'name' => 'vitamin_b3',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin B3 (Niacin) (mg), without unit. Standard daily value is 16 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_vitamin_b6',
                    'label' => 'Vitamin B6 (Pyridoxine)',
                    'name' => 'vitamin_b6',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin B6 (Pyridoxine) (mg), without unit. Standard daily value is 1.7 mg',
                    'min' => 0,
                    'step' => 0.1,
                ),
                array(
                    'key' => 'field_folate',
                    'label' => 'Folate',
                    'name' => 'folate',
                    'type' => 'number',
                    'instructions' => 'The amount of Folate (mcg), without unit. Standard daily value is 400 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_vitamin_b12',
                    'label' => 'Vitamin B12 (Cobalamine)',
                    'name' => 'vitamin_b12',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin B12 (Cobalamine) (mcg), without unit. Standard daily value is 2.4 mcg',
                    'min' => 0,
                    'step' => 0.1,
                ),
                array(
                    'key' => 'field_biotin',
                    'label' => 'Biotin',
                    'name' => 'biotin',
                    'type' => 'number',
                    'instructions' => 'The amount of Biotin (mcg), without unit. Standard daily value is 30 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_vitamin_b5',
                    'label' => 'Vitamin B5 (Pantothenic acid)',
                    'name' => 'vitamin_b5',
                    'type' => 'number',
                    'instructions' => 'The amount of Vitamin B5 (Pantothenic acid) (mg), without unit. Standard daily value is 5 mg',
                    'min' => 0,
                    'step' => 0.1,
                ),
                array(
                    'key' => 'field_choline',
                    'label' => 'Choline',
                    'name' => 'choline',
                    'type' => 'number',
                    'instructions' => 'The amount of Choline (mg), without unit. Standard daily value is 550 mg',
                    'min' => 0,
                    'step' => 1,
                ),

                // Minerals
                array(
                    'key' => 'field_calcium',
                    'label' => 'Calcium',
                    'name' => 'calcium',
                    'type' => 'number',
                    'instructions' => 'The amount of Calcium (mg), without unit. Standard daily value is 1300 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_iron',
                    'label' => 'Iron',
                    'name' => 'iron',
                    'type' => 'number',
                    'instructions' => 'The amount of Iron (mg), without unit. Standard daily value is 18 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_potassium',
                    'label' => 'Potassium',
                    'name' => 'potassium',
                    'type' => 'number',
                    'instructions' => 'The amount of Potassium (mg), without unit. Standard daily value is 4700 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_phosphorus',
                    'label' => 'Phosphorus',
                    'name' => 'phosphorus',
                    'type' => 'number',
                    'instructions' => 'The amount of Phosphorus (mg), without unit. Standard daily value is 1250 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_iodine',
                    'label' => 'Iodine',
                    'name' => 'iodine',
                    'type' => 'number',
                    'instructions' => 'The amount of Iodine (mcg), without unit. Standard daily value is 150 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_magnesium',
                    'label' => 'Magnesium',
                    'name' => 'magnesium',
                    'type' => 'number',
                    'instructions' => 'The amount of Magnesium (mg), without unit. Standard daily value is 420 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_zinc',
                    'label' => 'Zinc',
                    'name' => 'zinc',
                    'type' => 'number',
                    'instructions' => 'The amount of Zinc (mg), without unit. Standard daily value is 11 mg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_selenium',
                    'label' => 'Selenium',
                    'name' => 'selenium',
                    'type' => 'number',
                    'instructions' => 'The amount of Selenium (mcg), without unit. Standard daily value is 55 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_copper',
                    'label' => 'Copper',
                    'name' => 'copper',
                    'type' => 'number',
                    'instructions' => 'The amount of Copper (mg), without unit. Standard daily value is 0.9 mg',
                    'min' => 0,
                    'step' => 0.1,
                ),
                array(
                    'key' => 'field_manganese',
                    'label' => 'Manganese',
                    'name' => 'manganese',
                    'type' => 'number',
                    'instructions' => 'The amount of Manganese (mg), without unit. Standard daily value is 2.3 mg',
                    'min' => 0,
                    'step' => 0.1,
                ),
                array(
                    'key' => 'field_chromium',
                    'label' => 'Chromium',
                    'name' => 'chromium',
                    'type' => 'number',
                    'instructions' => 'The amount of Chromium (mcg), without unit. Standard daily value is 35 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_molybdenum',
                    'label' => 'Molybdenum',
                    'name' => 'molybdenum',
                    'type' => 'number',
                    'instructions' => 'The amount of Molybdenum (mcg), without unit. Standard daily value is 45 mcg',
                    'min' => 0,
                    'step' => 1,
                ),
                array(
                    'key' => 'field_chloride',
                    'label' => 'Chloride',
                    'name' => 'chloride',
                    'type' => 'number',
                    'instructions' => 'The amount of Chloride (mg), without unit. Standard daily value is 2300 mg',
                    'min' => 0,
                    'step' => 1,
                ),

                // Display Options
                array(
                    'key' => 'field_show_daily_values',
                    'label' => 'Show standard Daily Values',
                    'name' => 'show_daily_values',
                    'type' => 'true_false',
                    'instructions' => 'Enabling this option will show standard Daily Values in the chart.',
                    'default_value' => 1,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_round_daily_values',
                    'label' => 'Round off Daily Values',
                    'name' => 'round_daily_values',
                    'type' => 'true_false',
                    'instructions' => 'Enabling this option will round off daily values to their nearest integer value.',
                    'default_value' => 1,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_extra_notes',
                    'label' => 'Nutrition label extra notes',
                    'name' => 'extra_notes',
                    'type' => 'textarea',
                    'instructions' => 'Provide extra notes for the Nutrition table. This will be displayed at the end of table.',
                    'default_value' => '* The % Daily Value (DV) tells you how much a nutrient in a serving of food contributes to a daily diet. 2,000 calories a day is used for general nutrition advice.',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ));
    }

    public function nutrition_facts_shortcode($atts) {
        $atts = shortcode_atts(array(
            'product_id' => '',
            'heading' => '',
            'show_daily_values' => '',
            'round_daily_values' => '',
        ), $atts);

        // Get product ID
        $product_id = '';
        
        if (!empty($atts['product_id'])) {
            $product_id = intval($atts['product_id']);
        } elseif (is_product()) {
            global $product;
            if ($product) {
                $product_id = $product->get_id();
            }
        } else {
            return '<p>No product specified for nutrition facts.</p>';
        }

        if (empty($product_id)) {
            return '<p>Invalid product ID for nutrition facts.</p>';
        }

        // Get nutrition data
        $nutrition_heading = get_field('nutrition_heading', $product_id) ?: 'Nutrition Facts';
        $serving_size = get_field('serving_size', $product_id);
        $serving_per_container = get_field('serving_per_container', $product_id);
        $calories = get_field('calories', $product_id);
        $show_daily_values = get_field('show_daily_values', $product_id);
        $round_daily_values = get_field('round_daily_values', $product_id);
        $extra_notes = get_field('extra_notes', $product_id);

        // Override with shortcode attributes if provided
        if (!empty($atts['heading'])) {
            $nutrition_heading = $atts['heading'];
        }
        if ($atts['show_daily_values'] !== '') {
            $show_daily_values = filter_var($atts['show_daily_values'], FILTER_VALIDATE_BOOLEAN);
        }
        if ($atts['round_daily_values'] !== '') {
            $round_daily_values = filter_var($atts['round_daily_values'], FILTER_VALIDATE_BOOLEAN);
        }

        // Get all nutrition fields
        $total_fat = get_field('total_fat', $product_id);
        $saturated_fat = get_field('saturated_fat', $product_id);
        $trans_fat = get_field('trans_fat', $product_id);
        $polyunsaturated_fat = get_field('polyunsaturated_fat', $product_id);
        $monounsaturated_fat = get_field('monounsaturated_fat', $product_id);
        $cholesterol = get_field('cholesterol', $product_id);
        $sodium = get_field('sodium', $product_id);
        $carbohydrate = get_field('carbohydrate', $product_id);
        $fiber = get_field('fiber', $product_id);
        $sugar = get_field('sugar', $product_id);
        $added_sugar = get_field('added_sugar', $product_id);
        $sugar_alcohol = get_field('sugar_alcohol', $product_id);
        $protein = get_field('protein', $product_id);
        $vitamin_d = get_field('vitamin_d', $product_id);
        $vitamin_a = get_field('vitamin_a', $product_id);
        $vitamin_c = get_field('vitamin_c', $product_id);
        $vitamin_e = get_field('vitamin_e', $product_id);
        $vitamin_k = get_field('vitamin_k', $product_id);
        $vitamin_b1 = get_field('vitamin_b1', $product_id);
        $vitamin_b2 = get_field('vitamin_b2', $product_id);
        $vitamin_b3 = get_field('vitamin_b3', $product_id);
        $vitamin_b6 = get_field('vitamin_b6', $product_id);
        $folate = get_field('folate', $product_id);
        $vitamin_b12 = get_field('vitamin_b12', $product_id);
        $biotin = get_field('biotin', $product_id);
        $vitamin_b5 = get_field('vitamin_b5', $product_id);
        $choline = get_field('choline', $product_id);
        $calcium = get_field('calcium', $product_id);
        $iron = get_field('iron', $product_id);
        $potassium = get_field('potassium', $product_id);
        $phosphorus = get_field('phosphorus', $product_id);
        $iodine = get_field('iodine', $product_id);
        $magnesium = get_field('magnesium', $product_id);
        $zinc = get_field('zinc', $product_id);
        $selenium = get_field('selenium', $product_id);
        $copper = get_field('copper', $product_id);
        $manganese = get_field('manganese', $product_id);
        $chromium = get_field('chromium', $product_id);
        $molybdenum = get_field('molybdenum', $product_id);
        $chloride = get_field('chloride', $product_id);

        // Check if we have any nutrition data (including 0 values)
        $has_nutrition_data = ($serving_size !== '' && $serving_size !== null) || 
                             ($calories !== '' && $calories !== null) || 
                             ($total_fat !== '' && $total_fat !== null) || 
                             ($sodium !== '' && $sodium !== null) || 
                             ($carbohydrate !== '' && $carbohydrate !== null) || 
                             ($protein !== '' && $protein !== null);

        if (!$has_nutrition_data) {
            return ''; // Return empty string instead of message
        }

        // Standard Daily Values (same as original plugin)
        $standard_daily_values = array(
            'total_fat' => 78,
            'saturated_fat' => 20,
            'cholesterol' => 300,
            'sodium' => 2300,
            'carbohydrate' => 275,
            'fiber' => 28,
            'added_sugar' => 50,
            'protein' => 50,
            'vitamin_d' => 20,
            'calcium' => 1300,
            'iron' => 18,
            'potassium' => 4700,
            'vitamin_a' => 900,
            'vitamin_c' => 90,
            'vitamin_e' => 15,
            'vitamin_k' => 120,
            'vitamin_b1' => 1.2,
            'vitamin_b2' => 1.3,
            'vitamin_b3' => 16,
            'vitamin_b6' => 1.7,
            'folate' => 400,
            'vitamin_b12' => 2.4,
            'biotin' => 30,
            'vitamin_b5' => 5,
            'choline' => 550,
            'phosphorus' => 1250,
            'iodine' => 150,
            'magnesium' => 420,
            'zinc' => 11,
            'selenium' => 55,
            'copper' => 0.9,
            'manganese' => 2.3,
            'chromium' => 35,
            'molybdenum' => 45,
            'chloride' => 2300,
        );

        // Calculate daily value percentage will be done inline

        // Start output
        ob_start();
        ?>
        <div class="nflc std nutrition-section">
            <ul class="nutrition-table" itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation">
                <li class="nt-header b-0">
                    <h2 class="nt-title"><?php echo esc_html($nutrition_heading); ?></h2>
                </li>
                
                <?php if ($serving_per_container !== '' && $serving_per_container !== null): ?>
                    <li class="nt-row b-0 serving-per-cont">
                        <span class="nt-label col-100">
                            <?php printf(esc_html__('%s servings per container', 'wc-nutrition-simple'), esc_html($serving_per_container)); ?>
                        </span>
                    </li>
                <?php endif; ?>
                
                <?php if ($serving_size !== '' && $serving_size !== null): ?>
                    <li class="nt-row sep-10 serving-size">
                        <span class="nt-label col-50"><?php esc_html_e('Serving Size', 'wc-nutrition-simple'); ?></span>
                        <span class="nt-value col-50" itemprop="servingSize"><?php echo esc_html($serving_size); ?></span>
                    </li>
                <?php endif; ?>
                
                <li class="nt-row b-0 font-bold amount-per-serving">
                    <span class="nt-label col-100"><?php esc_html_e('Amount per serving', 'wc-nutrition-simple'); ?></span>
                </li>
                
                <?php if ($calories !== '' && $calories !== null): ?>
                    <li class="nt-row font-bold calories sep-4">
                        <span class="nt-label col-<?php echo $show_daily_values ? '80' : '70'; ?>"><?php esc_html_e('Calories', 'wc-nutrition-simple'); ?></span>
                        <span class="nt-value col-<?php echo $show_daily_values ? '20' : '30'; ?>"><?php echo esc_html($calories); ?></span>
                        <meta itemprop="calories" content="<?php echo esc_attr($calories); ?>">
                    </li>
                <?php endif; ?>
                
                <?php if ($show_daily_values): ?>
                    <li class="nt-row nt-head font-bold sep-1">
                        <span class="nt-label nutrient-label col-40"></span>
                        <span class="nt-label amount-label col-20"></span>
                        <span class="nt-label sdv-label col-20"><?php esc_html_e('Standard DV', 'wc-nutrition-simple'); ?></span>
                        <span class="pdv-label col-20"><?php esc_html_e('% Daily Value*', 'wc-nutrition-simple'); ?></span>
                    </li>
                <?php else: ?>
                    <li class="nt-head font-bold sep-1">
                        <span class="nt-label nutrient-label col-40"></span>
                        <span class="nt-label amount-label col-30"></span>
                        <span class="pdv-label col-30"><?php esc_html_e('% Daily Value*', 'wc-nutrition-simple'); ?></span>
                    </li>
                <?php endif; ?>
                
                <?php
                // Create nutrition facts array exactly like the original plugin
                $nutrition_facts = array(
                    array(
                        'id' => 'total_fat',
                        'label' => __('Total Fat', 'wc-nutrition-simple'),
                        'schema' => 'fatContent',
                        'liclass' => false,
                        'labelclass' => 'font-bold',
                        'sv' => 78,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'saturated_fat',
                        'label' => __('Saturated Fat', 'wc-nutrition-simple'),
                        'schema' => 'saturatedFatContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => false,
                        'sv' => 20,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'trans_fat',
                        'label' => __('Trans Fat', 'wc-nutrition-simple'),
                        'schema' => 'transFatContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => false,
                        'sv' => false,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'polyunsaturated_fat',
                        'label' => __('Polyunsaturated Fat', 'wc-nutrition-simple'),
                        'schema' => 'unsaturatedFatContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => false,
                        'sv' => false,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'monounsaturated_fat',
                        'label' => __('Monounsaturated Fat', 'wc-nutrition-simple'),
                        'schema' => 'unsaturatedFatContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => false,
                        'sv' => false,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'cholesterol',
                        'label' => __('Cholesterol', 'wc-nutrition-simple'),
                        'schema' => 'cholesterolContent',
                        'liclass' => '',
                        'labelclass' => 'font-bold',
                        'sv' => 300,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'sodium',
                        'label' => __('Sodium', 'wc-nutrition-simple'),
                        'schema' => 'sodiumContent',
                        'liclass' => '',
                        'labelclass' => 'font-bold',
                        'sv' => 2300,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'carbohydrate',
                        'label' => __('Total Carbohydrate', 'wc-nutrition-simple'),
                        'schema' => 'carbohydrateContent',
                        'liclass' => '',
                        'labelclass' => 'font-bold',
                        'sv' => 275,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'fiber',
                        'label' => __('Dietary Fiber', 'wc-nutrition-simple'),
                        'schema' => 'fiberContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => '',
                        'sv' => 28,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'sugar',
                        'label' => __('Total Sugars', 'wc-nutrition-simple'),
                        'schema' => 'sugarContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => '',
                        'sv' => false,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'added_sugar',
                        'label' => __('Added Sugars', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => 'nt-sublevel-2',
                        'labelclass' => '',
                        'sv' => 50,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'sugar_alcohol',
                        'label' => __('Sugar Alcohol', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => '',
                        'sv' => false,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'protein',
                        'label' => __('Protein', 'wc-nutrition-simple'),
                        'schema' => 'proteinContent',
                        'liclass' => 'nt-sep sep-8',
                        'labelclass' => 'font-bold',
                        'sv' => 50,
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'vitamin_d',
                        'label' => __('Vitamin D (Cholecalciferol)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 20,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'calcium',
                        'label' => __('Calcium', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 1300,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'iron',
                        'label' => __('Iron', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 18,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'potassium',
                        'label' => __('Potassium', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 4700,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_a',
                        'label' => __('Vitamin A', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 900,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'vitamin_c',
                        'label' => __('Vitamin C (Ascorbic Acid)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 90,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_e',
                        'label' => __('Vitamin E (Tocopherol)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 15,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_k',
                        'label' => __('Vitamin K', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 120,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'vitamin_b1',
                        'label' => __('Vitamin B1 (Thiamin)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 1.2,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_b2',
                        'label' => __('Vitamin B2 (Riboflavin)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 1.3,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_b3',
                        'label' => __('Vitamin B3 (Niacin)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 16,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_b6',
                        'label' => __('Vitamin B6 (Pyridoxine)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 1.7,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'folate',
                        'label' => __('Folate', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 400,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'vitamin_b12',
                        'label' => __('Vitamin B12 (Cobalamine)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 2.4,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'biotin',
                        'label' => __('Biotin', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 30,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'vitamin_b5',
                        'label' => __('Vitamin B5 (Pantothenic acid)', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 5,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'choline',
                        'label' => __('Choline', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 550,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'phosphorus',
                        'label' => __('Phosphorus', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 1250,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'iodine',
                        'label' => __('Iodine', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 150,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'magnesium',
                        'label' => __('Magnesium', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 420,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'zinc',
                        'label' => __('Zinc', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 11,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'selenium',
                        'label' => __('Selenium', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 55,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'copper',
                        'label' => __('Copper', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 0.9,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'manganese',
                        'label' => __('Manganese', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 2.3,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'chromium',
                        'label' => __('Chromium', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 35,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'molybdenum',
                        'label' => __('Molybdenum', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 45,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'chloride',
                        'label' => __('Chloride', 'wc-nutrition-simple'),
                        'schema' => false,
                        'liclass' => false,
                        'labelclass' => false,
                        'sv' => 2300,
                        'unit' => 'mg'
                    )
                );

                // Render nutrition facts exactly like the original plugin
                foreach($nutrition_facts as $nf) {
                    $field_value = get_field($nf['id'], $product_id);
                    if ($field_value !== '' && $field_value !== null) {
                        $offset = $round_daily_values ? 0 : 2;
                        $dv = !empty($nf['sv']) ? round((float)$field_value * 100 / $nf['sv'], $offset) : '';
                        
                        if ($show_daily_values) {
                            $format = '<li%1$s><span class="nt-label col-40%2$s">%3$s</span><span class="nt-amount col-20"%4$s>%5$s</span>%6$s%7$s</li>';
                        } else {
                            $format = '<li%1$s><span class="nt-label col-40%2$s">%3$s</span><span class="nt-amount col-30"%4$s>%5$s</span>%7$s</li>';
                        }

                        printf($format,
                            $nf['liclass'] ? ' class="' . esc_attr($nf['liclass']) . '"' : '',
                            $nf['labelclass'] ? ' ' . esc_attr($nf['labelclass']) : '',
                            esc_attr($nf['label']),
                            $nf['schema'] ? ' itemprop="' . esc_attr($nf['schema']) . '"' : '',
                            $field_value . ' ' . $nf['unit'],
                            !empty($nf['sv']) ? sprintf('<span class="nt-sdv col-20">%s</span>', $nf['sv'] . ' ' . $nf['unit']) : '',
                            !empty($nf['sv']) ? sprintf('<span class="nt-value col-%s">%s</span>',
                                $show_daily_values ? '20' : '30',
                                (int)$dv <= 100 ? $dv . '%' : '<b>' . $dv . '%</b>'
                            ) : ''
                        );
                    }
                }
                ?>
                
                <?php if (!empty($extra_notes)): ?>
                    <li class="nt-footer b-0"><?php echo wp_kses_post($extra_notes); ?></li>
                <?php endif; ?>
            </ul><!-- /.nutrition-table -->
        </div><!-- /.nutrition-section -->
        <?php
        
        return ob_get_clean();
    }

    public function add_nutrition_body_class($classes) {
        // Only add classes on single product pages
        if (is_product()) {
            global $post;
            
            if ($post && $post->post_type === 'product') {
                $product_id = $post->ID;
                
                // Check if product has nutrition data
                $has_nutrition = $this->check_nutrition_data($product_id);
                
                if ($has_nutrition) {
                    $classes[] = 'has-nutrition-facts';
                    $classes[] = 'nutrition-data-available';
                } else {
                    $classes[] = 'no-nutrition-facts';
                    $classes[] = 'nutrition-data-unavailable';
                }
                
                // Add specific nutrition data classes
                $nutrition_fields = array(
                    'calories', 'total_fat', 'saturated_fat', 'cholesterol', 'sodium',
                    'carbohydrate', 'fiber', 'sugar', 'protein', 'vitamin_d', 'calcium', 'iron'
                );
                
                foreach ($nutrition_fields as $field) {
                    $value = get_field($field, $product_id);
                    if ($value !== '' && $value !== null) {
                        $classes[] = 'has-' . str_replace('_', '-', $field);
                    }
                }
            }
        }
        
        return $classes;
    }
    
    private function check_nutrition_data($product_id) {
        // Check for basic nutrition data
        $basic_fields = array('calories', 'total_fat', 'sodium', 'carbohydrate', 'protein');
        
        foreach ($basic_fields as $field) {
            $value = get_field($field, $product_id);
            if ($value !== '' && $value !== null) {
                return true;
            }
        }
        
        return false;
    }

    private function calculate_daily_value($amount, $standard_value, $round = false) {
        if (empty($amount) || empty($standard_value) || $standard_value == 0) {
            return '';
        }
        $percentage = ($amount / $standard_value) * 100;
        return $round ? round($percentage) : round($percentage, 2);
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'wc-nutrition-simple-style',
            WC_NUTRITION_SIMPLE_PLUGIN_URL . 'assets/css/nutrition-facts.css',
            array(),
            WC_NUTRITION_SIMPLE_VERSION
        );
    }
}

// Initialize the plugin
new WC_Nutrition_Facts_Simple();
