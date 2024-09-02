<?php

add_shortcode('sku_list', 'sku_list_func');
function sku_list_func ( ){



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

        ob_start();
        echo '<div>
        <h3>SKUs available on this product page</h3>
        '.$sku.'
        </div>';
        return ob_get_clean();

    }

}


add_shortcode('show_reviews_on_page', 'show_reviews_on_page_func');
function show_reviews_on_page_func(){
    ob_start();
    //$return = "<div class='review-row'>";

    $args = array(
        'numberposts'      => -1,
        'orderby'          => 'rand',
        'post_type'        => 'reviews_to_show',
        'fields'           => 'ids'
    );
    $reviews = get_posts($args);

    $count = 0;
    foreach ($reviews as $review){
        $content_post = get_post($review);
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);

        /*if($count%3 == 0){
            $return .=
                "</div><div class='review-row'>";
        }*/

        $stars_str = '';
        $stars = get_post_meta($review, 'comment_stars', true);
        for($i=0;$i<$stars;$i++){
            $stars_str .= '<span class="dashicons dashicons-star-filled"></span>';
        }

        $return .=
            "<div class='review-item'>
                <div class='review-text'>
                    ".$content."
                </div>
                <div class='review-name'>
                    ".get_the_title($review)."
                </div>
                <div class='review-sector'>
                    ".get_post_meta($review, 'comment_sector', true)."
                </div>
                <div class='review-stars'>
                    ".$stars_str."
                </div>
            </div>";
        $count++;
    }
    //$return .= "</div>";

    ob_end_clean();
    return $return;
}

add_shortcode('show_subcategories', 'show_subcategories_func');
function show_subcategories_func($atts) {
    $attributes = shortcode_atts( array(
        'categories' => ""
    ), $atts );

    ob_start();

    if(!empty($attributes["categories"])){
        $args = array(
            'include' => explode(",",$attributes["categories"])
        );
    }else{
        $parentid = get_queried_object_id();
        if($parentid == 0){
            $args = array(

            );
        }else{
            $args = array(
                'parent' => $parentid
            );
        }
    }

    $terms = get_terms( 'product_cat', $args );

    if ( $terms ) {
        $return =
            '<h2>Categories</h2>
            <ul class="show-subcategories">';

        foreach ( $terms as $term ) {
            $thumbnail_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
            $image = wp_get_attachment_url( $thumbnail_id );
            $return .=
                '<li class="show-subcategories-item">
                    <a href="' .  esc_url( get_term_link( $term ) ) . '" class="' . $term->slug . '">                    
                        <div class="show-subcategories-img">
                            <img src="'.$image.'" alt="'.$term->name.'"/>
                            <span class="et_overlay"></span>
                        </div>
                        <span>'.$term->name.'</span>
                    </a>
                </li>';
        }

        $return .= '</ul>';
    }
    ob_end_clean();
    return $return;
}

add_shortcode( 'products_reviews2', 'products_reviews_func2' );
function products_reviews_func2( $atts ) {
    if(!empty($atts['ids'])){
        ob_start();
        $return = '<div class="woocommerce-products-reviews">';
        foreach (explode(',', $atts['ids']) as $product_id) {
            $return .= '<div class="woocommerce-product-review">';
            $product = wc_get_product($product_id);

            $product_categories = $product->get_categories();
            $product_link = get_permalink( $product->get_id() );
            $product_name = $product->get_name();
            $product_images = $product->get_image();

            $rating_count = $product->get_rating_count();
            $review_count = $product->get_review_count();
            $average = $product->get_average_rating();


            if ($rating_count > 0) :
                $return .= "
                        $product_images
                        $product_categories
                        <a href='$product_link' alt='$product_name'>
                            $product_name
                        </a>
                        ".wc_get_rating_html($average, $rating_count)."(".$review_count." customer reviews)";
            endif;
            $return .= '</div>';
        }
        $return .= '</div>';
        ob_end_clean();
        return $return;
    }
    return;
}

add_shortcode('products_reviews', 'products_reviews_func');
function products_reviews_func( $atts ) {
    extract( shortcode_atts( array(
        'limit' => 5, // number of reviews to be displayed by default
    ), $atts, 'woo_reviews' ) );

    $comments = get_comments( array(
        'number '     => 20,
        'status'      => 'approve',
        'post_status' => 'publish',
        'post_type'   => 'product',
        'meta_query'  => array(
            array(
                'key'     => 'rating',
                'value'   => '5',
            )
        ),
    ) );

    shuffle($comments);

    $comments = array_slice( $comments, 0, $limit );

    $html = '<div class="products-reviews">';
    foreach( $comments as $comment ) {
        if(!empty($comment->comment_content)){
            $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
            $rating_html = '';
            //if ( $rating > 4 ) $rating_html .= wc_get_rating_html( $rating );
            for($i=0; $i<$rating; $i++){
                $rating_html .= '<span class="dashicons dashicons-star-filled"></span>';
            }

            $title = get_the_title( $comment->comment_post_ID );

            $html .=
                "<div class='product-review'>
                <div class='product-review-title'>$title</div>
                <div class='product-review-rate'>$rating_html</div>
                <div class='product-review-content'><span class='dashicons dashicons-format-quote'></span>$comment->comment_content<span class='dashicons dashicons-format-quote' style='transform: rotate(180deg);'></span></div>
                <div class='product-review-by'>$comment->comment_author</div>
            </div>";
        }
    }
    return $html . '</div>';
}

/**
 *  Shortcode filter cateegories
 */
add_shortcode('filter_categories', 'filter_categories_func');
function filter_categories_func( $atts ) {
    $args = array(
        'hide_empty' => true,
    );
    $categories = get_categories($args);

    $return = "<div class='filters-categories'>";
    foreach ($categories as $category){
        $return .=
            "<div class='filters-categories-item' id='filter_category_".$category->slug."'>
                ".$category->name."
            </div>";
    }

    $return .= "</div>";
    return $return;
}

/**
 * Set footer copyright
 */
add_shortcode('footer_copyright', 'footer_copyright_func');
function footer_copyright_func(){
    return __( '&copy;'. date('Y') .' Sanitary Fittings, LLC. All rights reserved.', 'divi-child' );
}

/**
 *  Add a shortcode 'hoseconnections'
 */
add_shortcode('hoseconnections', 'hoseconnections');
function hoseconnections(){
    ob_start();
    echo
    '<h3>Available Hose Connections Styles</h3>
    <ul>
        <li>90-Degree Tri-Clamp Elbow</li>
        <li>Bevel Seat - Plain w/ Nut</li>
        <li>Bevel Seat Nut</li>
        <li>Camlock Female</li>
        <li>Compression Tube</li>
        <li>Electropolished Tri-Clamp</li>
        <li>Flange End</li>
        <li>I-Line Male</li>
        <li>I-Line Female</li>
        <li>Male NPT Thread</li>
        <li>Tri-Clamp</li>
    </ul>';
    return ob_get_clean();
}

/**
 *  Add a shortcode 'sizing'
 */
add_shortcode('sizing', 'sizing');
function sizing(){
    ob_start();

    echo
    '<br/>
    <h2>How to Measure Tri-Clamp Fittings</h2>
<iframe width="1080" height="608" src="https://www.youtube.com/embed/qOOeZhMKZiQ" title="Sanitary Fittings Dimensions" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
<p>
<h2>Tri-Clamp Fittings and Gasket Sizing Guide</h2>
<img width="600" src="https://dusouecxfowwg.cloudfront.net/wp-content/uploads/2018/01/SanitaryFittings_Dimensions-Image.png" class="image wp-image-8077  attachment-full size-full size_flange" alt="Sanitary Fitting Dimensions" style="height: auto; margin-top:20px;" srcset="https://sanitaryfittings.us/wp-content/uploads/2018/01/SanitaryFittings_Dimensions-Image.png 1079w, https://sanitaryfittings.us/wp-content/uploads/2018/01/SanitaryFittings_Dimensions-Image-300x111.png 300w, https://sanitaryfittings.us/wp-content/uploads/2018/01/SanitaryFittings_Dimensions-Image-768x285.png 768w, https://sanitaryfittings.us/wp-content/uploads/2018/01/SanitaryFittings_Dimensions-Image-1024x380.png 1024w" sizes="(max-width: 1079px) 100vw, 1079px">
<br>
	<table class="table-responsive">
<tbody>
<tr>
<th>Sanitary<br>Size</th>
<th>Flange<br>OD (<a href="https://dusouecxfowwg.cloudfront.net/wp-content/uploads/2022/02/tri-clamp-flange-OD-1.png" target="_blank" title="Tri-Clamp Flange Outside Diameter Example" rel="noopener">?</a>)
</th>
<th>Tube<br>ID (<a href="https://dusouecxfowwg.cloudfront.net/wp-content/uploads/2022/02/tube-inside-diameter.png" target="_blank" title="Tubing Inside Diameter Example" rel="noopener">?</a>)
</th>
<th>Tube<br>OD (<a href="https://dusouecxfowwg.cloudfront.net/wp-content/uploads/2022/02/tube-outside-diameter.png" target="_blank" title="Tubing Outside Diameter Example" rel="noopener">?</a>)
</th>
			</tr>
			<tr>
				<td>1/2″</td>
				<td>0.984</td>
				<td>0.370</td>
				<td>0.500</td>
			</tr>
			<tr>
				<td>3/4″</td>
				<td>0.984</td>
				<td>0.620</td>
				<td>0.750</td>
			</tr>
			<tr>
				<td>1″</td>
				<td>1.984</td>
				<td>0.870</td>
				<td>1.000</td>
			</tr>
			<tr>
				<td>1 1/2″</td>
				<td>1.984</td>
				<td>1.370</td>
				<td>1.500</td>
			</tr>
			<tr>
				<td>2″</td>
				<td>2.516</td>
				<td>1.870</td>
				<td>2.000</td>
			</tr>
			<tr>
				<td>2 1/2″</td>
				<td>3.047</td>
				<td>2.370</td>
				<td>2.500</td>
			</tr>
			<tr>
				<td>3″</td>
				<td>3.579</td>
				<td>2.870</td>
				<td>3.000</td>
			</tr>
			<tr>
				<td>4″</td>
				<td>4.682</td>
				<td>3.834</td>
				<td>4.000</td>
			</tr>
			<tr>
				<td>6″</td>
				<td>6.562</td>
				<td>5.782</td>
				<td>6.000</td>
			</tr>
			<tr>
				<td>8″</td>
				<td>8.602</td>
				<td>7.782</td>
				<td>8.000</td>
			</tr>
			<tr>
				<td>10″</td>
				<td>10.570</td>
				<td>9.782</td>
				<td>10.000</td>
			</tr>
			<tr>
				<td>12″</td>
				<td>12.570</td>
				<td>11.760</td>
				<td>12.000</td>
			</tr>
		</tbody>
	</table>
<h2 style="margin-top:30px;">Looking for even more information on Tri-Clamp fittings?</h2>
	We have everything you need. <a href="https://sanitaryfittings.us/tri-clamp-guide">Read "The Ultimate Guide to Tri-Clamp Fittings"</a>';
    return ob_get_clean();
}