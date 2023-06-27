<?php
// Add plugin settings page to the admin menu
add_action('admin_menu', 'vk_posts_parser_add_settings_page');
function vk_posts_parser_add_settings_page() {
    add_options_page(
        __('VK Posts Parser Settings', 'vk-posts-parser'),
        __('VK Posts Parser', 'vk-posts-parser'),
        'manage_options',
        'vk_posts_parser',
        'vk_posts_parser_render_settings_page'
    );
}

if (isset($_POST['vk_posts_parser_parse_posts'])) {
    // Parse VK posts
    vk_posts_parser_parse_posts();
    // Redirect to the settings page
    wp_redirect(admin_url('options-general.php?page=vk-posts-parser&parsed=1'));
    exit;
}

// Render the settings page
function vk_posts_parser_render_settings_page() {
    ?>
    
    <div class="wrap">
        <h1><?php _e('VK Posts Parser Settings', 'vk-posts-parser'); ?></h1>
        <?php if (isset($_GET['parsed'])) : ?>
            <div class="updated">
                <p><?php _e('VK posts parsed successfully.', 'vk-posts-parser'); ?></p>
            </div>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php settings_fields('vk_posts_parser_settings'); ?>
            <?php do_settings_sections('vk_posts_parser_settings'); ?>
            <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'vk-posts-parser'); ?>" />
            <?php if (get_option('vk_posts_parser_auto_parse')) : ?>
                <input type="submit" class="button-secondary" name="vk_posts_parser_parse_posts" value="<?php _e('Parse Posts Now', 'vk-posts-parser'); ?>" />
            <?php endif; ?>
        </form>
                    
    </div>
    
    <?php
}

// Register plugin settings
add_action('admin_init', 'vk_posts_parser_register_settings');
function vk_posts_parser_register_settings() {
    register_setting('vk_posts_parser_settings', 'vk_posts_parser_group_id');
    register_setting('vk_posts_parser_settings', 'vk_posts_parser_posts_count');
    register_setting('vk_posts_parser_settings', 'vk_posts_parser_default_image_url');
    register_setting('vk_posts_parser_settings', 'vk_posts_parser_default_category');
    register_setting('vk_posts_parser_settings', 'vk_posts_parser_access_token');
    register_setting('vk_posts_parser_settings', 'vk_posts_parser_auto_parse');
    register_setting('vk_posts_parser_settings', 'vk_posts_parser_auto_parse_interval');

    add_settings_section(
        'vk_posts_parser_auto_parse',
        __('Auto Parse Settings', 'vk-posts-parser'),
        'vk_posts_parser_render_auto_parse_section',
        'vk_posts_parser_settings'
    );
    
    add_settings_field(
        'vk_posts_parser_auto_parse',
        __('Auto Parse', 'vk-posts-parser'),
        'vk_posts_parser_render_auto_parse_field',
        'vk_posts_parser_settings',
        'vk_posts_parser_auto_parse'
    );
    
    add_settings_field(
        'vk_posts_parser_auto_parse_interval',
        __('Auto Parse Interval', 'vk-posts-parser'),
        'vk_posts_parser_render_auto_parse_interval_field',
        'vk_posts_parser_settings',
        'vk_posts_parser_auto_parse'
    );
    add_settings_section(
        'vk_posts_parser_api',
        __('API Settings', 'vk-posts-parser'),
        'vk_posts_parser_render_api_section',
        'vk_posts_parser_settings'
    );
    
    add_settings_field(
        'vk_posts_parser_access_token',
        __('Access Token', 'vk-posts-parser'),
        'vk_posts_parser_render_access_token_field',
        'vk_posts_parser_settings',
        'vk_posts_parser_api'
    );

    add_settings_section(
        'vk_posts_parser_general',
        __('General Settings', 'vk-posts-parser'),
        'vk_posts_parser_render_general_section',
        'vk_posts_parser_settings'
    );

    add_settings_field(
        'vk_posts_parser_group_id',
        __('VK Group ID', 'vk-posts-parser'),
        'vk_posts_parser_render_group_id_field',
        'vk_posts_parser_settings',
        'vk_posts_parser_general'
    );

    add_settings_field(
        'vk_posts_parser_posts_count',
        __('Number of Posts', 'vk-posts-parser'),
        'vk_posts_parser_render_posts_count_field',
        'vk_posts_parser_settings',
        'vk_posts_parser_general'
    );

    add_settings_field(
        'vk_posts_parser_default_image_url',
        __('Default Image URL', 'vk-posts-parser'),
        'vk_posts_parser_render_default_image_url_field',
        'vk_posts_parser_settings',
        'vk_posts_parser_general'
    );

    add_settings_field(
        'vk_posts_parser_default_category',
        __('Default Category', 'vk-posts-parser'),
        'vk_posts_parser_render_default_category_field',
        'vk_posts_parser_settings',
        'vk_posts_parser_general'
    );
}

// Render the general settings section
function vk_posts_parser_render_general_section() {
    echo '<p>' . __('Configure the general settings for the VK Posts Parser plugin.', 'vk-posts-parser') . '</p>';
}

// Render the group ID field
function vk_posts_parser_render_group_id_field() {
    $group_id = get_option('vk_posts_parser_group_id');
    echo '<input type="text" name="vk_posts_parser_group_id" value="' . esc_attr($group_id) . '" />';
}

// Render the posts count field
function vk_posts_parser_render_posts_count_field() {
    $posts_count = get_option('vk_posts_parser_posts_count');
    echo '<input type="number" name="vk_posts_parser_posts_count" value="' . esc_attr($posts_count) . '" />';
}

// Render the default image URL field
function vk_posts_parser_render_default_image_url_field() {
    $default_image_url = get_option('vk_posts_parser_default_image_url');
    echo '<input type="text" name="vk_posts_parser_default_image_url" value="' . esc_attr($default_image_url) . '" />';
}

// Render the default category field
function vk_posts_parser_render_default_category_field() {
    $default_category = get_option('vk_posts_parser_default_category');
    $categories = get_categories();

    echo '<select name="vk_posts_parser_default_category">';
    foreach ($categories as $category) {
        $selected = ($category->term_id == $default_category) ? 'selected' : '';
        echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
    }
    echo '</select>';
}


// Render the API settings section
function vk_posts_parser_render_api_section() {
    echo '<p>' . __('Configure the API settings for the VK Posts Parser plugin.', 'vk-posts-parser') . '</p>';
}

// Render the access token field
function vk_posts_parser_render_access_token_field() {
    $access_token = get_option('vk_posts_parser_access_token');
    echo '<input type="text" name="vk_posts_parser_access_token" value="' . esc_attr($access_token) . '" />';
}

// Render the auto parse settings section
function vk_posts_parser_render_auto_parse_section() {
    echo '<p>' . __('Configure the auto parse settings for the VK Posts Parser plugin.', 'vk-posts-parser') . '</p>';
}

// Render the auto parse field
function vk_posts_parser_render_auto_parse_field() {
    $auto_parse = get_option('vk_posts_parser_auto_parse');
    echo '<input type="checkbox" name="vk_posts_parser_auto_parse" value="1" ' . checked(1, $auto_parse, false) . ' />';
}

// Render the auto parse interval field
function vk_posts_parser_render_auto_parse_interval_field() {
    $auto_parse_interval = get_option('vk_posts_parser_auto_parse_interval');
    echo '<input type="number" name="vk_posts_parser_auto_parse_interval" value="' . esc_attr($auto_parse_interval) . '" min="1" step="1" /> ' . __('minutes', 'vk-posts-parser');
}
