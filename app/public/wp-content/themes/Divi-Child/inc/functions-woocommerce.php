<?php

/*
PRODUCTS DISPLAY BEGIN
*/
add_filter( 'loop_shop_per_page', 'lw_loop_shop_per_page', 30 );
function lw_loop_shop_per_page( $products ) {
    $products = 16;
    return $products;
}
/*
PRODUCTS DISPLAY END
*/

/**
 * FREE SHIPPING
 */
/**
 * WooCommerce Shipping - Product total 100 label and cost change Free Ground Shipping
 */
add_filter( 'woocommerce_shipping_rate_label', 'sanitaryfittings_shipping_label_change', 10, 2 );
function sanitaryfittings_shipping_label_change( $label, $method ) {
    $product_total = WC()->cart->get_subtotal();

    if($product_total >= 100){
        if($label === 'Ground (UPS)'){
            return 'Free Ground Shipping';
        }
    }
    return $label;
}

/**
 * WooCommerce Shipping -
 */
add_filter( 'woocommerce_shipping_rate_cost', 'sanitaryfittings_shipping_cost_change', 10, 2 );
function sanitaryfittings_shipping_cost_change($cost, $method) {
    $product_total = WC()->cart->get_subtotal();

    if($product_total >= 100){
        if($method->get_label() === 'Free Ground Shipping'){
            return '0';
        }
    }
    return $cost;
}

/**
 *  WooCommerce Shipping -
 */
add_filter( 'woocommerce_package_rates', 'sanitaryfittings_shipping_packages', 99, 2 );
function sanitaryfittings_shipping_packages ($rates, $package){
    $product_total = WC()->cart->get_subtotal();
    if($product_total >= 100){
        if ( isset( $rates['ups:13:03'] ) ) {
            $ground_ups = $rates['ups:13:03'];
            unset($rates['ups:13:03']);
            $rates = array_merge(array('ups:13:03' => $ground_ups), $rates);
            $taxes = array();
            foreach( $rates['ups:13:03']->taxes as $key => $tax_cost ) {
                $taxes[$key] = 0; // Set each tax to Zero
            }
            if ( ! empty($taxes) ) {
                $rates['ups:13:03']->taxes = $taxes;
            }
        }
    }
    return $rates;
}

/**
 *   INCLUDED -  WooCommerce AddToCart - Add custom HTML to Add to Cart message 'Customers Also Bought'.
 *   SAN-CartFlyout-Implementation-Dec2023 - Implementation merge
 */
add_filter( 'wc_add_to_cart_message', 'virtina_wc_add_to_cart_message', 10, 2 );
function virtina_wc_add_to_cart_message( $message, $product_id ) {

    $variation_id = $_POST['variation_id'];
    $crosssell_variant_ids = get_post_meta($variation_id,'_crosssell_variant_ids',true);

	$product = wc_get_product( $product_id );
    $cart_url = wc_get_cart_url();
    $checkout_url = wc_get_checkout_url();
    $cart_contents_count = WC()->cart->get_cart_contents_count();
    $image = $product->get_image(); // Get product image
    $product_title = $product->get_name(); // Get product title
    $product_price = $product->get_price(); // Get product price as a number

    // Get quantity of this product added to cart
    $cart_items = WC()->cart->get_cart();
    $product_count_added = 0;
    foreach ($cart_items as $item_key => $item_values) {
        if ($item_values['product_id'] == $product_id) {
            $product_count_added += $item_values['quantity'];
        }
    }

	// Calculate the subtotal for this product
    $subtotal = $product_count_added * $product_price;
    $formatted_subtotal = wc_price($subtotal); // Format the subtotal with the WooCommerce currency settings

    // Customize the message
    $message = '<div class="custom-cart-message">';
    $message .= '<div class="cart-message-wrapper">';
    $message .= '<p>Added to your Cart:</p>';
    $message .= '<div class="product-media">';
    $message .= '<div class="product-img">' . $image . '</div>';
    $message .= '<div class="product-details">';
    $message .= '<div class="product-title">' . $product_title . '</div>';
    $message .= '<div class="product-quantity-price">' . $product_count_added . ' x ' . wc_price($product_price) . '</div>';
    $message .= '</div>';
    $message .= '</div>';
	$message .= '<div class="product-subtotal">Added Subtotal: ' . $formatted_subtotal . '</div>';

    // Check if there are other items in the cart
    $message .= '<div class="crt-btn-cont">';
    if ($cart_contents_count > 1) {
        $message .= '<a href="' . esc_url( $cart_url ) . '" class="btn view-cart">View Cart (' . $cart_contents_count . ')</a>';
    } else {
        $message .= '<a href="' . esc_url( $cart_url ) . '" class="btn view-cart">View Cart (1 item)</a>';
    }

    $message .= '<a href="' . esc_url( $checkout_url ) . '" class="btn checkout">Checkout</a>';
    $message .= '</div>';
    $message .= '</div>';
    $message .= '</div>';

    //$message = '
    //        <i class="icon-info"></i>
    //        <div class="product-added-to-cart">'.$message.'</div>';

    if(!empty($crosssell_variant_ids)){
        $message .='<div class="divider"></div>
                    <div class="flex-container">
                        <div class="message">
                            <div class="message_title">'.esc_html__( 'You may also like:', 'woocommerce' ).'</div>
                            <div class="continue dt">'.esc_html__( 'Continue Shopping', 'woocommerce' ).'</div>
                        </div>
                        <div class="clear"></div>
                        <div class="suggestedProd">
                            <div class="products_block">';

        foreach ($crosssell_variant_ids as $crosssell_variant_id) {

            $product = wc_get_product( $crosssell_variant_id );

            $variation_link = get_permalink($crosssell_variant_id);
            if(!empty($product)){
                $message .= '
                            <div class="product_block">
                                <a href="'.$variation_link.'" class="var_link">
                                    <div class="image_box">'.$product->get_image( array( 80, 80 ) ).'</div>
                                    <div class="wrapper">
                                        <div class="title_box">'.$product->get_description().'</div>
                                        <div class="view-product-btn">'.esc_html__( 'View Product', 'woocommerce' ).'</div>
                                    </div>
                                </a>
                            </div>';
            }
        }
        $message .= '       </div>
                        </div>
                        <div class="continue mb">'.esc_html__( 'Continue Shopping', 'woocommerce' ).'</div>
                </div>';
    }
    return $message;
}

/**
 * WooCommerce - Create new fields for variations
 */
add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
function variation_settings_fields( $loop, $variation_data, $variation ) {

    woocommerce_wp_text_input(



        array(



            'id'          => '_text_field[' . $loop . ']',

            'label'       => __( 'Availability', 'woocommerce' ),

            'placeholder' => 'Usually In Stock',

            'desc_tip'    => 'true',

            'description' => __( 'Enter product availability here.', 'woocommerce' ),

            'value'       => get_post_meta( $variation->ID, '_text_field', true )

        )

    );

    woocommerce_wp_text_input(



        array(

            'id' => 'steel_no[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Item No.', 'woocommerce' ),

            'placeholder' => 'Item No',

            'desc_tip'    => 'true',

            'description' => __( 'Enter ERP Item No for WooCommerce Import here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'steel_no', true )

        )

    );

    woocommerce_wp_text_input(


        array(

            'id' => 'sanitary_size[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Sanitary Size', 'woocommerce' ),

            'placeholder' => 'Sanitary Size',

            'desc_tip'    => 'true',

            'description' => __( 'Enter product sanitary size here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'sanitary_size', true )

        )

    );

    woocommerce_wp_text_input(



        array(

            'id' => 'flange_od[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Flange OD', 'woocommerce' ),

            'placeholder' => 'Flange OD',

            'desc_tip'    => 'true',

            'description' => __( 'Enter product flange od here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'flange_od', true )

        )

    );

    woocommerce_wp_text_input(



        array(

            'id' => 'tube_id[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Tube ID', 'woocommerce' ),

            'placeholder' => 'Tube ID',

            'desc_tip'    => 'true',

            'description' => __( 'Enter product Tube ID here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'tube_id', true )

        )

    );

    woocommerce_wp_text_input(



        array(

            'id' => 'material[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Material', 'woocommerce' ),

            'placeholder' => 'Material',

            'desc_tip'    => 'true',

            'description' => __( 'Enter product material here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'material', true )

        )

    );

    woocommerce_wp_text_input(



        array(

            'id' => 'for_hose_id[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'For Hose Id', 'woocommerce' ),

            'placeholder' => 'For Hose Id',

            'desc_tip'    => 'true',

            'description' => __( 'Enter product for hose id here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'for_hose_id', true )

        )

    );

    woocommerce_wp_text_input(


        array(

            'id' => 'pipe_size_length[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Pipe Size Length', 'woocommerce' ),

            'placeholder' => 'Pipe Size Length',

            'desc_tip'    => 'true',

            'description' => __( 'Enter product for pipe size length here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'pipe_size_length', true )

        )

    );

    woocommerce_wp_text_input(



        array(

            'id' => 'drawing_url[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Drawing url', 'woocommerce' ),

            'placeholder' => 'Drawing url',

            'desc_tip'    => 'true',

            'description' => __( 'Enter product for drawing url here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'drawing_url', true )

        )

    );

    woocommerce_wp_text_input(



        array(

            'id' => 'drawing_sort_order[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Drawing Sort Order', 'woocommerce' ),

            'placeholder' => 'Drawing Sort Order',

            'desc_tip'    => 'true',

            'description' => __( 'Enter sort order for drawing url here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'drawing_sort_order', true )

        )

    );

    woocommerce_wp_text_input(



        array(

            'id' => 'specification[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Specifications Met', 'woocommerce' ),

            'placeholder' => 'Specifications Met',

            'desc_tip'    => 'true',

            'description' => __( 'Enter specifications met here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'specification', true )

        )

    );

    woocommerce_wp_text_input(

        array(

            'id' => 'certification[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Certification', 'woocommerce' ),

            'placeholder' => 'Certification',

            'desc_tip'    => 'true',

            'description' => __( 'Enter certification here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'certification', true )

        )

    );

    woocommerce_wp_text_input(

        array(

            'id' => 'wall_thickness[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Wall Thickness', 'woocommerce' ),

            'placeholder' => 'Wall Thickness',

            'desc_tip'    => 'true',

            'description' => __( 'Enter wall thickness here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'wall_thickness', true )

        )

    );

    woocommerce_wp_text_input(

        array(

            'id' => 'surface_finish[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Surface Finish', 'woocommerce' ),

            'placeholder' => 'Surface Finish',

            'desc_tip'    => 'true',

            'description' => __( 'Enter surface finish here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'surface_finish', true )

        )

    );

    woocommerce_wp_text_input(

        array(

            'id' => 'pressure_rating[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Pressure Rating', 'woocommerce' ),

            'placeholder' => 'Pressure Rating',

            'desc_tip'    => 'true',

            'description' => __( 'Enter pressure rating here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'pressure_rating', true )
        )
    );

    woocommerce_wp_text_input(

        array(

            'id' => 'temperature_rating[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Temperature Rating', 'woocommerce' ),

            'placeholder' => 'Temperature Rating',

            'desc_tip'    => 'true',

            'description' => __( 'Enter temperature rating here.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'temperature_rating', true )
        )
    );
//Extended OEM Part Number
    woocommerce_wp_text_input(

        array(

            'id' => 'ext_oem_part_no[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Extended OEM Part Number', 'woocommerce' ),

            'placeholder' => 'Extended OEM Part Number',

            'desc_tip'    => 'true',

            'description' => __( 'Enter extended OEM Part Number.', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'ext_oem_part_no', true )
        )
    );
    
    //OEM
    woocommerce_wp_text_input(

        array(

            'id' => 'oem[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'OEM', 'woocommerce' ),

            'placeholder' => 'OEM',

            'desc_tip'    => 'true',

            'description' => __( 'Enter OEM', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'oem', true )
        )
    );
    
    //MPN
    woocommerce_wp_text_input(

        array(

            'id' => 'mpn[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'MPN', 'woocommerce' ),

            'placeholder' => 'MPN',

            'desc_tip'    => 'true',

            'description' => __( 'Enter MPN', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'mpn', true )
        )
    );
	
 //Kit Includes
    woocommerce_wp_text_input(

        array(

            'id' => 'kit_includes[' . $loop . ']',

            'class' => 'custom_attributes',

            'label' => __( 'Kit Includes', 'woocommerce' ),

            'placeholder' => 'Kit Includes',

            'desc_tip'    => 'true',

            'description' => __( 'Included in kit', 'woocommerce' ),

            'value' => get_post_meta( $variation->ID, 'kit_includes', true )
        )
    );
	
}

/**
 * WooCommerce - Save new fields for variations
 */
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );
function save_variation_settings_fields( $variation_id, $i) {
    //Availability field
    $text_field = $_POST['_text_field'][$i];
    if( isset( $text_field ) ) {
        update_post_meta( $variation_id, '_text_field', sanitize_text_field( $text_field ) );
    }

    $sanitary_size = $_POST['sanitary_size'][$i];
    if( isset( $sanitary_size ) ) {
        update_post_meta( $variation_id, 'sanitary_size', sanitize_text_field( $sanitary_size ) );
    }

    $flange_od = $_POST['flange_od'][$i];
    if( isset( $flange_od ) ) {
        update_post_meta( $variation_id, 'flange_od', sanitize_text_field( $flange_od ) );
    }

    $tube_id = $_POST['tube_id'][$i];
    if( isset( $tube_id ) ) {
        update_post_meta( $variation_id, 'tube_id', sanitize_text_field( $tube_id ) );
    }

    $material = $_POST['material'][$i];
    if( isset( $material ) ) {
        update_post_meta( $variation_id, 'material', sanitize_text_field( $material ) );
    }

    $for_hose_id = $_POST['for_hose_id'][$i];
    if( isset( $for_hose_id ) ) {
        update_post_meta( $variation_id, 'for_hose_id', sanitize_text_field( $for_hose_id ) );
    }

    $pipe_size_length = $_POST['pipe_size_length'][$i];
    if( isset( $pipe_size_length ) ) {
        update_post_meta( $variation_id, 'pipe_size_length', sanitize_text_field( $pipe_size_length ) );
    }

    $drawing_url = $_POST['drawing_url'][$i];
    if( isset( $drawing_url ) ) {
        update_post_meta( $variation_id, 'drawing_url', sanitize_text_field( $drawing_url ) );
    }

    $drawing_sort_order = $_POST['drawing_sort_order'][$i];
    if( isset( $drawing_sort_order ) ) {
        update_post_meta( $variation_id, 'drawing_sort_order', sanitize_text_field( $drawing_sort_order ) );
    }

    $steel_no = $_POST['steel_no'][$i];
    if( isset( $steel_no ) ) {
        update_post_meta( $variation_id, 'steel_no', sanitize_text_field( $steel_no ) );
    }

    $specification = $_POST['specification'][$i];
    if( isset( $specification ) ) {
        update_post_meta( $variation_id, 'specification', sanitize_text_field( $specification ) );
    }

    $certification = $_POST['certification'][$i];
    if( isset( $certification ) ) {
        update_post_meta( $variation_id, 'certification', sanitize_text_field( $certification ) );
    }

    $wall_thickness = $_POST['wall_thickness'][$i];
    if( isset( $wall_thickness ) ) {
        update_post_meta( $variation_id, 'wall_thickness', sanitize_text_field( $wall_thickness ) );
    }

    $surface_finish = $_POST['surface_finish'][$i];
    if( isset( $surface_finish ) ) {
        update_post_meta( $variation_id, 'surface_finish', sanitize_text_field( $surface_finish ) );
    }

    $pressure_rating = $_POST['pressure_rating'][$i];
    if( isset( $pressure_rating ) ) {
        update_post_meta( $variation_id, 'pressure_rating', sanitize_text_field( $pressure_rating ) );
    }

    $temperature_rating = $_POST['temperature_rating'][$i];
    if( isset( $temperature_rating ) ) {
        update_post_meta( $variation_id, 'temperature_rating', sanitize_text_field( $temperature_rating ) );
    }
    
    //Extended OEM Part Number
    $ext_oem_part_no = $_POST['ext_oem_part_no'][$i];
    if( isset( $ext_oem_part_no ) ) {
        update_post_meta( $variation_id, 'ext_oem_part_no', sanitize_text_field( $ext_oem_part_no ) );
    }
    
     //OEM
    $oem = $_POST['oem'][$i];
    if( isset( $oem ) ) {
        update_post_meta( $variation_id, 'oem', sanitize_text_field( $oem ) );
    }
    
    //MPN
    $mpn = $_POST['mpn'][$i];
    if( isset( $mpn ) ) {
        update_post_meta( $variation_id, 'mpn', sanitize_text_field( $mpn ) );
    }
	
	 //Kit Includes
    $kit_includes = $_POST['kit_includes'][$i];
    if( isset( $kit_includes ) ) {
        update_post_meta( $variation_id, 'kit_includes', ( $kit_includes ) );
    }
}

/**
 * WooCommerce - Add New Variation Settings
 */
add_filter( 'woocommerce_available_variation', 'load_variation_settings_fields' );
function load_variation_settings_fields( $variations ) {

    $variations['text_field'] = get_post_meta( $variations['variation_id'], '_text_field', true );

    $variations['sanitary_size'] = get_post_meta( $variations['variation_id'], 'sanitary_size', true );

    $variations['flange_od'] = get_post_meta( $variations['variation_id'], 'flange_od', true );

    $variations['tube_id'] = get_post_meta( $variations['variation_id'], 'tube_id', true );

    $variations['material'] = get_post_meta( $variations['variation_id'], 'material', true );

    $variations['for_hose_id'] = get_post_meta( $variations['variation_id'], 'for_hose_id', true );

    $variations['pipe_size_length'] = get_post_meta( $variations['variation_id'], 'pipe_size_length', true );

    $variations['drawing_url'] = get_post_meta( $variations['variation_id'], 'drawing_url', true );

    $variations['drawing_sort_order'] = get_post_meta( $variations['variation_id'], 'drawing_sort_order', true );

    $variations['steel_no'] = get_post_meta( $variations['variation_id'], 'steel_no', true );

    $variations['specification'] = get_post_meta( $variations['variation_id'], 'specification', true );

    $variations['certification'] = get_post_meta( $variations['variation_id'], 'certification', true );

    $variations['wall_thickness'] = get_post_meta( $variations['variation_id'], 'wall_thickness', true );

    $variations['surface_finish'] = get_post_meta( $variations['variation_id'], 'surface_finish', true );

    $variations['pressure_rating'] = get_post_meta( $variations['variation_id'], 'pressure_rating', true );

    $variations['temperature_rating'] = get_post_meta( $variations['variation_id'], 'temperature_rating', true );
	
	//Extended OEM Part Number
    $variations['ext_oem_part_no'] = get_post_meta( $variations['variation_id'], 'ext_oem_part_no', true );
    
    //OEM
    $variations['oem'] = get_post_meta( $variations['variation_id'], 'oem', true );
    
    //MPN
    $variations['mpn'] = get_post_meta( $variations['variation_id'], 'mpn', true );
	
	 //Kit_Includes
    $variations['kit_includes'] = get_post_meta( $variations['variation_id'], 'kit_includes', true );
	
    return $variations;

}

/**
 * WooCommerce Product add 'line drawing' tabs on single product page
 */
add_filter( 'woocommerce_product_tabs', 'custom_tabs', 10 );
function custom_tabs( $tabs ) {
    $tabs['line_drawing'] = array(
        'title'     => __( 'Line Drawings', 'woocommerce' ),
        'priority'  => 50,
        'callback'  => 'woo_custom_description_tab_content'
    );
    return $tabs;
}

/**
 * WooCommerce Checkout remove Puerto Rico
 */
add_filter( 'woocommerce_countries', 'wc_remove_pr_country', 10, 1 );
function wc_remove_pr_country ( $country ) {
    unset($country["PR"]);
    return $country;
}

/**
 * WooCommerce Checkout List state to show
 */
add_filter( 'woocommerce_states', 'wc_us_states_mods' );
function wc_us_states_mods ( $states ) {
    $states['US'] = array(
        'AL' => __( 'Alabama', 'woocommerce' ),
        'AK' => __( 'Alaska', 'woocommerce' ),
        'AZ' => __( 'Arizona', 'woocommerce' ),
        'AR' => __( 'Arkansas', 'woocommerce' ),
        'CA' => __( 'California', 'woocommerce' ),
        'CO' => __( 'Colorado', 'woocommerce' ),
        'CT' => __( 'Connecticut', 'woocommerce' ),
        'DE' => __( 'Delaware', 'woocommerce' ),
        'DC' => __( 'District Of Columbia', 'woocommerce' ),
        'FL' => __( 'Florida', 'woocommerce' ),
        'GA' => _x( 'Georgia', 'US state of Georgia', 'woocommerce' ),
        'HI' => __( 'Hawaii', 'woocommerce' ),
        'ID' => __( 'Idaho', 'woocommerce' ),
        'IL' => __( 'Illinois', 'woocommerce' ),
        'IN' => __( 'Indiana', 'woocommerce' ),
        'IA' => __( 'Iowa', 'woocommerce' ),
        'KS' => __( 'Kansas', 'woocommerce' ),
        'KY' => __( 'Kentucky', 'woocommerce' ),
        'LA' => __( 'Louisiana', 'woocommerce' ),
        'ME' => __( 'Maine', 'woocommerce' ),
        'MD' => __( 'Maryland', 'woocommerce' ),
        'MA' => __( 'Massachusetts', 'woocommerce' ),
        'MI' => __( 'Michigan', 'woocommerce' ),
        'MN' => __( 'Minnesota', 'woocommerce' ),
        'MS' => __( 'Mississippi', 'woocommerce' ),
        'MO' => __( 'Missouri', 'woocommerce' ),
        'MT' => __( 'Montana', 'woocommerce' ),
        'NE' => __( 'Nebraska', 'woocommerce' ),
        'NV' => __( 'Nevada', 'woocommerce' ),
        'NH' => __( 'New Hampshire', 'woocommerce' ),
        'NJ' => __( 'New Jersey', 'woocommerce' ),
        'NM' => __( 'New Mexico', 'woocommerce' ),
        'NY' => __( 'New York', 'woocommerce' ),
        'NC' => __( 'North Carolina', 'woocommerce' ),
        'ND' => __( 'North Dakota', 'woocommerce' ),
        'OH' => __( 'Ohio', 'woocommerce' ),
        'OK' => __( 'Oklahoma', 'woocommerce' ),
        'OR' => __( 'Oregon', 'woocommerce' ),
        'PA' => __( 'Pennsylvania', 'woocommerce' ),
        'PR' => __( 'Puerto Rico', 'woocommerce' ),
        'RI' => __( 'Rhode Island', 'woocommerce' ),
        'SC' => __( 'South Carolina', 'woocommerce' ),
        'SD' => __( 'South Dakota', 'woocommerce' ),
        'TN' => __( 'Tennessee', 'woocommerce' ),
        'TX' => __( 'Texas', 'woocommerce' ),
        'UT' => __( 'Utah', 'woocommerce' ),
        'VT' => __( 'Vermont', 'woocommerce' ),
        'VA' => __( 'Virginia', 'woocommerce' ),
        'WA' => __( 'Washington', 'woocommerce' ),
        'WV' => __( 'West Virginia', 'woocommerce' ),
        'WI' => __( 'Wisconsin', 'woocommerce' ),
        'WY' => __( 'Wyoming', 'woocommerce' ),
        'AA' => __( 'Armed Forces (AA)', 'woocommerce' ),
        'AE' => __( 'Armed Forces (AE)', 'woocommerce' ),
        'AP' => __( 'Armed Forces (AP)', 'woocommerce' ),
    );
    return $states;
}

/**
 * WooCommerce MyOrders - Change the link's name to see invoice
 */
add_filter( 'wpo_wcpdf_myaccount_button_text', 'change_my_account_orders_invoice_text', 10, 2 );
function change_my_account_orders_invoice_text( $actions, $order ) {
    return __( 'Invoice', 'woocommerce' );
}

/**
 * WooCommerce MyOrders - add target blank (open on another windows) a link (UPS tracking link)
 */
add_action( 'woocommerce_after_account_orders', 'custom_ups_action_after_account_orders_js');
function custom_ups_action_after_account_orders_js() {
    $action_slug = 'ups_tracking_button'; ?>
    <script>
        jQuery(function($){
            $('a.<?php echo $action_slug; ?>').each( function(){
                $(this).attr('target','_blank');
            })
        });
    </script> <?php
}

/**
 * WooCommerce Shipping -
 */
add_filter( 'woocommerce_cart_shipping_method_full_label', 'Add_free_to_shipping_label_for_zero_cost', 10, 2 );
function Add_free_to_shipping_label_for_zero_cost( $label, $method ) {
    $product_total = WC()->cart->get_subtotal();

    if($product_total >= 100){
        if ( ! ( $method->cost > 0 ) && $method->method_id !== 'free_shipping' ) {
            $label .= ': ' . __('$0.00');
        }
    }
    return $label;
}

/**
 * WooCommerce MyOrders - Add a link to track the order on UPS
 */
add_filter( 'woocommerce_my_account_my_orders_actions', 'add_custom_ups_button_my_orders', 50, 2 );
function add_custom_ups_button_my_orders( $actions, $order ) {
    //$tracking_items = get_post_meta( 22746, '_wc_shipment_tracking_items', true );
    $tracking_items = get_post_meta( $order->id, '_wc_shipment_tracking_items', true );
    $action_slug = 'ups_tracking_button';
    if ( $tracking_items !== '' ) {
        foreach ( $tracking_items as $tracking_item ) {
            $tracker = esc_html( $tracking_item['tracking_number'] );
        }
        $actions[$action_slug] = array(
            'url'  => "https://wwwapps.ups.com/tracking/tracking.cgi?tracknum=$tracker",
            'name' => "UPS Tracking"
        );
    }
    return $actions;
}

/**
 * Woocommerce Checkout - Update review order prices on changing address info
 */
add_action( 'woocommerce_review_order_before_payment', 'refresh_checkout_on_payment_methods_change' );
function refresh_checkout_on_payment_methods_change(){
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery( 'form.checkout' ).on( 'change', '#billing_state, #billing_postcode, #shipping_state, #shipping_postcode, #billing_country', function() {
                jQuery('body').trigger('update_checkout');
            });
        });
    </script>
    <?php
}


/**
 * Custom User meta Fields form on backend
 */
add_action('show_user_profile', 'virtina_user_profile_field_callback');
add_action('edit_user_profile', 'virtina_user_profile_field_callback');
function virtina_user_profile_field_callback($user) {
    $checked = (isset($user->invoice_payment) && $user->invoice_payment) ? ' checked="checked"' : '';  ?>
    <h3>Invoice Settings</h3>
    <label for="vir_invoice_payment">
        <input name="vir_invoice_payment" type="checkbox" id="vir_invoice_payment" value="1" <?php echo $checked; ?>>
        Invoice Payment
    </label>
<?php }

/**
 * Custom User meta Field value update
 */
add_action('personal_options_update', 'virtina_user_profile_field_update');
add_action('edit_user_profile_update', 'virtina_user_profile_field_update');
function virtina_user_profile_field_update($user_id) {
    $cf_val = isset( $_POST['vir_invoice_payment'] ) ? 1 : 0;
    update_user_meta($user_id, 'invoice_payment', $cf_val);
}

/**
 *  WooCommerce Disable Invoice Payment Gateway
 */
add_filter( 'woocommerce_available_payment_gateways', 'vi_invoice_payment_gateway_disable' );
function vi_invoice_payment_gateway_disable( $available_gateways ) {
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $invoice_payment_flag = get_user_meta( $user_id, 'invoice_payment', true );
        if( $invoice_payment_flag != 1 ){
            unset( $available_gateways['invoice'] );
        }
    }
    return $available_gateways;
}

/**
 *  default checkout state
 */
add_filter( 'default_checkout_billing_state', 'change_default_checkout_state' );
add_filter( 'default_checkout_shipping_state', 'change_default_checkout_state' );
function change_default_checkout_state() {
    return ''; //set state code if you want to set it otherwise leave it blank.
}

// ##### TO BE CLEAN #####

//add_action( 'woocommerce_after_single_product_summary', 'variation_deb', 10);
function woo_custom_description_tab_content() {
    global $product;
    $id_product = $product->get_id();
    $product = wc_get_product( $id_product );
    if($product->is_type( 'variable' )) {
        $variations = $product->get_available_variations();
        $sanitary_size = array_column($variations, 'sanitary_size');

        $sort_variations = [];
        foreach($variations as $k => $v) {
            $sort_variations[$k]['sku'] = $v['sku'];
            $sort_variations[$k]['variation_description'] = $v['variation_description'];
            $sort_variations[$k]['drawing_url'] = $v['drawing_url'];
        };

        sort($sort_variations);

    }

    if(!empty($variations)) {
        $html =
            '<div id="product_description" class="et_pb_row row products margin-0 justify-content-between">
                    <h3>Line Drawings</h3>
                    <table class="table dynamic_desc" style="width:100%">
                        <tr>
                            <th>SKU</th><th>Description</th><th>Line Drawing</th>
                        </tr>';


        foreach ( $sort_variations as $variation ) {
            $URL = $variation['drawing_url'];
            if (str_contains($URL, 'request-line-drawing')) {
                $urlText = 'Request Line Drawing';
            } else{$urlText = 'PDF';}

            $html .=
                '<tr>
                            <td>'.$variation['sku'].'</td>
                            <td>'.$variation['variation_description'].'</td>
                            <td><a href="'.$variation['drawing_url'].'" target="_blank">'.$urlText.'</a></td>
                        </tr>';
        }

        $html .=
            '</table>
            </div>';
        echo $html;
    }
}

/* REMOVE CROSS-SELL IN CART */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

add_filter( 'woocommerce_terms_is_checked_default', '__return_true' );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

//Hide Price Range for WooCommerce Variable Products
add_filter('woocommerce_get_price_html', 'lw_hide_variation_price', 10, 2);
function lw_hide_variation_price( $v_price, $v_product ) {
    $v_product_types = array( 'variable');
    //JDZ 20230421 change product_type -> get_type()
    if ( in_array ( $v_product->get_type(), $v_product_types ) && !(is_shop()) ) {
        return '';
    }
// return regular price
    return $v_price;
}

// SUPPORT TO CHANGE THEME FILES
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );
function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

/*metadata field to woocommerce rest API for orders.*/
add_filter( 'woocommerce_rest_prepare_shop_order_object', 'prefix_wc_rest_prepare_order_object', 10, 3 );
function prefix_wc_rest_prepare_order_object( $response, $object, $request ) {

    if( empty( $response->data ) )
        return $response;

    if ( metadata_exists( 'post', $object->get_id(), 'mtrs_required' ) ) {
        $mtrs_required = get_post_meta( $object->get_id() , 'mtrs_required', true );
    }

    $order = wc_get_order( $object->get_id() );
    if($order->get_customer_note()) {
        $order_note = $order->get_customer_note();
    }

    foreach($response->data['line_items'] as $key => $productItems) {
        $productID = $productItems['product_id'];
        $variationID = $productItems['variation_id'];

        if($variationID != 0) {
            if ( metadata_exists( 'post', $variationID, 'steel_no' ) ) {
                $steel_no = get_post_meta( $variationID , 'steel_no', true );
                if($steel_no) {
                    $response->data['line_items'][$key]['steel_no'] = $steel_no;
                }
            }
        }

    }

    if($mtrs_required) {
        $response->data['line_items'][$key]['mtrs_required'] = $mtrs_required;
    }

    if($order_note) {
        $response->data['line_items'][$key]['additional_order_note'] = $order_note;
    }

    return $response;
}

// ##### NEW FUNCTIONS #####