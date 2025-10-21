# Developer Guide - WooCommerce Nutrition Facts ACF

## Quick Reference

### All ACF Field Keys

```php
// Basic Information
'nutrition_heading'     // Text - Custom heading
'serving_size'         // Text - Serving size info
'serving_per_container' // Text - Servings per container
'calories'             // Number - Calories per serving

// Macronutrients
'total_fat'            // Number - Total Fat (g)
'saturated_fat'        // Number - Saturated Fat (g)
'trans_fat'            // Number - Trans Fat (g)
'polyunsaturated_fat'  // Number - Polyunsaturated Fat (g)
'monounsaturated_fat'  // Number - Monounsaturated Fat (g)
'cholesterol'          // Number - Cholesterol (mg)
'sodium'               // Number - Sodium (mg)
'carbohydrate'         // Number - Total Carbohydrate (g)
'fiber'                // Number - Dietary Fiber (g)
'sugar'                // Number - Total Sugars (g)
'added_sugar'          // Number - Added Sugars (g)
'sugar_alcohol'        // Number - Sugar Alcohol (g)
'protein'              // Number - Protein (g)

// Vitamins
'vitamin_d'            // Number - Vitamin D (mcg)
'vitamin_a'            // Number - Vitamin A (mcg)
'vitamin_c'            // Number - Vitamin C (mg)
'vitamin_e'            // Number - Vitamin E (mg)
'vitamin_k'            // Number - Vitamin K (mcg)
'vitamin_b1'           // Number - Vitamin B1 (mg)
'vitamin_b2'           // Number - Vitamin B2 (mg)
'vitamin_b3'           // Number - Vitamin B3 (mg)
'vitamin_b6'           // Number - Vitamin B6 (mg)
'folate'               // Number - Folate (mcg)
'vitamin_b12'          // Number - Vitamin B12 (mcg)
'biotin'               // Number - Biotin (mcg)
'vitamin_b5'           // Number - Vitamin B5 (mg)
'choline'              // Number - Choline (mg)

// Minerals
'calcium'              // Number - Calcium (mg)
'iron'                 // Number - Iron (mg)
'potassium'            // Number - Potassium (mg)
'phosphorus'           // Number - Phosphorus (mg)
'iodine'               // Number - Iodine (mcg)
'magnesium'            // Number - Magnesium (mg)
'zinc'                 // Number - Zinc (mg)
'selenium'             // Number - Selenium (mcg)
'copper'               // Number - Copper (mg)
'manganese'            // Number - Manganese (mg)
'chromium'             // Number - Chromium (mcg)
'molybdenum'           // Number - Molybdenum (mcg)
'chloride'             // Number - Chloride (mg)

// Display Options
'show_daily_values'    // True/False - Show Standard DV column
'round_daily_values'   // True/False - Round percentages
'extra_notes'         // Textarea - Additional notes
```

### Standard Daily Values

```php
$standard_daily_values = array(
    'total_fat' => 78,        // g
    'saturated_fat' => 20,    // g
    'cholesterol' => 300,     // mg
    'sodium' => 2300,         // mg
    'carbohydrate' => 275,    // g
    'fiber' => 28,            // g
    'added_sugar' => 50,      // g
    'protein' => 50,          // g
    'vitamin_d' => 20,        // mcg
    'calcium' => 1300,        // mg
    'iron' => 18,             // mg
    'potassium' => 4700,      // mg
    'vitamin_a' => 900,       // mcg
    'vitamin_c' => 90,        // mg
    'vitamin_e' => 15,        // mg
    'vitamin_k' => 120,       // mcg
    'vitamin_b1' => 1.2,      // mg
    'vitamin_b2' => 1.3,      // mg
    'vitamin_b3' => 16,       // mg
    'vitamin_b6' => 1.7,      // mg
    'folate' => 400,          // mcg
    'vitamin_b12' => 2.4,     // mcg
    'biotin' => 30,           // mcg
    'vitamin_b5' => 5,        // mg
    'choline' => 550,         // mg
    'phosphorus' => 1250,     // mg
    'iodine' => 150,          // mcg
    'magnesium' => 420,       // mg
    'zinc' => 11,             // mg
    'selenium' => 55,         // mcg
    'copper' => 0.9,          // mg
    'manganese' => 2.3,       // mg
    'chromium' => 35,         // mcg
    'molybdenum' => 45,       // mcg
    'chloride' => 2300,       // mg
);
```

### Common Usage Examples

```php
// Get nutrition data for a product
$product_id = 123;
$calories = get_field('calories', $product_id);
$total_fat = get_field('total_fat', $product_id);

// Check if nutrition data exists
$has_nutrition = !empty(get_field('calories', $product_id)) || 
                 !empty(get_field('total_fat', $product_id));

// Get all nutrition fields at once
$nutrition_fields = array(
    'calories', 'total_fat', 'saturated_fat', 'cholesterol', 'sodium',
    'carbohydrate', 'fiber', 'sugar', 'protein', 'vitamin_d', 'calcium', 'iron'
);

$nutrition_data = array();
foreach($nutrition_fields as $field) {
    $nutrition_data[$field] = get_field($field, $product_id);
}
```

### Hooks and Filters

```php
// Filter nutrition facts array (if needed)
add_filter('nflc_nutrition_facts_list', 'custom_nutrition_facts');

// Customize standard daily values
add_filter('total_fat_sv', function($value) {
    return 80; // Custom standard value
});

// Modify shortcode output
add_filter('wc_nutrition_facts_output', 'custom_nutrition_output');
```

### CSS Customization

```css
/* Custom styling for nutrition labels */
.nflc .nutrition-table {
    border: 2px solid #000;
    font-family: Arial, sans-serif;
}

.nflc .nt-title {
    font-size: 28px;
    font-weight: bold;
}

.nflc .nt-label {
    font-weight: bold;
}

.nflc .nt-value {
    text-align: right;
    font-weight: bold;
}
```

### Template Override

To customize the nutrition label template:

1. Copy the shortcode output from the plugin
2. Create a custom template in your theme
3. Use `get_field()` to get nutrition data
4. Apply custom styling

### Database Schema

All nutrition data is stored in WordPress post meta:

```sql
-- Example meta keys in wp_postmeta table
meta_key = 'calories'
meta_key = 'total_fat'
meta_key = 'saturated_fat'
-- ... etc for all 40+ fields
```

### Performance Notes

- All fields use ACF's `get_field()` function
- Data is cached by ACF automatically
- No database queries needed for field retrieval
- CSS is enqueued only on relevant pages
