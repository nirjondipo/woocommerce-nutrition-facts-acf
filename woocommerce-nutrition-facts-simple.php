<?php
/**
 * Plugin Name: WooCommerce Nutrition Facts
 * Plugin URI: https://github.com/your-username/woocommerce-nutrition-facts-acf
 * Description: Add nutrition facts labels to WooCommerce products using ACF fields. Automatically parses nutrition data from a single field and populates individual fields.
 * Version: 1.0.0
 * Author: Md Solaiman
 * Author URI: https://www.upwork.com/freelancers/~01da2982e531013221
 * Text Domain: wc-nutrition-simple
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

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
        add_shortcode('wc_nutrition_facts', array($this, 'nutrition_facts_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('body_class', array($this, 'add_nutrition_body_class'));
        
        // Add hook to parse new_nutrition_info field
        add_action('acf/save_post', array($this, 'parse_nutrition_info_field'), 20);
        
        // Lazy parsing for products updated via Air WP Sync or other external methods
        add_action('template_redirect', array($this, 'maybe_parse_on_product_view'));
        
        // Declare WooCommerce HPOS compatibility
        add_action('before_woocommerce_init', array($this, 'declare_woocommerce_compatibility'));
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

    /**
     * Declare WooCommerce HPOS compatibility
     */
    public function declare_woocommerce_compatibility() {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        }
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
        } elseif (is_singular('product')) {
            global $post;
            if ($post) {
                $product_id = $post->ID;
            }
        } else {
            // Try to get product ID from global $post if available
            global $post;
            if ($post && get_post_type($post->ID) === 'product') {
                $product_id = $post->ID;
            } else {
                return ''; // Return empty string instead of error message
            }
        }

        if (empty($product_id)) {
            return ''; // Return empty string if no valid product ID
        }

        // Check and parse nutrition data if needed (lazy parsing for Air WP Sync)
        $this->maybe_parse_nutrition_data($product_id);

        // Get nutrition data from post_meta (parsed data)
        $nutrition_data = get_post_meta($product_id, '_nutrition_parsed_data', true);
        
        if (empty($nutrition_data)) {
            return ''; // Return empty string if no nutrition data
        }

        // Get display options
        $nutrition_heading = !empty($atts['heading']) ? $atts['heading'] : 'Nutrition Facts';
        $show_daily_values = $atts['show_daily_values'] !== '' ? filter_var($atts['show_daily_values'], FILTER_VALIDATE_BOOLEAN) : true;
        $round_daily_values = $atts['round_daily_values'] !== '' ? filter_var($atts['round_daily_values'], FILTER_VALIDATE_BOOLEAN) : true;

        // Standard Daily Values
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
        );

        // Start output
        ob_start();
        ?>
        <div class="nflc std nutrition-section">
            <ul class="nutrition-table" itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation">
                <li class="nt-header b-0">
                    <h2 class="nt-title"><?php echo esc_html($nutrition_heading); ?></h2>
                </li>
                
                <?php if (!empty($nutrition_data['serving_per_container'])): ?>
                    <li class="nt-row b-0 serving-per-cont">
                        <span class="nt-label col-100">
                            <?php printf(esc_html__('%s servings per container', 'wc-nutrition-simple'), esc_html($nutrition_data['serving_per_container'])); ?>
                        </span>
                    </li>
                <?php endif; ?>
                
                <?php if (!empty($nutrition_data['serving_size'])): ?>
                    <li class="nt-row sep-10 serving-size">
                        <span class="nt-label col-50"><?php esc_html_e('Serving Size', 'wc-nutrition-simple'); ?></span>
                        <span class="nt-value col-50" itemprop="servingSize"><?php echo esc_html($nutrition_data['serving_size']); ?></span>
                    </li>
                <?php endif; ?>
                
                <?php if (!empty($nutrition_data['serving_size'])): ?>
                    <li class="nt-row b-0 font-bold amount-per-serving sep-1">
                        <span class="nt-label col-100"><?php esc_html_e('Amount per serving', 'wc-nutrition-simple'); ?></span>
                    </li>
                <?php endif; ?>
                
                <?php if (!empty($nutrition_data['calories'])): ?>
                    <li class="nt-row font-bold calories sep-4">
                        <span class="nt-label col-<?php echo $show_daily_values ? '80' : '70'; ?>"><?php esc_html_e('Calories', 'wc-nutrition-simple'); ?></span>
                        <span class="nt-value col-<?php echo $show_daily_values ? '20' : '30'; ?>"><?php echo esc_html($nutrition_data['calories']); ?></span>
                        <meta itemprop="calories" content="<?php echo esc_attr($nutrition_data['calories']); ?>">
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
                // Create nutrition facts array
                $nutrition_facts = array(
                    array(
                        'id' => 'total_fat',
                        'label' => __('Total Fat', 'wc-nutrition-simple'),
                        'schema' => 'fatContent',
                        'liclass' => false,
                        'labelclass' => 'font-bold',
                        'sv' => $standard_daily_values['total_fat'],
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'saturated_fat',
                        'label' => __('Saturated Fat', 'wc-nutrition-simple'),
                        'schema' => 'saturatedFatContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => '',
                        'sv' => $standard_daily_values['saturated_fat'],
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'trans_fat',
                        'label' => __('Trans Fat', 'wc-nutrition-simple'),
                        'schema' => 'transFatContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => '',
                        'sv' => '',
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'cholesterol',
                        'label' => __('Cholesterol', 'wc-nutrition-simple'),
                        'schema' => 'cholesterolContent',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => $standard_daily_values['cholesterol'],
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'sodium',
                        'label' => __('Sodium', 'wc-nutrition-simple'),
                        'schema' => 'sodiumContent',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => $standard_daily_values['sodium'],
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'carbohydrate',
                        'label' => __('Total Carbohydrate', 'wc-nutrition-simple'),
                        'schema' => 'carbohydrateContent',
                        'liclass' => false,
                        'labelclass' => 'font-bold',
                        'sv' => $standard_daily_values['carbohydrate'],
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'fiber',
                        'label' => __('Dietary Fiber', 'wc-nutrition-simple'),
                        'schema' => 'fiberContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => '',
                        'sv' => $standard_daily_values['fiber'],
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'sugar',
                        'label' => __('Total Sugars', 'wc-nutrition-simple'),
                        'schema' => 'sugarContent',
                        'liclass' => 'nt-sublevel-1',
                        'labelclass' => '',
                        'sv' => '',
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'added_sugars',
                        'label' => __('Added Sugars', 'wc-nutrition-simple'),
                        'schema' => 'sugarContent',
                        'liclass' => 'nt-sublevel-2',
                        'labelclass' => '',
                        'sv' => $standard_daily_values['added_sugar'],
                        'unit' => 'g'
                    ),
                    array(
                        'id' => 'protein',
                        'label' => __('Protein', 'wc-nutrition-simple'),
                        'schema' => 'proteinContent',
                        'liclass' => false,
                        'labelclass' => 'font-bold',
                        'sv' => $standard_daily_values['protein'],
                        'unit' => 'g'
                    ),
                    // Vitamins
                    array(
                        'id' => 'vitamin_d',
                        'label' => __('Vitamin D (Cholecalciferol)', 'wc-nutrition-simple'),
                        'schema' => 'vitaminD',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => $standard_daily_values['vitamin_d'],
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'vitamin_a',
                        'label' => __('Vitamin A', 'wc-nutrition-simple'),
                        'schema' => 'vitaminA',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 900,
                        'unit' => 'IU'
                    ),
                    array(
                        'id' => 'vitamin_c',
                        'label' => __('Vitamin C', 'wc-nutrition-simple'),
                        'schema' => 'vitaminC',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 90,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_e',
                        'label' => __('Vitamin E', 'wc-nutrition-simple'),
                        'schema' => 'vitaminE',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 15,
                        'unit' => 'IU'
                    ),
                    array(
                        'id' => 'vitamin_k',
                        'label' => __('Vitamin K', 'wc-nutrition-simple'),
                        'schema' => 'vitaminK',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 120,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'vitamin_b1',
                        'label' => __('Vitamin B1 (Thiamin)', 'wc-nutrition-simple'),
                        'schema' => 'vitaminB1',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 1.2,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_b2',
                        'label' => __('Vitamin B2 (Riboflavin)', 'wc-nutrition-simple'),
                        'schema' => 'vitaminB2',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 1.3,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_b3',
                        'label' => __('Vitamin B3 (Niacin)', 'wc-nutrition-simple'),
                        'schema' => 'vitaminB3',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 16,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_b5',
                        'label' => __('Vitamin B5 (Pantothenic Acid)', 'wc-nutrition-simple'),
                        'schema' => 'vitaminB5',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 5,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_b6',
                        'label' => __('Vitamin B6', 'wc-nutrition-simple'),
                        'schema' => 'vitaminB6',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 1.7,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'vitamin_b12',
                        'label' => __('Vitamin B12', 'wc-nutrition-simple'),
                        'schema' => 'vitaminB12',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 2.4,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'folate',
                        'label' => __('Folate', 'wc-nutrition-simple'),
                        'schema' => 'folate',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 400,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'biotin',
                        'label' => __('Biotin', 'wc-nutrition-simple'),
                        'schema' => 'biotin',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 30,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'choline',
                        'label' => __('Choline', 'wc-nutrition-simple'),
                        'schema' => 'choline',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 550,
                        'unit' => 'mg'
                    ),
                    // Minerals
                    array(
                        'id' => 'calcium',
                        'label' => __('Calcium', 'wc-nutrition-simple'),
                        'schema' => 'calcium',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => $standard_daily_values['calcium'],
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'iron',
                        'label' => __('Iron', 'wc-nutrition-simple'),
                        'schema' => 'iron',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => $standard_daily_values['iron'],
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'potassium',
                        'label' => __('Potassium', 'wc-nutrition-simple'),
                        'schema' => 'potassium',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => $standard_daily_values['potassium'],
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'phosphorus',
                        'label' => __('Phosphorus', 'wc-nutrition-simple'),
                        'schema' => 'phosphorus',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 1250,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'magnesium',
                        'label' => __('Magnesium', 'wc-nutrition-simple'),
                        'schema' => 'magnesium',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 420,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'zinc',
                        'label' => __('Zinc', 'wc-nutrition-simple'),
                        'schema' => 'zinc',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 11,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'selenium',
                        'label' => __('Selenium', 'wc-nutrition-simple'),
                        'schema' => 'selenium',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 55,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'copper',
                        'label' => __('Copper', 'wc-nutrition-simple'),
                        'schema' => 'copper',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 0.9,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'manganese',
                        'label' => __('Manganese', 'wc-nutrition-simple'),
                        'schema' => 'manganese',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 2.3,
                        'unit' => 'mg'
                    ),
                    array(
                        'id' => 'chromium',
                        'label' => __('Chromium', 'wc-nutrition-simple'),
                        'schema' => 'chromium',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 35,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'molybdenum',
                        'label' => __('Molybdenum', 'wc-nutrition-simple'),
                        'schema' => 'molybdenum',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 45,
                        'unit' => 'mcg'
                    ),
                    array(
                        'id' => 'chloride',
                        'label' => __('Chloride', 'wc-nutrition-simple'),
                        'schema' => 'chloride',
                        'liclass' => false,
                        'labelclass' => '',
                        'sv' => 2300,
                        'unit' => 'mg'
                    ),
                );

                // Render nutrition facts
                foreach($nutrition_facts as $nf) {
                    if (!empty($nutrition_data[$nf['id']])) {
                        $field_value = $nutrition_data[$nf['id']];
                        $offset = $round_daily_values ? 0 : 2;
                        $dv = !empty($nf['sv']) ? round((float)$field_value * 100 / $nf['sv'], $offset) : '';
                        
                        if ($show_daily_values) {
                            // Always render all 4 columns for proper alignment
                            printf('<li%s><span class="nt-label col-40%s">%s</span><span class="nt-amount col-20"%s>%s</span><span class="sdv-label col-20">%s</span><span class="pdv-label col-20">%s</span></li>',
                                !empty($nf['liclass']) ? ' class="' . esc_attr($nf['liclass']) . '"' : '',
                                !empty($nf['labelclass']) ? ' ' . esc_attr($nf['labelclass']) : '',
                                esc_html($nf['label']),
                                !empty($nf['schema']) ? ' itemprop="' . esc_attr($nf['schema']) . '"' : '',
                                esc_html($field_value . $nf['unit']),
                                !empty($nf['sv']) ? esc_html($nf['sv'] . $nf['unit']) : '',
                                !empty($dv) ? esc_html($dv . '%') : ''
                            );
                        } else {
                            // 3 columns when Standard DV is hidden
                            printf('<li%s><span class="nt-label col-40%s">%s</span><span class="nt-amount col-30"%s>%s</span><span class="pdv-label col-30">%s</span></li>',
                                !empty($nf['liclass']) ? ' class="' . esc_attr($nf['liclass']) . '"' : '',
                                !empty($nf['labelclass']) ? ' ' . esc_attr($nf['labelclass']) : '',
                                esc_html($nf['label']),
                                !empty($nf['schema']) ? ' itemprop="' . esc_attr($nf['schema']) . '"' : '',
                                esc_html($field_value . $nf['unit']),
                                !empty($dv) ? esc_html($dv . '%') : ''
                            );
                        }
                    }
                }
                ?>
                
                <li class="nt-sep sep-8"></li>
                <li class="nt-footer b-0">
                    <span class="nt-label col-100"><?php esc_html_e('* The % Daily Value (DV) tells you how much a nutrient in a serving of food contributes to a daily diet. 2,000 calories a day is used for general nutrition advice.', 'wc-nutrition-simple'); ?></span>
                </li>
            </ul>
        </div>
        <?php
        
        return ob_get_clean();
    }

    public function add_nutrition_body_class($classes) {
        if (is_product()) {
            global $post;
            if ($post) {
                $product_id = $post->ID;
                $nutrition_data = get_post_meta($product_id, '_nutrition_parsed_data', true);
                
                if (!empty($nutrition_data)) {
                    $classes[] = 'has-nutrition-facts';
                    $classes[] = 'nutrition-data-available';
                } else {
                    $classes[] = 'no-nutrition-facts';
                    $classes[] = 'nutrition-data-unavailable';
                }
            }
        }
        
        return $classes;
    }

    private function calculate_daily_value($amount, $standard_value, $round = false) {
        if (empty($amount) || empty($standard_value) || $standard_value == 0) {
            return '';
        }
        $percentage = ($amount / $standard_value) * 100;
        return $round ? round($percentage) : round($percentage, 2);
    }

    public function enqueue_scripts() {
        // Only load CSS on product pages
        if (is_product()) {
            wp_enqueue_style(
                'wc-nutrition-simple-style',
                WC_NUTRITION_SIMPLE_PLUGIN_URL . 'assets/css/nutrition-facts.css',
                array(),
                WC_NUTRITION_SIMPLE_VERSION
            );
        }
    }
    
    /**
     * Parse new_nutrition_info field and update individual ACF fields
     */
    public function parse_nutrition_info_field($post_id) {
        // Only process WooCommerce products
        if (get_post_type($post_id) !== 'product') {
            return;
        }
        
        // Get the new_nutrition_info field value
        $nutrition_info = get_field('new_nutrition_info', $post_id);
        
        // Handle array input (from Air WP Sync)
        if (is_array($nutrition_info) && !empty($nutrition_info)) {
            $nutrition_info = $nutrition_info[0]; // Get first element
        }
        
        if (empty($nutrition_info)) {
            // Clear nutrition data if no source data
            delete_post_meta($post_id, '_nutrition_parsed_data');
            return;
        }
        
        // Parse the nutrition data
        $parsed_data = $this->parse_nutrition_text($nutrition_info);
        
        // Store parsed data in post_meta
        if (!empty($parsed_data)) {
            update_post_meta($post_id, '_nutrition_parsed_data', $parsed_data);
        } else {
            delete_post_meta($post_id, '_nutrition_parsed_data');
        }
    }
    
    /**
     * Check and parse nutrition data if needed (lazy parsing)
     * Used when products are updated via Air WP Sync or other external methods
     */
    public function maybe_parse_nutrition_data($post_id) {
        // Only process WooCommerce products
        if (get_post_type($post_id) !== 'product') {
            return;
        }
        
        // Check if we already have parsed data
        $existing_data = get_post_meta($post_id, '_nutrition_parsed_data', true);
        if (!empty($existing_data)) {
            return; // Already parsed, no need to check again
        }
        
        // Check transient to avoid repeated parsing attempts
        $transient_key = 'nutrition_parse_check_' . $post_id;
        if (get_transient($transient_key)) {
            return; // Already checked recently, skip
        }
        
        // Get the new_nutrition_info field value
        $nutrition_info = get_field('new_nutrition_info', $post_id);
        
        // Handle array input (from Air WP Sync)
        if (is_array($nutrition_info) && !empty($nutrition_info)) {
            $nutrition_info = $nutrition_info[0]; // Get first element
        }
        
        if (empty($nutrition_info)) {
            // Set transient to avoid checking again for 1 hour
            set_transient($transient_key, 'checked', HOUR_IN_SECONDS);
            return;
        }
        
        // Parse the nutrition data
        $parsed_data = $this->parse_nutrition_text($nutrition_info);
        
        // Store parsed data in post_meta
        if (!empty($parsed_data)) {
            update_post_meta($post_id, '_nutrition_parsed_data', $parsed_data);
            // Clear transient on successful parse
            delete_transient($transient_key);
        } else {
            // Set transient to avoid checking again for 1 hour if parsing failed
            set_transient($transient_key, 'checked', HOUR_IN_SECONDS);
        }
    }
    
    /**
     * Lazy parse nutrition data on product view (frontend)
     * Uses transient to avoid performance impact
     */
    public function maybe_parse_on_product_view() {
        // Only run on single product pages
        if (!is_product() && !is_singular('product')) {
            return;
        }
        
        global $post;
        if (!$post || get_post_type($post->ID) !== 'product') {
            return;
        }
        
        // Check and parse if needed
        $this->maybe_parse_nutrition_data($post->ID);
    }
    
    /**
     * Parse nutrition text and extract values
     */
    private function parse_nutrition_text($text) {
        $parsed_data = array();
        
        // Convert <br> tags to spaces and remove other HTML tags
        $text = str_replace(array('<br>', '<br/>', '<br />'), ' ', $text);
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text); // Replace multiple spaces with single space
        $text = trim($text);
        
        // Define field mappings with alternative spellings and common mistakes
        $field_mappings = array(
            'serving_size' => array('patterns' => array(
                '/Serving Size\s+([^<]+?)(?:\s+\d+%|\s*$)/i',
                '/Serving\s+Size\s+([^<]+?)(?:\s+\d+%|\s*$)/i'
            )),
            'serving_per_container' => array('patterns' => array(
                '/Serving Per Container\s+(\d+(?:\.\d+)?)/i',
                '/Servings Per Container\s+(\d+(?:\.\d+)?)/i',
                '/Serving\s+Per\s+Container\s+(\d+(?:\.\d+)?)/i'
            )),
            'calories' => array('patterns' => array(
                '/Calories\s+(\d+)/i',
                '/Calorie\s+(\d+)/i',
                '/Energy\s+\d+kJ\/(\d+)kcal/i', // Energy 231kJ/55kcal
                '/Energy\s+(\d+)\s*kcal/i' // Energy 55kcal
            )),
            'total_fat' => array('patterns' => array(
                '/Total Fat\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Total\s+Fat\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Fat\s+<(\d+(?:\.\d+)?)\s*g/i', // Fat <0.5g (less than)
                '/Fat\s+(\d+(?:\.\d+)?)\s*g/i' // Fat 0.5g
            )),
            'saturated_fat' => array('patterns' => array(
                '/Saturated Fat\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Saturated\s+Fat\s+(\d+(?:\.\d+)?)\s*g/i',
                '/of which saturates\s+<(\d+(?:\.\d+)?)\s*g/i', // of which saturates <0.1g
                '/of which saturates\s+(\d+(?:\.\d+)?)\s*g/i', // of which saturates 0.1g
                '/-of which saturates\s+<(\d+(?:\.\d+)?)\s*g/i', // -of which saturates <0.1g
                '/-of which saturates\s+(\d+(?:\.\d+)?)\s*g/i' // -of which saturates 0.1g
            )),
            'trans_fat' => array('patterns' => array(
                '/Trans Fat\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Trans\s+Fat\s+(\d+(?:\.\d+)?)\s*g/i'
            )),
            'cholesterol' => array('patterns' => array(
                '/Cholesterol\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Cholestrol\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'sodium' => array('patterns' => array(
                '/Sodium\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Salt\s+(\d+(?:\.\d+)?)\s*g/i' // Salt 3.7g - convert to sodium (salt * 400 = sodium in mg)
            ), 'convert' => array('salt' => 400)), // Convert salt (g) to sodium (mg): salt * 400
            'carbohydrate' => array('patterns' => array(
                '/Total Carbohydrate\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Total\s+Carbohydrate\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Carbohydrate\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Carbohydrates\s+(\d+(?:\.\d+)?)\s*g/i' // Carbohydrates 11g (Airtable format)
            )),
            'fiber' => array('patterns' => array(
                '/Dietary Fiber\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Dietary\s+Fiber\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Fiber\s+(\d+(?:\.\d+)?)\s*g/i'
            )),
            'sugar' => array('patterns' => array(
                '/Total Sugars\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Total\s+Sugars\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Sugars\s+(\d+(?:\.\d+)?)\s*g/i',
                '/of which sugars\s+(\d+(?:\.\d+)?)\s*g/i', // of which sugars 7.7g
                '/- of which sugars\s+(\d+(?:\.\d+)?)\s*g/i' // - of which sugars 7.7g
            )),
            'added_sugars' => array('patterns' => array(
                '/Includes\s+(\d+(?:\.\d+)?)\s*g\s+Added Sugars/i',
                '/Added Sugars\s+(\d+(?:\.\d+)?)\s*g/i',
                '/Added\s+Sugars\s+(\d+(?:\.\d+)?)\s*g/i'
            )),
            'protein' => array('patterns' => array(
                '/Protein\s+(\d+(?:\.\d+)?)\s*g/i'
            )),
            // Vitamins
            'vitamin_d' => array('patterns' => array(
                '/Vitamin D\s+(\d+(?:\.\d+)?)\s*mcg/i',
                '/Vitamin\s+D\s+(\d+(?:\.\d+)?)\s*mcg/i'
            )),
            'vitamin_a' => array('patterns' => array(
                '/Vitamin A\s+(\d+(?:\.\d+)?)\s*IU/i',
                '/Vitamin\s+A\s+(\d+(?:\.\d+)?)\s*IU/i'
            )),
            'vitamin_c' => array('patterns' => array(
                '/Vitamin C\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Vitamin\s+C\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'vitamin_e' => array('patterns' => array(
                '/Vitamin E\s+(\d+(?:\.\d+)?)\s*IU/i',
                '/Vitamin\s+E\s+(\d+(?:\.\d+)?)\s*IU/i'
            )),
            'vitamin_k' => array('patterns' => array(
                '/Vitamin K\s+(\d+(?:\.\d+)?)\s*mcg/i',
                '/Vitamin\s+K\s+(\d+(?:\.\d+)?)\s*mcg/i'
            )),
            'vitamin_b1' => array('patterns' => array(
                '/Vitamin B1\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Vitamin\s+B1\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Thiamin\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'vitamin_b2' => array('patterns' => array(
                '/Vitamin B2\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Vitamin\s+B2\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Riboflavin\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'vitamin_b3' => array('patterns' => array(
                '/Vitamin B3\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Vitamin\s+B3\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Niacin\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'vitamin_b5' => array('patterns' => array(
                '/Vitamin B5\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Vitamin\s+B5\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Pantothenic Acid\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'vitamin_b6' => array('patterns' => array(
                '/Vitamin B6\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Vitamin\s+B6\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'vitamin_b12' => array('patterns' => array(
                '/Vitamin B12\s+(\d+(?:\.\d+)?)\s*mcg/i',
                '/Vitamin\s+B12\s+(\d+(?:\.\d+)?)\s*mcg/i'
            )),
            'folate' => array('patterns' => array(
                '/Folate\s+(\d+(?:\.\d+)?)\s*mcg/i',
                '/Folic Acid\s+(\d+(?:\.\d+)?)\s*mcg/i'
            )),
            'biotin' => array('patterns' => array(
                '/Biotin\s+(\d+(?:\.\d+)?)\s*mcg/i'
            )),
            'choline' => array('patterns' => array(
                '/Choline\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            // Minerals
            'calcium' => array('patterns' => array(
                '/Calcium\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'iron' => array('patterns' => array(
                '/Iron\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'potassium' => array('patterns' => array(
                '/Potassium\s+(\d+(?:\.\d+)?)\s*mg/i',
                '/Potasium\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'phosphorus' => array('patterns' => array(
                '/Phosphorus\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'magnesium' => array('patterns' => array(
                '/Magnesium\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'zinc' => array('patterns' => array(
                '/Zinc\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'selenium' => array('patterns' => array(
                '/Selenium\s+(\d+(?:\.\d+)?)\s*mcg/i'
            )),
            'copper' => array('patterns' => array(
                '/Copper\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'manganese' => array('patterns' => array(
                '/Manganese\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
            'chromium' => array('patterns' => array(
                '/Chromium\s+(\d+(?:\.\d+)?)\s*mcg/i'
            )),
            'molybdenum' => array('patterns' => array(
                '/Molybdenum\s+(\d+(?:\.\d+)?)\s*mcg/i'
            )),
            'chloride' => array('patterns' => array(
                '/Chloride\s+(\d+(?:\.\d+)?)\s*mg/i'
            )),
        );
        
        // Parse each field
        foreach ($field_mappings as $field_name => $config) {
            foreach ($config['patterns'] as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    $value = floatval($matches[1]);
                    
                    // Handle conversions (e.g., salt to sodium)
                    if (isset($config['convert']) && isset($config['convert']['salt'])) {
                        // Convert salt (g) to sodium (mg): salt * 400
                        $value = $value * $config['convert']['salt'];
                    }
                    
                    $parsed_data[$field_name] = $value;
                    break; // Stop after first match
                }
            }
        }
        
        return $parsed_data;
    }
}

// Initialize the plugin
new WC_Nutrition_Facts_Simple();