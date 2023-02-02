<?php
/*
 * Plugin Name: Product Scraper
 * Plugin URI: https://www.example.com/product-scraper
 * Description: A plugin that scrapes product information from a specified URL and adds it as new products in WordPress.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://www.example.com
 */

add_action( 'admin_menu', 'product_scraper_menu' );

function product_scraper_menu() {
    add_menu_page( 'Product Scraper', 'Product Scraper', 'manage_options', 'product-scraper', 'product_scraper_options', 'dashicons-admin-generic' );
}

function product_scraper_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
    echo '<h1>Product Scraper</h1>';

    // Check if the form has been submitted
    if (isset($_POST['product_scraper_form_submitted'])) {
        $url = $_POST['product_scraper_url'];
        $html = file_get_contents($url);
        preg_match_all('/<h3 class="product-name">(.*?)<\/h3>/', $html, $product_names);
        preg_match_all('/<span class="product-price">(.*?)<\/span>/', $html, $product_prices);
        preg_match_all('/<img src="(.*?)" class="product-image"/', $html, $product_images);
        preg_match_all('/<p class="product-description">(.*?)<\/p>/', $html, $product_descriptions);

        $products = array();
        for ($i = 0; $i < count($product_names[1]); $i++) {
            $product = array(
                'name' => $product_names[1][$i],
                'price' => $product_prices[1][$i],
                'image_url' => $product_images[1][$i],
                'description' => $product_descriptions[1][$i]
            );
            array_push($products, $product);
        }

        foreach ($products as $product) {
            $post = array(
                'post_title' => $product['name'],
                'post_content' => $product['description'],
                'post_status' => 'publish',
                'post_type' => 'product'
            );
            $post_id = wp_insert_post($post);
            update_post_meta($post_id, '_regular_price', $product['price']);
            update_post_meta($post_id, '_price', $product['price']);
        }
    }

    echo '<form method="post">';
    echo '<table class="form-table">';
$image_url = $product['image_url'];
$image_id = product_scraper_upload_image($image_url, $post_id);
set_post_thumbnail($post_id, $image_id);
}    echo '<div class="notice notice-success is-dismissible"><p>Products successfully added.</p></div>';
}
// Display the form
echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
echo '<table class="form-table">';
echo '<tr>';
echo '<th scope="row">Product URL</th>';
echo '<td><input type="text" name="product_scraper_url" value="' . ( isset( $_POST['product_scraper_url'] ) ? esc_attr( $_POST['product_scraper_url'] ) : '' ) . '" size="50" /></td>';
echo '</tr>';
echo '</table>';
echo '<input type="hidden" name="product_scraper_form_submitted" value="1" />';
echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Scrape Products"></p>';
echo '</form>';
echo '</div>';
}

// Function to upload image
function product_scraper_upload_image($image_url, $post_id) {
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
$image_filename = basename($image_url);
$upload_file = wp_upload_bits($image_filename, null, file_get_contents($image_url));
if (!$upload_file['error']) {
    $wp_filetype = wp_check_filetype($image_filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_parent' => $post_id,
        'post_title' => preg_replace('/\.[^.]+$/', '', $image_filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $post_id);
    if (!is_wp_error($attachment_id)) {
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);
        return $attachment_id;
    }
}
return 0;
}
?>
            $filename = basename($product['image_url']);
            $upload_file = wp_upload_bits($filename, null, file_get_contents($product['image_url']));
            if (!$upload_file['error']) {
                $wp_filetype = wp_check_filetype($filename, null);
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name($filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attachment_id = wp_insert_attachment($attachment, $upload_file['file'], $post_id);
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                set_post_thumbnail($post_id, $attachment_id);
            }
        }

        echo '<div id="message" class="notice notice-success is-dismissible">';
        echo '<p>Scraping completed successfully!</p>';
        echo '<button type="button" class="notice-dismiss">';
        echo '<span class="screen-reader-text">Dismiss this notice.</span>';
        echo '</button>';
        echo '</div>';
    }

    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th scope="row">';
    echo '<label for="product_scraper_url">Product URL</label>';
    echo '</th>';
    echo '<td>';
    echo '<input type="text" id="product_scraper_url" name="product_scraper_url" value="" class="regular-text" />';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<p class="submit">';
    echo '<input type="submit" name="Submit" class="button-primary" value="Scrape Products" />';
    echo '<input type="hidden" name="product_scraper_form_submitted" value="Y">';
    echo '</p>';
    echo '</form>';
    echo '</div>';
}
        }
    }

    echo '<form action="" method="post">';
    echo '<table class="form-table">';
    echo '<tr><th scope="row">URL to scrape</th><td><input type="text" name="product_scraper_url" value=""></td></tr>';
    echo '</table>';
    echo '<input type="hidden" name="product_scraper_form_submitted" value="1">';
    echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Scrape Products"></p>';
    echo '</form>';
    echo '</div>';
}
?>
