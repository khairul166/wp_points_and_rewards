<?php
function enqueue_custom_admin_style()
{
    // Replace 'your-theme-directory' with the actual path to your CSS file.
    $css_url = get_template_directory_uri() . '/reward-point/css/custom-modal.css';

    // Enqueue the stylesheet.
    wp_enqueue_style('custom-admin-style', $css_url);
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_style');



function enqueue_admin_point_adjust_script()
{
    // Check if we are on the correct admin page
    if (isset($_GET['page']) && $_GET['page'] === 'points-rewards' && isset($_GET['tab']) && $_GET['tab'] === 'point-settings') {
        // Enqueue the JavaScript file
        wp_enqueue_script('admin-point-adjust', get_template_directory_uri() . '/reward-point/js/admin-point-adjust.js', array('jquery'), null, true);

        wp_localize_script(
            'admin-point-adjust',
            'adminPointAdjustData',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                // This sets the ajaxurl variable
                'verificationCode' => esc_js(get_option('verification_code', '')),
                'mailSendReport' => '',
            )
        );
    }
}

add_action('admin_enqueue_scripts', 'enqueue_admin_point_adjust_script');

function custom_enqueue_scripts()
{
    wp_enqueue_script('custom-script', get_template_directory_uri() . '/reward-point/js/custom-script.js', array('jquery'), '1.0', true);

    // Define and localize the ajaxurl, redemption rate, and nonce variables
    wp_localize_script(
        'custom-script',
        'custom_script_params',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'redemption_rate' => get_option('point_conversation_rate_taka', ''),
            // Replace 'point_conversation_rate_taka' with the option name for your conversion rate
            'nonce' => wp_create_nonce('apply_points_redemption_nonce'),
            // Create a nonce for the AJAX request
        )
    );
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');
