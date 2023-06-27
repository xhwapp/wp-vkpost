<?php
/**
 * Get a response from the VK API.
 *
 * @param string $method The API method to call.
 * @param array $params The parameters to pass to the API method.
 * @return mixed The API response, or false on failure.
 */
function vk_posts_parser_get_api_response($method, $params) {
    $access_token = get_option('vk_posts_parser_access_token');

    if (!$access_token) {
        return false;
    }

    $params['access_token'] = $access_token;
    $params['v'] = '5.131';

    $url = 'https://api.vk.com/method/' . $method . '?' . http_build_query($params);

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);

    if (!$body) {
        return false;
    }

    $data = json_decode($body);

    if (!$data) {
        return false;
    }

    if (isset($data->error)) {
        return false;
    }

    return $data->response;
}
