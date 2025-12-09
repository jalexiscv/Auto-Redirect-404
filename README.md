# Auto Redirect 404

[![Read in Spanish](https://img.shields.io/badge/Leer_en-Español-red)](README.es.md)
![License](https://img.shields.io/badge/license-GPLv2-blue.svg)
![Version](https://img.shields.io/badge/version-1.0.6-green.svg)
![Tested Up To](https://img.shields.io/badge/tested_up_to-WordPress_6.8-brightgreen.svg)

**The ultimate solution for managing 404 errors in WordPress. Automatically redirect lost visitors to similar content using smart matching algorithms.**

---

## 📖 Introduction

**Auto Redirect 404** is a robust, performance-oriented WordPress plugin designed to improve user experience (UX) and Search Engine Optimization (SEO). When a visitor encounters a "Page Not Found" (404) error, this plugin steps in to analyze the requested URL and intelligently redirect them to the most relevant existing content on your site.

Instead of losing a visitor to a dead end, Auto Redirect 404 seamlessly guides them to the post, page, or term they were likely looking for, reducing bounce rates and retaining traffic.

---

## 🚀 Key Features

### 🧠 Intelligent Matching Engine
The core of Auto Redirect 404 is its advanced search algorithm, which factors in:
*   **Title Analysis**: Scans your posts for matching keywords found in the 404 URL.
*   **Post Type Context**: Detects if the URL follows a specific post type structure.
*   **Taxonomy Logic**: Identifies potential categories or tags to find related content.

### ⚙️ Customizable Behavior (Fallback)
You have full control over what happens when no similar content is found:
*   **Homepage Redirect**: Send visitors to your main page.
*   **Custom URL**: Define a specific landing page (e.g., a custom search page or sitemap).
*   **Default 404**: maintain standard behavior if preferred.

### 🛠️ Technical Capabilities
*   **Status Codes**: Choose between **301 (Permanent Moved)** for SEO value or **302 (Found)** for temporary changes.
*   **Exclusion Rules**: Prevent specific Post Types or Taxonomies from ever being redirect targets.
*   **Meta Control**: use the `ar404_no_redirect` meta field to exclude specific posts or terms individually.
*   **Logging**: Keep a detailed audit trail of every redirection in your `/wp-content/debug.log` file.
*   **Non-Intrusive**: Optimizes for speed and saves **zero** useless data to your database tables.

---

## 💾 Installation & Configuration

1.  **Download & Install**: 
    *   Upload the plugin folder to `/wp-content/plugins/auto-redirect-404-similar-post`.
    *   Or install directly via the WordPress Plugins dashboard.
2.  **Activate**: Enable the plugin.
3.  **Configure**: Navigate to **Settings > Auto Redirect 404**.
    *   Set your preferred Fallback behavior.
    *   Review exclusion settings if necessary.
4.  **Test**: Visit a non-existent URL (e.g., `yourdomain.com/testing-missing-page`) to see the magic happen.

---

## 💻 Developer API

For advanced users and developers, Auto Redirect 404 offers a comprehensive API to hook into its logic, create custom search engines, or modify redirect behaviors programmatically.

👉 **[Read the Full Developer Documentation](docs/DEVELOPER.md)**

*   Create Custom Search Groups
*   Register New Search Engines
*   Modify Fire Sequences
*   Hook into Redirection Events

---

## 🤝 Support & Contributions

We welcome contributions to improve Auto Redirect 404!
Please review our **[Contribution Guidelines](CONTRIBUTING.md)** (Coming Soon).

If you encounter any issues, please check the [Support Forums](https://wordpress.org/support/plugin/auto-redirect-404-similar-post/) or open an issue on GitHub.

---

## 👨‍💻 Author

**Jose Alexis Correa Valencia**  
*Full Stack Developer & Software Architect*

*   **GitHub**: [@jalexiscv](https://github.com/jalexiscv)
*   **Email**: jalexiscv@gmail.com
*   **Location**: Colombia

---

## ❤️ Donations

If this plugin has helped you or your business, please consider breaking a small donation to support its continued development and maintenance.

| Method | Details |
| :--- | :--- |
| **PayPal** | [jalexiscv@gmail.com](https://paypal.me/jalexiscv) |
| **Nequi (Colombia)** | `3117977281` |

*Thank you for your support!*
