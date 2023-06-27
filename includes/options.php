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
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Group ID', 'vk-posts-parser'); ?></th>
                <td><input type="text" name="vk_posts_parser_group_id" value="<?php echo esc_attr(get_option('vk_posts_parser_group_id')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Posts Count', 'vk-posts-parser'); ?></th>
                <td><input type="number" name="vk_posts_parser_posts_count" value="<?php echo esc_attr(get_option('vk_posts_parser_posts_count')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Default Image URL', 'vk-posts-parser'); ?></th>
                <td><input type="text" name="vk_posts_parser_default_image_url" value="<?php echo esc_attr(get_option('vk_posts_parser_default_image_url')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Default Category', 'vk-posts-parser'); ?></th>
                <td><input type="text" name="vk_posts_parser_default_category" value="<?php echo esc_attr(get_option('vk_posts_parser_default_category')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Auto Parse Posts', 'vk-posts-parser'); ?></th>
                <td><input type="checkbox" name="vk_posts_parser_auto_parse" value="1" <?php checked(1, get_option('vk_posts_parser_auto_parse'), true); ?> /></td>
            </tr>
        </table>
        <?php submit_button(__('Save Changes', 'vk-posts-parser')); ?>
        <?php if (get_option('vk_posts_parser_auto_parse')) : ?>
            <input type="submit" class="button-secondary" name="vk_posts_parser_parse_posts" value="<?php _e('Parse Posts Now', 'vk-posts-parser'); ?>" />
        <?php endif; ?>
    </form>
</div>
