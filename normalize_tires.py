#!/usr/bin/env python3
import csv
import json
import re
import sys

def parse_tire_name(name):
    """Parse tire name to extract components."""
    result = {
        'name': name,
        'brand': '',
        'model': '',
        'load_index': '',
        'speed_rating': '',
        'studdable': '',
        'size': '',
        'type': ''
    }
    
    # Extract model (first word/code before space)
    model_match = re.match(r'^([A-Z0-9]+)', name)
    if model_match:
        result['model'] = model_match.group(1)
    
    # Extract load index and speed rating (e.g., "100S", "90T")
    load_speed_match = re.search(r'\b(\d{2,3})([A-Z])\b', name)
    if load_speed_match:
        result['load_index'] = load_speed_match.group(1)
        result['speed_rating'] = load_speed_match.group(2)
    
    # Check if studdable (CLOUTABLE means studdable in French)
    if 'CLOUTABLE' in name.upper() or 'STUDDABLE' in name.upper():
        result['studdable'] = 'yes'
    
    # Extract size (pattern like P225/65R17, 205/55R16, etc.)
    size_match = re.search(r'(P?\d{3}/\d{2}R\d{2})', name)
    if size_match:
        result['size'] = size_match.group(1)
    
    # Remove "Size:" prefix if present
    if result['size'].startswith('Size:'):
        result['size'] = result['size'].replace('Size:', '').strip()
    
    return result

def normalize_price(price_str):
    """Normalize price by removing $ and converting comma to period."""
    if not price_str:
        return ''
    # Remove $ and whitespace, replace comma with period
    cleaned = price_str.replace('$', '').strip().replace(',', '.')
    return cleaned

def normalize_tire_data(csv_file_path):
    """Read CSV and normalize tire data to specified schema."""
    tires = []
    
    with open(csv_file_path, 'r', encoding='utf-8-sig') as f:
        reader = csv.DictReader(f)
        for row in reader:
            # Parse the tire name to extract components
            parsed = parse_tire_name(row.get('name', ''))
            
            # Build normalized tire object
            tire = {
                'name': row.get('name', ''),
                'brand': parsed['brand'],
                'image': row.get('image', ''),
                'price': normalize_price(row.get('price', '')),
                'size': parsed['size'],
                'type': parsed['type'],
                'model': parsed['model'],
                'load_index': parsed['load_index'],
                'speed_rating': parsed['speed_rating'],
                'studdable': parsed['studdable']
            }
            
            tires.append(tire)
    
    return tires

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python normalize_tires.py <csv_file>")
        sys.exit(1)
    
    csv_file = sys.argv[1]
    normalized_tires = normalize_tire_data(csv_file)
    
    # Output strictly as JSON array
    print(json.dumps(normalized_tires, indent=2, ensure_ascii=False))
