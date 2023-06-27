<?php
// Include required files
require_once VK_POSTS_PARSER_PLUGIN_DIR . 'includes/api.php';
require_once VK_POSTS_PARSER_PLUGIN_DIR . 'includes/images.php';

/**
 * Parse VK posts and create WordPress posts from them.
 */
 
function vk_posts_parser_parse_posts() {
    $params = array(
        'owner_id' => '-' . get_option('vk_posts_parser_group_id'),
        'count' => get_option('vk_posts_parser_posts_count')
    );

    $posts = vk_posts_parser_get_api_response('wall.get', $params);

    if (!$posts) {
        return;
    }

    // Get the default image URL from the plugin settings
    $default_image_url = get_option('vk_posts_parser_default_image_url');

    foreach ($posts->items as $post) {
        $post_id = $post->id;
        $post_date = date('Y-m-d H:i:s', $post->date);
        $post_title = substr($post->text, 0, 30) . '...'; // Shorten the title to 30 characters
        $post_content = $post->text;
        $post_category = get_option('vk_posts_parser_default_category');

        // Check if the post already exists by title
        $existing_post = get_page_by_title($post_title, OBJECT, 'post');

        if ($existing_post) {
            continue;
        }

        // Create the post
        $post_data = array(
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_date' => $post_date,
            'post_category' => array($post_category),
            'post_status' => 'publish'
        );

        $post_id = wp_insert_post($post_data);

        // Add the VK post ID as post meta
        add_post_meta($post_id, 'vk_post_id', $post_id);

        // Upload and attach any images in the post
        $attachment_ids = array();
        preg_match_all('/(https?:\/\/\S+\.(?:jpg|jpeg|png|gif))/i', $post_content, $matches);

        if ($matches) {
            foreach ($matches[1] as $image_url) {
                $attachment_id = vk_posts_parser_upload_image($image_url, $post_id);

                if ($attachment_id) {
                    $attachment_ids[] = $attachment_id;
                    $post_content = str_replace($image_url, wp_get_attachment_url($attachment_id), $post_content);
                }
            }

            // Update the post content with the new image URLs
            wp_update_post(array(
                'ID' => $post_id,
                'post_content' => $post_content
            ));
        }

        // If there are no images, set the default image as the post thumbnail
        if (empty($attachment_ids) && $default_image_url) {
            $attachment_id = vk_posts_parser_upload_image($default_image_url, $post_id);

            if ($attachment_id) {
                $attachment_ids[] = $attachment_id;
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        // Create a gallery from the attached images
        if (!empty($attachment_ids)) {
            $gallery_shortcode = '[gallery ids="' . implode(',', $attachment_ids) . '"]';
            $gallery_html = do_shortcode($gallery_shortcode);

            // Append the gallery to the post content
            $post_content .= $gallery_html;

            // Update the post content with the new gallery
            wp_update_post(array(
                'ID' => $post_id,
                'post_content' => $post_content
            ));

            // Set the first image as the post thumbnail
            set_post_thumbnail($post_id, $attachment_ids[0]);
        }
    }
}

/**
 * Activate the plugin.
 */
function vk_posts_parser_activate() {
    // Set default plugin options
    add_option('vk_posts_parser_group_id', '');
    add_option('vk_posts_parser_posts_count', 10);
    add_option('vk_posts_parser_default_image_url', '');
    add_option('vk_posts_parser_default_category', 1);

    // Schedule the post parsing event
    wp_schedule_event(time(), 'hourly', 'vk_posts_parser_parse_posts');
}

/**
 * Deactivate the plugin.
 */
function vk_posts_parser_deactivate() {
    // Unschedule the post parsing event
    wp_clear_scheduled_hook('vk_posts_parser_parse_posts');
}
