# WooCommerce Nutrition Facts ACF Plugin

A comprehensive WordPress plugin that adds nutrition facts fields to WooCommerce products using Advanced Custom Fields (ACF) Free version.

## Plugin Information

- **Plugin Name**: WooCommerce Nutrition Facts - Simple
- **Version**: 1.0.0
- **Author**: Md Solaiman
- **Email**: nirjondipo@gmail.com
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

## Automatic Field Population

The plugin includes an automatic field population feature that parses nutrition data from a `new_nutrition_info` field and populates all individual ACF fields automatically.

### How to Use Automatic Population

1. **Add the `new_nutrition_info` field** to your ACF field group
2. **Paste your nutrition data** into this field
3. **Save the product** - plugin automatically populates all individual fields

### When Automatic Parsing Works

The plugin automatically parses nutrition data in these scenarios:

#### ✅ **Product Updates:**
- **Manual Updates**: When you save/update products in WordPress admin
- **Bulk Updates**: When updating multiple products at once
- **API Updates**: When products are updated via WooCommerce REST API
- **AirTable Integration**: When AirTable updates products via WooCommerce API

#### ✅ **Product Creation:**
- **New Products**: When creating new products manually
- **API Creation**: When products are created via WooCommerce REST API
- **Bulk Imports**: When importing products from CSV or other sources
- **AirTable Sync**: When AirTable creates new products

#### ✅ **Data Changes:**
- **Field Updates**: When `new_nutrition_info` field content changes
- **Data Addition**: When nutrition data is added to empty field
- **Data Removal**: When nutrition data is cleared from field
- **Data Modification**: When existing nutrition data is updated

#### ✅ **Integration Scenarios:**
- **AirTable**: Perfect for bulk product management
- **WooCommerce API**: Works with any API-based updates
- **Import Tools**: Compatible with product import plugins
- **Custom Integrations**: Works with any WordPress/WooCommerce hooks

### How Automatic Population Works

When you paste nutrition data into the `new_nutrition_info` field and save the product, the plugin automatically:

- ✅ **Parses the text** and identifies nutrition field names
- ✅ **Extracts values** (ignoring percentages and HTML tags)
- ✅ **Populates individual ACF fields** automatically
- ✅ **Updates all 40+ nutrition fields** behind the scenes
- ✅ **Displays nutrition label** on frontend with parsed data

### Complete Example for `new_nutrition_info` Field
For example, we can use the following as field values for New Nutrition info `new_nutrition_info`

```html
<p>Serving Size 1 cup (240ml)<br /> Serving Per Container 4<br />Calories 180<br /> Total Fat 5 g 6%<br /> Saturated Fat 2 g 10%<br /> Trans Fat 0 g<br /> Cholesterol 10 mg 3%<br /> Sodium 25 mg 1%<br /> Total Carbohydrate 14 g 5%<br /> Dietary Fiber 6 g 21%<br /> Total Sugars 4 g<br /> Includes 2 g Added Sugars 4%<br /> Protein 20 g 40%<br /> Vitamin D 0 mcg 0%<br /> Calcium 246 mg 19%<br /> Iron 3 mg 17%<br /> Potassium 601 mg 13%<br /> Vitamin A 900 IU 100%<br /> Vitamin C 89 mg 99%<br /> Vitamin E 15 IU 100%<br /> Vitamin K 119 mcg 99%<br /> Vitamin B1 1.2 mg 100%<br /> Vitamin B2 1.3 mg 100%<br /> Vitamin B3 16 mg 100%<br /> Vitamin B5 5 mg 100%<br /> Vitamin B6 1.7 mg 100%<br /> Vitamin B12 2.4 mcg 100%<br /> Folate 400 mcg 100%<br /> Biotin 30 mcg 100%<br /> Choline 550 mg 100%<br /> Phosphorus 1250 mg 100%<br /> Magnesium 420 mg 100%<br /> Zinc 11 mg 100%<br /> Selenium 55 mcg 100%<br /> Copper 0.9 mg 100%<br /> Manganese 2.3 mg 100%<br /> Chromium 35 mcg 100%<br /> Molybdenum 45 mcg 100%<br /> Chloride 2300 mg 100%</p>
```

This is how the data will look in the `new_nutrition_info` field for better understanding:

```html
<p>Serving Size 1 cup (240ml)<br />
Serving Per Container 4<br />
Calories 180<br />
Total Fat 5 g 6%<br />
Saturated Fat 2 g 10%<br />
Trans Fat 0 g<br />
Cholesterol 10 mg 3%<br />
Sodium 25 mg 1%<br />
Total Carbohydrate 14 g 5%<br />
Dietary Fiber 6 g 21%<br />
Total Sugars 4 g<br />
Includes 2 g Added Sugars 4%<br />
Protein 20 g 40%<br />
Vitamin D 0 mcg 0%<br />
Calcium 246 mg 19%<br />
Iron 3 mg 17%<br />
Potassium 601 mg 13%<br />
Vitamin A 900 IU 100%<br />
Vitamin C 89 mg 99%<br />
Vitamin E 15 IU 100%<br />
Vitamin K 119 mcg 99%<br />
Vitamin B1 1.2 mg 100%<br />
Vitamin B2 1.3 mg 100%<br />
Vitamin B3 16 mg 100%<br />
Vitamin B5 5 mg 100%<br />
Vitamin B6 1.7 mg 100%<br />
Vitamin B12 2.4 mcg 100%<br />
Folate 400 mcg 100%<br />
Biotin 30 mcg 100%<br />
Choline 550 mg 100%<br />
Phosphorus 1250 mg 100%<br />
Magnesium 420 mg 100%<br />
Zinc 11 mg 100%<br />
Selenium 55 mcg 100%<br />
Copper 0.9 mg 100%<br />
Manganese 2.3 mg 100%<br />
Chromium 35 mcg 100%<br />
Molybdenum 45 mcg 100%<br />
Chloride 2300 mg 100%</p>
```

### What Happens When You Save

After pasting the above data into `new_nutrition_info` and saving the product, the plugin automatically populates these individual ACF fields:

### What This Example Will Populate

#### Basic Information
- **serving_size** → `1 cup (240ml)`
- **serving_per_container** → `4`
- **calories** → `180`

#### Macronutrients
- **total_fat** → `5`
- **saturated_fat** → `2`
- **trans_fat** → `0`
- **cholesterol** → `10`
- **sodium** → `25`
- **carbohydrate** → `14`
- **fiber** → `6`
- **sugar** → `4`
- **added_sugars** → `2`
- **protein** → `20`

#### Vitamins
- **vitamin_d** → `0`
- **vitamin_a** → `900`
- **vitamin_c** → `89`
- **vitamin_e** → `15`
- **vitamin_k** → `119`
- **vitamin_b1** → `1.2`
- **vitamin_b2** → `1.3`
- **vitamin_b3** → `16`
- **vitamin_b5** → `5`
- **vitamin_b6** → `1.7`
- **vitamin_b12** → `2.4`
- **folate** → `400`
- **biotin** → `30`
- **choline** → `550`

#### Minerals
- **calcium** → `246`
- **iron** → `3`
- **potassium** → `601`
- **phosphorus** → `1250`
- **magnesium** → `420`
- **zinc** → `11`
- **selenium** → `55`
- **copper** → `0.9`
- **manganese** → `2.3`
- **chromium** → `35`
- **molybdenum** → `45`
- **chloride** → `2300`

### Format Rules

#### ✅ What the plugin recognizes:
- **Field names**: Total Fat, Saturated Fat, Sodium, Protein, etc.
- **Values**: Numbers (5, 2, 25, 20)
- **Units**: g, mg, mcg, IU
- **HTML tags**: Automatically cleaned
- **Percentages**: Automatically ignored

#### ✅ Supported formats:
```
Total Fat 5 g
Total Fat 5 g 6%
Total Fat 5 g 6% (with percentages)
```

#### ✅ Units supported:
- **g** (grams)
- **mg** (milligrams) 
- **mcg** (micrograms)
- **IU** (International Units)

### Step-by-Step Process

1. **Input**: You paste nutrition data into `new_nutrition_info` field
   ```
   Serving Size 1 cup (240ml)
   Calories 180
   Total Fat 5 g 6%
   Sodium 25 mg 1%
   ```

2. **Parse**: Plugin reads and analyzes the text
   - Identifies "Serving Size" → `serving_size` field
   - Identifies "Calories" → `calories` field
   - Identifies "Total Fat" → `total_fat` field
   - Identifies "Sodium" → `sodium` field

3. **Extract**: Plugin extracts values and units
   - `serving_size` = "1 cup (240ml)"
   - `calories` = "180"
   - `total_fat` = "5"
   - `sodium` = "25"

4. **Clean**: Plugin removes unwanted elements
   - Removes percentages (6%, 1%)
   - Removes HTML tags (`<br>`, `<p>`)
   - Cleans extra spaces

5. **Update**: Plugin populates individual ACF fields
   - Updates `serving_size` field with "1 cup (240ml)"
   - Updates `calories` field with "180"
   - Updates `total_fat` field with "5"
   - Updates `sodium` field with "25"

6. **Display**: Frontend shows nutrition label with parsed data
   - Nutrition Facts label displays with all parsed values
   - % Daily Values calculated automatically
   - Professional FDA-compliant design

### Benefits

- ✅ **Bulk data entry**: Paste complete nutrition data at once
- ✅ **Automatic parsing**: No manual field entry required
- ✅ **Flexible format**: Works with or without percentages
- ✅ **Clean data**: Automatically removes unwanted elements
- ✅ **Time saving**: Reduces data entry time significantly

### Technical Implementation

The automatic parsing uses WordPress hooks to trigger on product saves:

```php
// WordPress hook that triggers on any post save
add_action('acf/save_post', array($this, 'parse_nutrition_info_field'), 20);
```

#### **Process Flow:**
1. **Trigger**: Product save/update occurs
2. **Detection**: Plugin checks if `new_nutrition_info` has data
3. **Parsing**: Plugin extracts nutrition values using regex patterns
4. **Storage**: Parsed data stored in `_nutrition_parsed_data` post_meta
5. **Display**: Frontend reads from post_meta for nutrition label

#### **Supported Integrations:**
- **AirTable**: ✅ Full compatibility with WooCommerce API
- **WooCommerce REST API**: ✅ Works with any API-based updates
- **Import Plugins**: ✅ Compatible with product import tools
- **Custom Hooks**: ✅ Works with any WordPress/WooCommerce hooks

## Support

For support and customization requests, contact:
- **Author**: Md Solaiman
- **Email**: nirjondipo@gmail.com
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
