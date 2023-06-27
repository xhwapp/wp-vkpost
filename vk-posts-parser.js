jQuery(document).ready(function($) {
    // Show a confirmation message when the "Parse Posts" button is clicked
    $('#vk-posts-parser-parse-posts-button').click(function() {
        return confirm('Are you sure you want to parse VK posts?');
    });
});
