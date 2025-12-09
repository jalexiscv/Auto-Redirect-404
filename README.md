# Auto Redirect 404

[![Read in Spanish](    https://img.shields.io/badge/Leer_en-Español-red)](README.es.md)
![License](https://img.shields.io/badge/license-GPLv2-blue.svg)
![Version](https://img.shields.io/badge/version-1.0.6-green.svg)

**Automatically redirect any 404 page to a similar Post based on Title, Post Types, and Taxonomies using 301 or 302 Redirects.**

---

## Description

Welcome to **Auto Redirect 404**!

This plugin automatically redirects 404 pages to similar posts based on Title, Post Types, and Taxonomies. If nothing similar is found, visitors will be redirected to the homepage or a custom URL.

### Features
*   **Automatic 404 Detection**: Catches 404 errors immediately.
*   **Smart Search**: Finds similar posts based on:
    *   Title
    *   Post Type
    *   Taxonomy
*   **Fallback Behavior**: If no match is found, you can:
    *   Redirect to Homepage
    *   Redirect to a Custom URL
    *   Display the default 404 page
*   **Redirect Status**: Choose between **301 (Permanent)** or **302 (Temporary)** redirects.
*   **Exclusions**:
    *   Exclude specific Post Types.
    *   Exclude specific Taxonomies.
    *   Exclude Posts/Terms via custom meta fields.
*   **Debug & Preview**:
    *   Debug Console (for Admins).
    *   Preview redirections from the Admin Panel.

### New Features (v1.0+)
*   **Engines & Groups**: Customize your own searching and matching logic.
*   **Logging**: Log redirections to `/wp-content/debug.log`.
*   **Headers**: Expose `Auto-Redirect-404` headers for debugging.

---

## Installation

1.  Upload the plugin files to the `/wp-content/plugins/auto-redirect-404-similar-post` directory, or install the plugin through the WordPress plugins screen directly.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Go to **Settings > Auto Redirect 404** to configure your preferences.
4.  Done! Try accessing a non-existent URL to test it.

---

## Developer API

Auto Redirect 404 caches and groups logic for extensibility.

### Create a Custom Group
```php
add_action('ar404/search/init', 'my_404_group');
function my_404_group($query){
    ar404_register_group(array(
        'name' => 'My Group',
        'slug' => 'my_group',
        'engines' => array('default_post', 'default_fix_url')
    ));
}
```

### Create a Custom Engine
```php
add_action('ar404/search/init', 'my_404_group_engine');
function my_404_group_engine($query){
    ar404_register_engine(array(
        'name' => 'My Engine',
        'slug' => 'my_engine',
        'weight' => 100,
        'primary' => true
    ));
    // Implementation logic via 'ar404/search/engine/my_engine' filter...
}
```

---

## Frequently Asked Questions

**Is it compatible with other redirection plugins?**
Yes! It works alongside Redirection, RankMath, Yoast, etc. If a manual redirection isn't found, Auto Redirect 404 takes over.

---

## Changelog
**1.0.6**
*   Fix: Escaping priority value in settings.
*   Fix: PHP 8.3 dynamic property deprecation.
*   Bumped WordPress version to 6.8.

*(See full changelog in `readme.txt`)*
