<?php
require_once( __DIR__ . '/inc/functions-shortcode.php');
require_once( __DIR__ . '/inc/functions-woocommerce.php');
require_once( __DIR__ . '/inc/functions-cpt.php');

add_action('wp_enqueue_scripts', 'your_theme_enqueue_styles');
function your_theme_enqueue_styles() {
    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array($parent_style), wp_get_theme()->get('Version'));
    wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/css/custom-style.css', array($parent_style, 'child-style'), strtotime(date('d-m-Y H:i')));

    if (is_account_page()) {
      wp_enqueue_style( 'account-style', get_stylesheet_directory_uri() . '/css/account.css', array($parent_style, 'child-style'), strtotime(date('d-m-Y H:i')));
    }

    wp_enqueue_script('main', get_stylesheet_directory_uri() . '/js/main.js', array('jquery'), strtotime(date('d-m-Y H:i')), true);
}



/**
 * Add Custom Admin Bar Menu Link to clean divi cache
 */
if (!function_exists('pac_misc_csc_maybe_admin_bar_link')):
    function pac_misc_csc_maybe_admin_bar_link($admin_bar)
    {
        $admin_bar->add_menu([
            'id' => 'pac_misc_csc',
            'title' => '<span class="ab-icon"></span><span class="ab-label">Clear Divi Cache</span>',
            'href' => '',
            'meta' => [
                'title' => '',
            ],
        ]);
        $admin_bar->add_menu([
            'id' => 'pac_misc_clear_static_css',
            'parent' => 'pac_misc_csc',
            'title' => sprintf('<span data-wpnonce="%1$s">%2$s</span>', wp_create_nonce('pac_misc_clear_static_css'), esc_html('Clear Static CSS File Generation')),
            'href' => 'javascript:void(0)',
        ]);
        $admin_bar->add_menu([
            'id' => 'pac_misc_csc_clear_local_storage',
            'parent' => 'pac_misc_csc',
            'title' => esc_html('Clear Local Storage'),
            'href' => 'javascript:void(0)',
        ]);
    }

    add_action('admin_bar_menu', 'pac_misc_csc_maybe_admin_bar_link', 999);
endif;

/**
 * Add Javascript In Admin Footer
 */
if (!function_exists('pac_misc_csc_maybe_admin_scripts')):
    function pac_misc_csc_maybe_admin_scripts()
    {
        ?>
        <script>
            jQuery(document).ready(function () {
                var adminAaxURL = '<?php echo admin_url('admin-ajax.php'); ?>';
                var isAdmin = '<?php echo is_admin(); ?>';
                // Clear Static CSS
                jQuery("#wp-admin-bar-pac_misc_clear_static_css").click(function (e) {
                    e.preventDefault();
                    jQuery.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: adminAaxURL,
                        data: {
                            'action': 'pac_misc_clear_static_css',
                            '_wpnonce': jQuery(this).find('span').data('wpnonce')
                        },
                        success: function (response) {
                            if (response.success) {
                                let successData = response.data;
                                if (isAdmin) {
                                    let messageHTML = '<div class="notice notice-success pac-misc-message"><p>' + successData + '</p></div>';
                                    if (jQuery('body .wrap h1').length > 0) {
                                        jQuery('body .wrap h1').after(messageHTML);
                                    } else {
                                        jQuery('body #wpbody-content').prepend(messageHTML);
                                    }
                                    setTimeout(function () {
                                        jQuery(".pac-misc-message").remove();
                                    }, 3500);
                                } else {
                                    alert(successData);
                                }
                            }
                        },
                    });
                });
                // Clear Local Storage
                jQuery("#wp-admin-bar-pac_misc_csc_clear_local_storage").click(function (e) {
                    e.preventDefault();
                    let msgText = 'The local storage has been cleared!';
                    window.localStorage.clear();
                    if (isAdmin) {
                        let messageHTML = '<div class="notice notice-success pac-misc-message"><p>' + msgText + '</p></div>';
                        if (jQuery('body .wrap h1').length > 0) {
                            jQuery('body .wrap h1').after(messageHTML);
                        } else {
                            jQuery('body #wpbody-content').prepend(messageHTML);
                        }
                        setTimeout(function () {
                            jQuery(".pac-misc-message").remove();
                        }, 3500);
                    } else {
                        alert(msgText);
                    }
                });
            });
        </script>
        <?php
    }

    add_action('admin_footer', 'pac_misc_csc_maybe_admin_scripts');
    add_action('wp_footer', 'pac_misc_csc_maybe_admin_scripts');
endif;

/**
 * Ajax request
 */
if (!function_exists('pac_misc_csc_maybe_ajax_request')):
    function pac_misc_csc_maybe_ajax_request()
    {
        if ((isset($_POST['action']) && 'pac_misc_clear_static_css' === sanitize_text_field($_POST['action'])) && (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'pac_misc_clear_static_css'))) {
            ET_Core_PageResource::remove_static_resources('all', 'all');
            wp_send_json_success(esc_html('The static CSS file generation has been cleared!'), 200);
        }
    }

    add_action('wp_ajax_pac_misc_clear_static_css', 'pac_misc_csc_maybe_ajax_request');
endif;

// tjohnson - display more variants per pdp admin, will display 200 //
function update_variations_number(){
    return 200; //change your desired number
}
add_filter('woocommerce_admin_meta_boxes_variations_per_page', 'update_variations_number');

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
}
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

/**
 * Disable WooCommerce block styles (front-end).
 */
function themesharbor_disable_woocommerce_block_styles() {
    wp_dequeue_style( 'wc-blocks-style' );
}
add_action( 'wp_enqueue_scripts', 'themesharbor_disable_woocommerce_block_styles' );

/**
 * Disable WooCommerce block styles (front-end).
 */
function unload_plugins_scripts() {
    wp_dequeue_style( 'cookie-law-info' );
    wp_dequeue_style( 'cookie-law-info-gdpr' );

    wp_dequeue_style( 'aftax-frontc' );
    wp_dequeue_script( 'aftax-frontj' );
}
add_action( 'wp_enqueue_scripts', 'unload_plugins_scripts', 100 );

function load_footer_style_script() {
    wp_enqueue_style('cookie-law-info', site_url('/wp-content/plugins/cookie-law-info/legacy/public/css/cookie-law-info-public.css'), array(), '3.0.9', 'all');
    wp_enqueue_style('cookie-law-info-gdpr', site_url('/wp-content/plugins/cookie-law-info/legacy/public/css/cookie-law-info-gdpr.css'), array(), '3.0.9', 'all');

    wp_enqueue_style('aftax-frontc', site_url('/wp-content/plugins/woocommerce-tax-exempt-plugin//assets/css/aftax_front.css'), array(), '1.0', false);
    wp_enqueue_script('aftax-frontj', site_url('/wp-content/plugins/woocommerce-tax-exempt-plugin//assets/js/aftax_front.js'), array(), '1.0', false);

    if(is_front_page()){
        wp_enqueue_style( 'slickcss', get_stylesheet_directory_uri() . '/css/slick.css', array(), 1);
        wp_enqueue_style( 'slick-theme', get_stylesheet_directory_uri() . '/css/slick-theme.css', array('slickcss'), 1);
        wp_enqueue_script('slickjs', get_stylesheet_directory_uri() . '/js/slick.min.js', array(), 1, true);
    }
};
add_action( 'get_footer', 'load_footer_style_script' );

function ws365986_check_request( $query ) {
    if( isset( $_SERVER['REQUEST_URI'] ) && preg_match( '/\/product\//', $_SERVER['REQUEST_URI'], $matches ) ) {
        if( empty( url_to_postid( $_SERVER['REQUEST_URI'] ) ) ) {
            wp_safe_redirect( home_url(), 301 );
            exit();
        }
    }

    return $query;
}
add_filter( 'request', 'ws365986_check_request' );

/**
 * Reorder My Account Menu
 */
function my_account_menu_order($menu_items){

  $reordered_items = array(
      'orders' => __('Orders', 'woocommerce'),
      'edit-address' => __('Addresses', 'woocommerce'),
      'payment-methods' => __('Payment Methods', 'woocommerce'),
      'edit-account' => __('Account Details', 'woocommerce'),
      'tax-exempt' => __('Tax Exemption', 'woocommerce'),
      '../contact-us' => __('Contact Us', 'woocommerce'),
      '../faq' => __('FAQs', 'woocommerce'),
      'customer-logout' => __('Sign Out', 'woocommerce')
  );

  return $reordered_items;
}

add_filter('woocommerce_account_menu_items', 'my_account_menu_order');

//My Account Menus
// Register the first navigation menu for the first section
function register_first_account_menu() {
  register_nav_menu( 'first-account-menu', 'First Account Menu' );
}
add_action( 'after_setup_theme', 'register_first_account_menu' );

// Register the second navigation menu for the second section
function register_second_account_menu() {
  register_nav_menu( 'second-account-menu', 'Second Account Menu' );
}
add_action( 'after_setup_theme', 'register_second_account_menu' );

// Register the third navigation menu for the third section
function register_third_account_menu() {
  register_nav_menu( 'third-account-menu', 'Third Account Menu' );
}
add_action( 'after_setup_theme', 'register_third_account_menu' );





function add_taxes_body_class($classes) {
  // Check if the URL contains '/tax-exempt'
  if (strpos($_SERVER['REQUEST_URI'], '/tax-exempt') !== false) {
      $classes[] = 'woocommerce-taxes';
  }
  return $classes;
}
add_filter('body_class', 'add_taxes_body_class');


add_action( 'woocommerce_after_single_product', 'display_sku_after_product_description_single', 10 );
function display_sku_after_product_description_single ( ){

    $product = wc_get_product( get_the_id() );

    if ( is_a( $product, 'WC_Product' ) ) {

        if ( $product->is_type('variable') ) {
            $sku = '';
            foreach ( $product->get_children() as $child_id ) {
            $variation = wc_get_product( $child_id );
            if ( $variation && $variation->exists() ) $sku .= $variation->get_sku() . ', ';
            }
        } else {
            $sku .= $product->get_sku();
        }

        echo '<div style="display:none;">
        <h3>SKUs available on this product page</h3>
        '.$sku.'
        </div>';

    }

//Elastic Press Enable Search of Product Variation SKUs
function ep_custom_index_and_search_prod_variations( $post_types ) {
	$post_types['product_variation'] = 'product_variation';

	return $post_types;
}
add_filter( 'ep_indexable_post_types', 'ep_custom_index_and_search_prod_variations' );
add_filter( 'ep_searchable_post_types', 'ep_custom_index_and_search_prod_variations' );

function ep_custom_add_sku_field_weighting_prod_variations( $fields, $post_type ) {
	if ( 'product_variation' === $post_type ) {
		$key = 'meta._sku.value';

		$fields['attributes']['children'][ $key ] = array(
			'key'   => $key,
			'label' => __( 'SKU', 'textdomain' ),
		);
	}

	return $fields;
}
add_filter(
	'ep_weighting_fields_for_post_type',
	'ep_custom_add_sku_field_weighting_prod_variations',
	10,
	2
);

}



function woocommerce_output_content_wrapper_end() {
    ob_start();
    if (is_product_category()) {
    global $post;
    global $wp_query;
    $product_cat_id = $wp_query->get_queried_object()->term_id;
    ?>
    <div class="prod-cat-blog-main">
    <?php
     $blog_categories=get_field('blog_categories', 'category_'. $product_cat_id .'');
	 if(!empty( $blog_categories )) :
	    echo '<h2>Related Blog Posts</h2>';

	   $cat_ar = [];
      foreach($blog_categories as $assigned_blog_cat){

        $cat_ar[] = $assigned_blog_cat->term_id;
    }

    $args = array(
        'post_type' => 'post',// your post type,
         'orderby' => 'rand',
         'posts_per_page' => 3,
         'orderby' => 'date',
        'category__in' => $cat_ar
    );

$the_query = new WP_Query($args);
if($the_query->have_posts()){
    ?>
    <div class="prod-cat-blog-if">
    <?php
   while($the_query->have_posts()){
    $the_query->the_post();
     $id=get_the_ID();
      $prod_blog_img = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'single-post-thumbnail' );
      ?>
       <div class="blog-main-sec-pro">
      <?php
      if($prod_blog_img[0]){
    ?>
    <div class="prod-blog-img">
        <a href="<?php echo the_permalink();  ?>"  class="popular_img">
                  <img src="<?php echo $prod_blog_img[0]; ?>" class="img-fluid" alt=""></a>
    </div>
<?php }else{ ?>
	<div class="prod-blog-img">
        <a href="<?php echo the_permalink();  ?>"  class="popular_img">
                  <img src="<?php echo site_url(); ?>/wp-content/uploads/2022/12/construction-company-6.jpg" class="img-fluid" alt=""></a>
    </div>
<?php } ?>
    <div class="prod-blog-title">
   <?php echo "<h3>".get_the_title()."</h3>"; ?>
   </div>
   <div class="prod-blog-content">
    <?php
   $content=apply_filters('the_content', $post->post_content);
      echo "<p>".wp_trim_words($content, 40, '...' )."<a class='blog_button' href='".get_the_permalink()."'>Read More</a></p>";
     ?>
   </div>
</div>
    <?php
    }
    ?>
    </div>
    <?php
   }

		?>
		<div class="main_blog_more">

<a class="read_more_blog" href="https://sanitaryfittings.us/blog">Read more on our blog</a>

</div>
		<?php
      endif;
?>
</div>

<?php
}
return ob_get_clean();




}
add_shortcode( 'my_custom_blogs', 'woocommerce_output_content_wrapper_end', 20 );

/* Search Results */

// Show Blog posts only within the "Content" section
function filter_divi_blog_module_posts_on_search($query) {
  if (!is_admin() && $query->is_main_query() && is_search()) {
    $query->set('post_type', 'post'); // Limit the query to standard posts
  }
}
add_action('pre_get_posts', 'filter_divi_blog_module_posts_on_search');


