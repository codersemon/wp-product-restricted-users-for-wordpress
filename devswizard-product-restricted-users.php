<?php
/*
Plugin Name: Product Visiblity Control for Users
Description: It will work as a addons for Dokan plugin. which allow to select user who are able to see and purchase a product. It will works with dokan frontend product upload and edit page.
Version: 1.0
Author: Emon Khan
Author URI: https://emonkhan.me
Plugin URI: https://devswizard.com/plugins
*/

/**
 * Dependency Plugin 
 */
// https://wordpress.org/plugins/woo-products-restricted-users/


/**
 * Add custom fields to product edit and upload page
 */
function dokan_custom_product_fields()
{
    global $post;

    $wpru_enable = get_post_meta($post->ID, 'wpru_enable', true);
    $wpru_users  = get_post_meta($post->ID, 'wpru_users', true);

    // Display visibility enable / disable field 
    echo '<div class="dokan-form-group">';
    echo '<label for="wpru_enable" class="devswizard-label">' . __('Product Visible To User Control', 'dokan-lite') . '</label>';
    echo '<select name="wpru_enable" class="dokan-form-control user-selection-control">';
    echo '<option value="1" ' . selected($wpru_enable, '1', false) . '>' . __('Enable', 'dokan-lite') . '</option>';
    echo '<option value="" ' . selected($wpru_enable, '', false) . '>' . __('Disable', 'dokan-lite') . '</option>';
    echo '</select>';
    echo '</div>';

    // Hidden field
    echo '<input type="hidden" name="wpru_mode" value="view">';

    // Display the user select field
    $all_users = get_users();
    echo '<div class="dokan-form-group user-selection-wrap">';
    echo '<label for="wpru_users" class="devswizard-label">' . __('Select Users', 'dokan-lite') . '</label>';
    echo '<select name="wpru_users[]" class="dokan-form-control user-selection" multiple>';
    foreach ($all_users as $user) {
        $selected = in_array($user->ID, (array) $wpru_users) ? 'selected' : '';
        echo '<option value="' . esc_attr($user->ID) . '" ' . $selected . '>' . esc_html($user->first_name) . ' (' . esc_html($user->user_email) . ')</option>';
    }
    echo '</select>';
    echo '</div>';
}
add_action('dokan_product_edit_after_product_tags', 'dokan_custom_product_fields');
add_action('dokan_new_product_after_product_tags', 'dokan_custom_product_fields');

// Save custom fields values
function dokan_save_custom_product_fields($product_id)
{
    if (isset($_POST['wpru_enable'])) {
        update_post_meta($product_id, 'wpru_enable', sanitize_text_field($_POST['wpru_enable']));
    }

    // Save 'wpru_mode' only when 'wpru_enable' is set to 1
    if (isset($_POST['wpru_enable']) && $_POST['wpru_enable'] == '1' && isset($_POST['wpru_mode'])) {
        update_post_meta($product_id, 'wpru_mode', sanitize_text_field($_POST['wpru_mode']));
    } else {
        update_post_meta($product_id, 'wpru_mode', '');
    }

    // Save 'wpru_users' with an array of selected user IDs
    if (isset($_POST['wpru_users'])) {
        $selected_users = array_map('absint', $_POST['wpru_users']);
        update_post_meta($product_id, 'wpru_users', $selected_users);
    } else {
        // If no users are selected, make sure 'wpru_users' is not present in the meta
        delete_post_meta($product_id, 'wpru_users');
    }
}
add_action('dokan_process_product_meta', 'dokan_save_custom_product_fields');


// Vendor Dashboard Products to Regular sale nav menu renamed 
function get_dashboard_nav( $menus ) {
    $custom_menus = [
        'products' => [
            'title' => __( 'Regular sale', 'dokan-lite' ),
            'icon'  => '<i class="fas fa-briefcase"></i>',
            'url'   => dokan_get_navigation_url( 'products' ),
            'pos'   => 10,
        ],
    ];

    return array_merge( $menus, $custom_menus );
}

add_filter( 'dokan_get_dashboard_nav', 'get_dashboard_nav' );



/**
 * Scripts and CSS
 */
// Output CSS and script in the head
function my_custom_inline_assets()
{
?>
    <style>
        label.devswizard-label {
            display: block;
            font-weight: bold;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            // Bind the change event handler
            $('.user-selection-control').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue == '1') {
                    $('.user-selection-wrap').show();
                } else {
                    $('.user-selection-wrap').hide();
                }
            });

            // Trigger the change event on page load
            $('.user-selection-control').trigger('change');

            $('.user-selection').select2();
        });
    </script>
<?php
}
add_action('wp_head', 'my_custom_inline_assets');
