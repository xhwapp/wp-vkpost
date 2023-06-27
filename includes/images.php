<?php
/**
 * Upload an image to WordPress and attach it to a post.
 *
 * @param string $image_url The URL of the image to upload.
 * @param int $post_id The ID of the post to attach the image to.
 * @return int|false The ID of the uploaded image, or false on failure.
 */
function vk_posts_parser_upload_image($image_url, $post_id) {
    $image_data = file_get_contents($image_url);

    if (!$image_data) {
        return false;
    }

    $file_name = basename($image_url);
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['path'] . '/' . $file_name;

    $upload_file = file_put_contents($upload_path, $image_data);

    if (!$upload_file) {
        return false;
    }

    $attachment = array(
        'post_mime_type' => wp_check_filetype($file_name)['type'],
        'post_title' => sanitize_file_name($file_name),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attachment_id = wp_insert_attachment($attachment, $upload_path, $post_id);

    if (!$attachment_id) {
        return false;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';

    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_path);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    return $attachment_id;
}
