# Developer API Documentation

**Auto Redirect 404** is built with extensibility in mind. Uses a modular "Group & Engine" architecture, allowing developers to inject custom search logic, prioritize specific engines, or completely alter the redirection flow.

---

## Core Concepts

### Engines
An **Engine** is a single unit of search logic. For example, the `default_post` engine searches for a WordPress Post that matches the keywords in the URL.

### Groups
A **Group** is a collection of Engines arranged in a specific "Fire Sequence". When a 404 occurs, the plugin selects a Group (usually the 'default' group) and executes its engines in order until a match is found.

---

## Code Examples

### 1. Registering a Custom Group

You can define a new group with a specific set of engines.

```php
add_action('ar404/search/init', 'register_my_custom_group');

function register_my_custom_group($query) {
    ar404_register_group(array(
        'name'    => 'E-commerce Group',
        'slug'    => 'ecommerce_group',
        'engines' => array(
            'product_sku_search', // Custom engine
            'default_post',       // Built-in engine
            'default_term'        // Built-in engine
        )
    ));
}
```

### 2. Registering a Custom Engine

Create logical units that search for content in specific ways (e.g., searching by SKU, searching an external API, etc.).

```php
add_action('ar404/search/init', 'register_sku_engine');

function register_sku_engine($query) {
    ar404_register_engine(array(
        'name'    => 'Product SKU Search',
        'slug'    => 'product_sku_search',
        'weight'  => 100, // Score multiplier if found
        'primary' => true // Stop searching if this engine finds a match
    ));
}

// Define the logic
add_filter('ar404/search/engine/product_sku_search', 'logic_sku_search', 10, 3);

function logic_sku_search($result, $query, $group) {
    // Extract keywords from URL
    $potential_sku = $query['request']['keywords']['all'];

    // Custom DB query to find product by SKU
    $product_id = my_custom_find_product_by_sku($potential_sku);

    if ($product_id) {
        return array(
            'score' => 100,
            'url'   => get_permalink($product_id),
            'why'   => "Found product with matching SKU: $potential_sku"
        );
    }

    return false; // Nothing found
}
```

### 3. Triggering a Specific Group

You can conditionally switch which Group is used based on the requested URL.

```php
add_filter('ar404/search/group', 'trigger_ecommerce_group', 10, 2);

function trigger_ecommerce_group($group, $query) {
    // If the URL contains '/shop/' or '/product/', use our custom group
    if (strpos($query['request']['url'], '/shop/') !== false) {
        return 'ecommerce_group';
    }
    return $group; // Return default
}
```

---

## Available Hooks

### Actions

*   `ar404/search/init` - Fires during plugin initialization. Use this to register groups and engines.
*   `ar404/after_redirect` - Fires immediately after a redirection is performed (useful for tracking).

### Filters

*   `ar404/init` - Return `false` to completely disable the plugin for the current request.
*   `ar404/search/group` - Modify which Group is selected for the current request.
*   `ar404/search/query` - Modify the parsed query arguments before search begins.
*   `ar404/search/results` - Filter the final results before the winner is picked.
*   `ar404/search/redirect` - Last chance to modify the redirection URL or headers.

---
[Return to README](../README.md)
