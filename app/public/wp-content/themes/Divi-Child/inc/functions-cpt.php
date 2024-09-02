<?php
function cpt_reviews_to_show() {
    $labels = array(
        'name' => _x( 'Reviews on page', 'post type general name' ),
        'singular_name' => _x( 'Review on page', 'post type singular name' ),
        'add_new' => _x( 'Add New', 'Review on page' ),
        'add_new_item' => __( 'Add New Review on page' ),
        'edit_item' => __( 'Edit Review on page' ),
        'new_item' => __( 'New Review on page' ),
        'all_items' => __( 'All Reviews on page' ),
        'view_item' => __( 'View Review on page' ),
        'search_items' => __( 'Search Reviews on page' ),
        'not_found' => __( 'No Reviews on page found' ),
        'not_found_in_trash' => __( 'No Reviews on page found in the Trash' ),
        'parent_item_colon' => '',
        'menu_name' => 'Review on page'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'menu_position' => 3,
        'supports' => array( 'title', 'editor'),
        'has_archive' => false,
        'publicly_queryable' => false,
    );

    register_post_type( 'reviews_to_show', $args );
}
add_action( 'init', 'cpt_reviews_to_show' );

function add_your_fields_meta_box() {
    add_meta_box(
        'reviews_to_show_meta_box', // $id
        'Extra fields', // $title
        'add_reviews_to_show_meta_box', // $callback
        'reviews_to_show', // $screen
        'normal', // $context
        'high' // $priority
    );
}
add_action( 'add_meta_boxes', 'add_your_fields_meta_box' );

function add_reviews_to_show_meta_box(){
    global $post;
    ?>
    <p>
        <label for="comment_sector">User Sector:</label><br />
        <input type="text" name="comment_sector" value="<?= get_post_meta( $post->ID, 'comment_sector', true ); ?>" />
    </p>

    <p>
        <label for="comment_stars">Stars:</label><br />
        <input type="number" name="comment_stars" value="<?= get_post_meta( $post->ID, 'comment_stars', true ); ?>" />
    </p>
    <input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">
    <?php
}

function save_reviews_to_show_custom_fields($post_id ){
    if ( !wp_verify_nonce( $_POST['your_meta_box_nonce'], basename(__FILE__) ) ) { return $post_id; }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return $post_id; }
    $old = get_post_meta( $post_id, 'comment_sector', true );
    $new = $_POST['comment_sector'];
    if ( $new && $new !== $old ) {
        update_post_meta( $post_id, 'comment_sector', $new );
    } elseif ( '' === $new && $old ) {
        delete_post_meta( $post_id, 'comment_sector', $old );
    }

    $old = get_post_meta( $post_id, 'comment_stars', true );
    $new = $_POST['comment_stars'];
    if ( $new && $new !== $old ) {
        update_post_meta( $post_id, 'comment_stars', $new );
    } elseif ( '' === $new && $old ) {
        delete_post_meta( $post_id, 'comment_stars', $old );
    }
}
add_action( 'save_post', 'save_reviews_to_show_custom_fields' );