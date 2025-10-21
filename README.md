# WooCommerce Nutrition Facts ACF Plugin

A comprehensive WordPress plugin that adds nutrition facts fields to WooCommerce products using Advanced Custom Fields (ACF) Free version.

## Plugin Information

- **Plugin Name**: WooCommerce Nutrition Facts - Simple
- **Version**: 1.0.0
- **Author**: Md Solaiman
- **Author URI**: https://www.upwork.com/freelancers/~01da2982e531013221
- **Description**: Simple nutrition facts fields for WooCommerce products using ACF Free
- **Requires**: WordPress 5.0+, WooCommerce 5.0+, ACF Free

## Features

- ✅ **40+ Nutrition Fields** - Complete FDA-compliant nutrition label
- ✅ **ACF Free Compatible** - No PRO version required
- ✅ **WooCommerce Integration** - Works with WooCommerce products
- ✅ **Shortcode Support** - Display nutrition labels anywhere
- ✅ **FDA-Compliant Design** - Professional nutrition label appearance
- ✅ **Standard Daily Values** - Automatic percentage calculations
- ✅ **Elementor Compatible** - Use in Elementor tabs and widgets

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WooCommerce and ACF Free are installed and active

## ACF Field Groups

The plugin creates a field group called **"Nutrition Facts"** with the following fields:

### Basic Information Fields

| Field Key | Field Label | Field Type | Description |
|-----------|-------------|------------|-------------|
| `nutrition_heading` | Nutrition Table Heading | Text | Custom heading for the nutrition table (default: "Nutrition Facts") |
| `serving_size` | Serving Size | Text | Serving size information (e.g., "1 Cookie(20g)") |
| `serving_per_container` | Serving Per Container | Text | Number of servings per container (e.g., "30") |
| `calories` | Calories per serving (cal) | Number | Calories per serving (without unit) |

### Macronutrients Fields

| Field Key | Field Label | Field Type | Standard DV | Unit |
|-----------|-------------|------------|-------------|------|
| `total_fat` | Total Fat | Number | 78g | g |
| `saturated_fat` | Saturated Fat | Number | 20g | g |
| `trans_fat` | Trans Fat | Number | - | g |
| `polyunsaturated_fat` | Polyunsaturated Fat | Number | - | g |
| `monounsaturated_fat` | Monounsaturated Fat | Number | - | g |
| `cholesterol` | Cholesterol | Number | 300mg | mg |
| `sodium` | Sodium | Number | 2300mg | mg |
| `carbohydrate` | Total Carbohydrate | Number | 275g | g |
| `fiber` | Dietary Fiber | Number | 28g | g |
| `sugar` | Total Sugars | Number | - | g |
| `added_sugar` | Added Sugars | Number | 50g | g |
| `sugar_alcohol` | Sugar Alcohol | Number | - | g |
| `protein` | Protein | Number | 50g | g |

### Vitamin Fields

| Field Key | Field Label | Field Type | Standard DV | Unit |
|-----------|-------------|------------|-------------|------|
| `vitamin_d` | Vitamin D (Cholecalciferol) | Number | 20mcg | mcg |
| `vitamin_a` | Vitamin A | Number | 900mcg | mcg |
| `vitamin_c` | Vitamin C (Ascorbic Acid) | Number | 90mg | mg |
| `vitamin_e` | Vitamin E (Tocopherol) | Number | 15mg | mg |
| `vitamin_k` | Vitamin K | Number | 120mcg | mcg |
| `vitamin_b1` | Vitamin B1 (Thiamin) | Number | 1.2mg | mg |
| `vitamin_b2` | Vitamin B2 (Riboflavin) | Number | 1.3mg | mg |
| `vitamin_b3` | Vitamin B3 (Niacin) | Number | 16mg | mg |
| `vitamin_b6` | Vitamin B6 (Pyridoxine) | Number | 1.7mg | mg |
| `folate` | Folate | Number | 400mcg | mcg |
| `vitamin_b12` | Vitamin B12 (Cobalamine) | Number | 2.4mcg | mcg |
| `biotin` | Biotin | Number | 30mcg | mcg |
| `vitamin_b5` | Vitamin B5 (Pantothenic acid) | Number | 5mg | mg |
| `choline` | Choline | Number | 550mg | mg |

### Mineral Fields

| Field Key | Field Label | Field Type | Standard DV | Unit |
|-----------|-------------|------------|-------------|------|
| `calcium` | Calcium | Number | 1300mg | mg |
| `iron` | Iron | Number | 18mg | mg |
| `potassium` | Potassium | Number | 4700mg | mg |
| `phosphorus` | Phosphorus | Number | 1250mg | mg |
| `iodine` | Iodine | Number | 150mcg | mcg |
| `magnesium` | Magnesium | Number | 420mg | mg |
| `zinc` | Zinc | Number | 11mg | mg |
| `selenium` | Selenium | Number | 55mcg | mcg |
| `copper` | Copper | Number | 0.9mg | mg |
| `manganese` | Manganese | Number | 2.3mg | mg |
| `chromium` | Chromium | Number | 35mcg | mcg |
| `molybdenum` | Molybdenum | Number | 45mcg | mcg |
| `chloride` | Chloride | Number | 2300mg | mg |

### Display Options

| Field Key | Field Label | Field Type | Default | Description |
|-----------|-------------|------------|---------|-------------|
| `show_daily_values` | Show standard Daily Values | True/False | Yes | Enable/disable Standard DV column |
| `round_daily_values` | Round off Daily Values | True/False | Yes | Round percentages to nearest integer |
| `extra_notes` | Nutrition label extra notes | Textarea | Default FDA text | Additional notes displayed at bottom |

## Usage

### Shortcode

Use the shortcode to display nutrition facts anywhere:

```
[wc_nutrition_facts]
```

#### Shortcode Attributes

| Attribute | Description | Example |
|-----------|-------------|---------|
| `product_id` | Specific product ID | `[wc_nutrition_facts product_id="123"]` |
| `heading` | Custom heading | `[wc_nutrition_facts heading="Nutrition Information"]` |
| `show_daily_values` | Show/hide Standard DV column | `[wc_nutrition_facts show_daily_values="true"]` |
| `round_daily_values` | Round percentages | `[wc_nutrition_facts round_daily_values="false"]` |

### PHP Usage

Get nutrition data programmatically:

```php
// Get specific nutrition values
$calories = get_field('calories', $product_id);
$total_fat = get_field('total_fat', $product_id);
$protein = get_field('protein', $product_id);

// Get all nutrition data
$nutrition_data = array(
    'calories' => get_field('calories', $product_id),
    'total_fat' => get_field('total_fat', $product_id),
    'saturated_fat' => get_field('saturated_fat', $product_id),
    'cholesterol' => get_field('cholesterol', $product_id),
    'sodium' => get_field('sodium', $product_id),
    'carbohydrate' => get_field('carbohydrate', $product_id),
    'fiber' => get_field('fiber', $product_id),
    'sugar' => get_field('sugar', $product_id),
    'protein' => get_field('protein', $product_id),
    // ... add more fields as needed
);
```

### Elementor Integration

1. Add a **Text** or **HTML** widget to your Elementor page
2. Use the shortcode: `[wc_nutrition_facts]`
3. The nutrition label will display with proper styling

## Design Features

- **FDA-Compliant Layout**: Professional nutrition label design
- **4-Column Structure**: Nutrient | Amount | Standard DV | % Daily Value
- **Proper Indentation**: Sub-nutrients are indented (Saturated Fat, Dietary Fiber, etc.)
- **Bold Headers**: Main nutrients are bold, sub-nutrients are regular
- **Separators**: Thick lines separate major sections
- **Responsive Design**: Works on all device sizes

## CSS Classes

The plugin uses the following CSS classes for styling:

- `.nflc` - Main container
- `.nutrition-table` - Nutrition facts table
- `.nt-header` - Header section
- `.nt-title` - Main title
- `.nt-row` - Nutrition row
- `.nt-label` - Nutrient label
- `.nt-amount` - Amount value
- `.nt-sdv` - Standard Daily Value
- `.nt-value` - Percentage Daily Value
- `.nt-sublevel-1` - First level indentation
- `.nt-sublevel-2` - Second level indentation
- `.nt-footer` - Footer notes

## File Structure

```
woocommerce-nutrition-facts-acf/
├── woocommerce-nutrition-facts-simple.php  # Main plugin file
├── assets/
│   └── css/
│       └── nutrition-facts.css            # Styling
└── README.md                              # This documentation
```

## Requirements

- **WordPress**: 5.0 or higher
- **WooCommerce**: 5.0 or higher
- **ACF**: Free version (no PRO required)
- **PHP**: 7.4 or higher

## Compatibility

- ✅ **WooCommerce HPOS**: Compatible with High-Performance Order Storage
- ✅ **ACF Free**: Works with free version of Advanced Custom Fields
- ✅ **Elementor**: Compatible with Elementor page builder
- ✅ **Air WP Sync**: Compatible with WooCommerce sync plugins
- ✅ **Multisite**: Works on WordPress multisite installations

## Support

For support and customization requests, contact:
- **Author**: Md Solaiman
- **Upwork Profile**: https://www.upwork.com/freelancers/~01da2982e531013221

## Changelog

### Version 1.0.0
- Initial release
- 40+ nutrition fields
- ACF Free compatibility
- WooCommerce integration
- Shortcode support
- FDA-compliant design
- Elementor compatibility

## License

This plugin is licensed under the GPL v2 or later.
