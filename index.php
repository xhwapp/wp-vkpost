<?php
/**
 * Plugin Name: VK Posts Parser
 * Plugin URI: https://example.com/
 * Description: A plugin that parses VK posts and creates WordPress posts from them.
 * Version: 1.0.0
 * Author: John Doe
 * Author URI: https://example.com/
 * License: GPL2
 */

// Define plugin constants
define('VK_POSTS_PARSER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VK_POSTS_PARSER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include plugin files
require_once VK_POSTS_PARSER_PLUGIN_DIR . 'includes/functions.php';
require_once VK_POSTS_PARSER_PLUGIN_DIR . 'includes/settings.php';

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'vk_posts_parser_activate');
register_deactivation_hook(__FILE__, 'vk_posts_parser_deactivate');

// Add plugin settings link to the plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'vk_posts_parser_settings_link');
function vk_posts_parser_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=vk_posts_parser">' . __('Settings', 'vk-posts-parser') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}


