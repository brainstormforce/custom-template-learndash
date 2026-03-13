# Custom Template for LearnDash

## Project Overview

Custom Template for LearnDash is a WordPress plugin by Brainstorm Force. Replace default LearnDash course template for non-enrolled students with a custom page-builder template.

- **Version:** 1.0.7
- **Text Domain:** custom-template-learndash
- **Main File:** custom-template-learndash.php
- **Requires:** WordPress, PHP 5.6+

## Tech Stack

- **Language:** PHP
- **Platform:** WordPress
- **Build:** Grunt (i18n, readme conversion)
- **Coding Standards:** PHPCS with WordPress standards

## Commands

```bash
# Install dependencies
npm install
composer install

# Build (i18n + readme)
npx grunt

# Generate translations
npx grunt i18n

# Convert readme.txt to README.md
npx grunt readme

# Run PHPCS
./vendor/bin/phpcs .
```

## Architecture

This is a WordPress plugin following standard WordPress patterns:
- Entry point: `custom-template-learndash.php`
- Constants defined for version, file path, base, dir, and URI
- Classes loaded via `after_setup_theme` or `plugins_loaded` hook
- Follows WordPress Coding Standards (WPCS)

## Conventions

- Use WordPress hooks (`add_action`, `add_filter`) for extensibility
- Prefix all functions and classes to avoid conflicts
- Use `custom-template-learndash` text domain for all translatable strings
- Escape all output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses`)
- Sanitize all input (`sanitize_text_field`, `absint`, etc.)
- Use nonces for form submissions and AJAX requests
- Follow WordPress PHP coding standards
