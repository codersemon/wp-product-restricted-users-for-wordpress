<?php
/*
Plugin Name: Product Visiblity Control for Users
Description: It will work as a addons for Dokan plugin. which allow to select user who are able to see and purchase a product. It will works with dokan frontend product upload and edit page.
Version: 1.0.4
Author: Emon Khan
Author URI: https://emonkhan.me
Plugin URI: https://devswizard.com/plugins
*/

/**
 * Dependency Plugin 
 */
// https://wordpress.org/plugins/woo-products-restricted-users/



 /***
  * UPDATED CODE START HERE
  */

  function dokan_custom_product_fields()
  {
      global $post;
  
      $wpru_enable = get_post_meta($post->ID, 'wpru_enable', true);
      $selected_groups = get_post_meta($post->ID, 'wpru_groups', true);
  
      // Display additional option based on user meta 'group_for_user'
      $all_users = get_users();
      $group_options = array();
      foreach ($all_users as $user) {
          $group = get_user_meta($user->ID, 'group_for_user', true);
          if (!empty($group) && !in_array($group, $group_options)) {
              $group_options[] = $group;
          }
      }
  
      echo '<div class="dokan-form-group">';
      echo '<label for="wpru_enable" class="devswizard-label">' . __('Product Visible To User Control', 'dokan-lite') . '</label>';
      echo '<select name="wpru_enable" class="dokan-form-control user-selection-control">';
      echo '<option value="1" ' . selected($wpru_enable, '1', false) . '>' . __('Enable', 'dokan-lite') . '</option>';
      echo '<option value="" ' . selected($wpru_enable, '', false) . '>' . __('Disable', 'dokan-lite') . '</option>';
      echo '</select>';
      echo '</div>';
  
      // Display the group select field allowing multiple selection
      echo '<div class="dokan-form-group user-selection-wrap">';
      echo '<label for="wpru_groups" class="devswizard-label">' . __('Select User Groups', 'dokan-lite') . '</label>';
      echo '<select name="wpru_groups[]" class="dokan-form-control user-selection" multiple>';
      
      // Add the "Select All" option
      echo '<option value="select_all">' . __('Select All', 'dokan-lite') . '</option>';
  
      foreach ($group_options as $group) {
          $selected = in_array($group, (array) $selected_groups) ? 'selected' : '';
          echo '<option value="' . esc_attr($group) . '" ' . $selected . '>' . esc_html($group) . '</option>';
      }
      echo '</select>';
      echo '</div>';
  }
  add_action('dokan_product_edit_after_product_tags', 'dokan_custom_product_fields');
  add_action('dokan_new_product_after_product_tags', 'dokan_custom_product_fields');
  add_action('dokan_auction_before_general_options', 'dokan_custom_product_fields');
  
  // Save product visibility meta
  function dokan_save_custom_product_fields($product_id)
  {
      if (isset($_POST['wpru_enable'])) {
          update_post_meta($product_id, 'wpru_enable', sanitize_text_field($_POST['wpru_enable']));
      }
  
      // Save selected groups
      if (isset($_POST['wpru_groups'])) {
          update_post_meta($product_id, 'wpru_groups', array_map('sanitize_text_field', $_POST['wpru_groups']));
  
          // Gather user IDs associated with the selected groups
          $group_users = array();
          global $wpdb;
          foreach ($_POST['wpru_groups'] as $group) {
              $group_users_query = $wpdb->get_col($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'group_for_user' AND meta_value = %s", $group));
              $group_users = array_merge($group_users, $group_users_query);
          }
  
          // Add the current user's ID to the selected users array
          $current_user_id = get_current_user_id();
          if (!in_array($current_user_id, $group_users)) {
              $group_users[] = $current_user_id;
          }
  
          // Save selected users
          update_post_meta($product_id, 'wpru_users', array_unique($group_users));
      } else {
          // If no group is selected, make sure 'wpru_groups' and 'wpru_users' are not present in the meta
          delete_post_meta($product_id, 'wpru_groups');
          delete_post_meta($product_id, 'wpru_users');
      }
  }
  add_action('dokan_process_product_meta', 'dokan_save_custom_product_fields');
  add_action('dokan_update_auction_product', 'dokan_save_custom_product_fields');
  
  
  /**
   * UPDATED CODE END HERE
   */





// Add custom fields to product edit and upload page
function products_custom_data()
{
    global $post;

    $brand_name                     = get_post_meta($post->ID, 'brand_name', true);
    $ean_code_cu                    = get_post_meta($post->ID, 'ean_code_cu', true);
    $ean_code_su                    = get_post_meta($post->ID, 'ean_code_su', true);
    $number_of_products_on_pallet   = get_post_meta($post->ID, 'number_of_products_on_pallet', true);
    $number_of_products_on_layer    = get_post_meta($post->ID, 'number_of_products_on_layer', true);
    $recommended_consumer_price     = get_post_meta($post->ID, 'recommended_consumer_price', true);
    $goods_location                 = get_post_meta($post->ID, 'goods_location', true);
    $pick_up_or_delivery_by_seller  = get_post_meta($post->ID, 'pick_up_or_delivery_by_seller', true);
    $timing_of_pick_up_or_delivery  = get_post_meta($post->ID, 'timing_of_pick_up_or_delivery', true);
    $additional_information         = get_post_meta($post->ID, 'additional_information', true);
    $best_before_date_batch         = get_post_meta($post->ID, 'best_before_date_batch', true);
    $language_on_packaging          = get_post_meta($post->ID, 'language_on_packaging', true);
    $items_case                        = get_post_meta($post->ID, 'items_case', true);
    $additional_information         = get_post_meta($post->ID, 'additional_information', true);

    // Display brand name field
    echo '<div class="dokan-form-group">';
    echo '<label for="brand_name" class="devswizard-label">' . __('Brand Name', 'dokan-lite') . '</label>';
    echo '<input type="text" name="brand_name" class="dokan-form-control" value="' . esc_attr($brand_name) . '">';
    echo '</div>';

    // Display EAN code (CU) field
    echo '<div class="dokan-form-group">';
    echo '<label for="ean_code_cu" class="devswizard-label">' . __('EAN Code (CU)', 'dokan-lite') . '</label>';
    echo '<input type="text" name="ean_code_cu" class="dokan-form-control" value="' . esc_attr($ean_code_cu) . '">';
    echo '</div>';

    // Display EAN code (SU) field
    echo '<div class="dokan-form-group">';
    echo '<label for="ean_code_su" class="devswizard-label">' . __('EAN Code (SU)', 'dokan-lite') . '</label>';
    echo '<input type="text" name="ean_code_su" class="dokan-form-control" value="' . esc_attr($ean_code_su) . '">';
    echo '</div>';

    // Display number of products on pallet field
    echo '<div class="dokan-form-group">';
    echo '<label for="number_of_products_on_pallet" class="devswizard-label">' . __('Number of Products on Pallet', 'dokan-lite') . '</label>';
    echo '<input type="text" name="number_of_products_on_pallet" class="dokan-form-control" value="' . esc_attr($number_of_products_on_pallet) . '">';
    echo '</div>';

    // Display number of products on layer field
    echo '<div class="dokan-form-group">';
    echo '<label for="number_of_products_on_layer" class="devswizard-label">' . __('Number of Products on Layer', 'dokan-lite') . '</label>';
    echo '<input type="text" name="number_of_products_on_layer" class="dokan-form-control" value="' . esc_attr($number_of_products_on_layer) . '">';
    echo '</div>';

    // Display recommended consumer price field
    echo '<div class="dokan-form-group">';
    echo '<label for="recommended_consumer_price" class="devswizard-label">' . __('Recommended Consumer Price', 'dokan-lite') . '</label>';
    echo '<input type="text" name="recommended_consumer_price" class="dokan-form-control" value="' . esc_attr($recommended_consumer_price) . '">';
    echo '</div>';

    // Display best before text batch field
    echo '<div class="dokan-form-group">';
    echo '<label for="best_before_date_batch" class="devswizard-label">' . __('Best Before Date Batch', 'dokan-lite') . '</label>';
    echo '<input type="text" name="best_before_date_batch" class="dokan-form-control" value="' . esc_attr($best_before_date_batch) . '">';
    echo '</div>';

    // Display goods location field
    echo '<div class="dokan-form-group">';
    echo '<label for="goods_location" class="devswizard-label">' . __('Goods Location', 'dokan-lite') . '</label>';
    echo '<input type="text" name="goods_location" class="dokan-form-control" value="' . esc_attr($goods_location) . '">';
    echo '</div>';

    // Display pick-up or delivery by seller field (select)
    echo '<div class="dokan-form-group">';
    echo '<label for="pick_up_or_delivery_by_seller" class="devswizard-label">' . __('Pick-up or Delivery by Seller', 'dokan-lite') . '</label>';
    echo '<select name="pick_up_or_delivery_by_seller" class="dokan-form-control">';
    echo '<option value="Pick-up" ' . selected($pick_up_or_delivery_by_seller, 'Pick-up', false) . '>' . __('Pick-up', 'dokan-lite') . '</option>';
    echo '<option value="Delivery" ' . selected($pick_up_or_delivery_by_seller, 'Delivery', false) . '>' . __('Delivery', 'dokan-lite') . '</option>';
    echo '</select>';
    echo '</div>';

    // Display timing of pick-up or delivery field (time input)
    echo '<div class="dokan-form-group">';
    echo '<label for="timing_of_pick_up_or_delivery" class="devswizard-label">' . __('Timing of Pick-up or Delivery', 'dokan-lite') . '</label>';
    echo '<input type="date" name="timing_of_pick_up_or_delivery" class="dokan-form-control" value="' . esc_attr($timing_of_pick_up_or_delivery) . '">';
    echo '</div>';

    // Display language_on_packaging field (text input)
    echo '<div class="dokan-form-group">';
    echo '<label for="language_on_packaging" class="devswizard-label">' . __('Language on packaging', 'dokan-lite') . '</label>';
    echo '<input type="text" name="language_on_packaging" class="dokan-form-control" value="' . esc_attr($language_on_packaging) . '">';
    echo '</div>';

    // Display items_case field (text input)
    echo '<div class="dokan-form-group">';
    echo '<label for="items_case" class="devswizard-label">' . __('Items in case', 'dokan-lite') . '</label>';
    echo '<input type="text" name="items_case" class="dokan-form-control" value="' . esc_attr($items_case) . '">';
    echo '</div>';

    // Display additional information field
    echo '<div class="dokan-form-group">';
    echo '<label for="additional_information" class="devswizard-label">' . __('Additional Information', 'dokan-lite') . '</label>';
    echo '<textarea name="additional_information" class="dokan-form-control">' . esc_textarea($additional_information) . '</textarea>';
    echo '</div>';
}
add_action('dokan_product_edit_after_product_tags', 'products_custom_data');
add_action('dokan_new_product_after_product_tags', 'products_custom_data');
add_action('dokan_auction_before_general_options', 'products_custom_data');

// Save custom fields values
function save_products_custom_data($product_id)
{
    // Use isset to check if the fields are set in $_POST
    if (isset($_POST['brand_name'])) {
        update_post_meta($product_id, 'brand_name', sanitize_text_field($_POST['brand_name']));
    }

    if (isset($_POST['ean_code_cu'])) {
        update_post_meta($product_id, 'ean_code_cu', sanitize_text_field($_POST['ean_code_cu']));
    }

    if (isset($_POST['ean_code_su'])) {
        update_post_meta($product_id, 'ean_code_su', sanitize_text_field($_POST['ean_code_su']));
    }

    if (isset($_POST['number_of_products_on_pallet'])) {
        update_post_meta($product_id, 'number_of_products_on_pallet', sanitize_text_field($_POST['number_of_products_on_pallet']));
    }

    if (isset($_POST['number_of_products_on_layer'])) {
        update_post_meta($product_id, 'number_of_products_on_layer', sanitize_text_field($_POST['number_of_products_on_layer']));
    }

    if (isset($_POST['recommended_consumer_price'])) {
        update_post_meta($product_id, 'recommended_consumer_price', sanitize_text_field($_POST['recommended_consumer_price']));
    }

    if (isset($_POST['best_before_date_batch'])) {
        update_post_meta($product_id, 'best_before_date_batch', sanitize_text_field($_POST['best_before_date_batch']));
    }

    if (isset($_POST['goods_location'])) {
        update_post_meta($product_id, 'goods_location', sanitize_text_field($_POST['goods_location']));
    }

    if (isset($_POST['pick_up_or_delivery_by_seller'])) {
        update_post_meta($product_id, 'pick_up_or_delivery_by_seller', sanitize_text_field($_POST['pick_up_or_delivery_by_seller']));
    }

    if (isset($_POST['timing_of_pick_up_or_delivery'])) {
        update_post_meta($product_id, 'timing_of_pick_up_or_delivery', sanitize_text_field($_POST['timing_of_pick_up_or_delivery']));
    }

    if (isset($_POST['language_on_packaging'])) {
        update_post_meta($product_id, 'language_on_packaging', sanitize_text_field($_POST['language_on_packaging']));
    }

    if (isset($_POST['items_case'])) {
        update_post_meta($product_id, 'items_case', sanitize_text_field($_POST['items_case']));
    }

    if (isset($_POST['additional_information'])) {
        update_post_meta($product_id, 'additional_information', sanitize_textarea_field($_POST['additional_information']));
    }
}
add_action('dokan_process_product_meta', 'save_products_custom_data');
add_action('dokan_update_auction_product', 'save_products_custom_data');


// Display meta field data under product single summary 
function display_custom_fields_on_product_page()
{
    global $product;

    // Get product ID
    $product_id = $product->get_id();

    // Get custom field values
    $brand_name                     = get_post_meta($product_id, 'brand_name', true);
    $ean_code_cu                    = get_post_meta($product_id, 'ean_code_cu', true);
    $ean_code_su                    = get_post_meta($product_id, 'ean_code_su', true);
    $number_of_products_on_pallet   = get_post_meta($product_id, 'number_of_products_on_pallet', true);
    $number_of_products_on_layer    = get_post_meta($product_id, 'number_of_products_on_layer', true);
    $recommended_consumer_price     = get_post_meta($product_id, 'recommended_consumer_price', true);
    $goods_location                 = get_post_meta($product_id, 'goods_location', true);
    $pick_up_or_delivery_by_seller  = get_post_meta($product_id, 'pick_up_or_delivery_by_seller', true);
    $timing_of_pick_up_or_delivery  = get_post_meta($product_id, 'timing_of_pick_up_or_delivery', true);
    $language_on_packaging          = get_post_meta($product_id, 'language_on_packaging', true);
    $items_case                        = get_post_meta($product_id, 'items_case', true);
    $additional_information         = get_post_meta($product_id, 'additional_information', true);
    $best_before_date_batch         = get_post_meta($product_id, 'best_before_date_batch', true);


    // check before adding currency 
    if (!empty($recommended_consumer_price)) {
        $recommended_consumer_price = get_woocommerce_currency_symbol() . $recommended_consumer_price;
    }

    // Display custom fields in a table
    echo '<div class="custom-fields-section">';
    echo '<table class="custom-fields-table">';

    // Check and display each field only if the data is available
    display_table_row(__('Brand Name', 'dokan-lite'), $brand_name);
    display_table_row(__('EAN Code (CU)', 'dokan-lite'), $ean_code_cu);
    display_table_row(__('EAN Code (SU)', 'dokan-lite'), $ean_code_su);
    display_table_row(__('Number of Products on Pallet', 'dokan-lite'), $number_of_products_on_pallet);
    display_table_row(__('Number of Products on Layer', 'dokan-lite'), $number_of_products_on_layer);
    display_table_row(__('Recommended Consumer Price', 'dokan-lite'), $recommended_consumer_price);
    display_table_row(__('Best Before Date Batch', 'dokan-lite'), $best_before_date_batch);
    display_table_row(__('Goods Location', 'dokan-lite'), $goods_location);
    display_table_row(__('Pick-up or Delivery by Seller', 'dokan-lite'), $pick_up_or_delivery_by_seller);
    display_table_row(__('Timing of Pick-up or Delivery', 'dokan-lite'), date('F j, Y', strtotime($timing_of_pick_up_or_delivery)));
    display_table_row(__('Language on packaging', 'dokan-lite'), $language_on_packaging);
    display_table_row(__('Items in case', 'dokan-lite'), $items_case);
    display_table_row(__('Additional Information', 'dokan-lite'), $additional_information);


    echo '</table>';
    echo '</div>';
}

function display_table_row($label, $value)
{
    if (!empty($value)) {
        echo '<tr>';
        echo '<td>' . esc_html($label) . ':</td>';
        echo '<td>' . esc_html($value) . '</td>';
        echo '</tr>';
    }
}

add_action('woocommerce_single_product_summary', 'display_custom_fields_on_product_page', 11);



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

        .select2-container {
            width: 100% !important;
        }

        @media all and (min-width: 768px) {
            ul.dokan-dashboard-menu {
                display: flex;
                flex-flow: column wrap;
            }

            li.auction {
                order: -7;
            }

            li.products {
                order: -8;
            }

            li.dashboard {
                order: -9;
            }
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            // show hide user selector field based on user selection control field value
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

            // Select init
            $('.user-selection').select2();

            // Handle Select All option
            $('.user-selection').on('select2:select', function(e) {
                if (e.params.data.id === 'select_all') {
                    $(this).find('option').prop('selected', true);
                    $(this).trigger('change');
                }
            });


            // 	Dokan dashboard product upload page product type selection 
            $("#product_type option[value=external], #product_type option[value=variable], #product_type option[value=grouped]").remove();
            $("#product_type option[value=simple]").text('Regular sale');
            $('label[for="product_type"]').text('Listing type');
        });
    </script>
<?php
}
add_action('wp_head', 'my_custom_inline_assets');
