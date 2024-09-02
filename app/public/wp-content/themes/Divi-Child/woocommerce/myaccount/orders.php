<?php
/**
 * Orders
 *
 * Shows orders on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/orders.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>
<span class="account-back"><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ) ?>">Orders</a></span>
<h1 class="account-heading"> Orders </h1>
<div class="orders-container">
<?php if ( $has_orders ) : ?>
  <?php
		foreach ( $customer_orders->orders as $customer_order ) {
			$order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	  	$item_count = $order->get_item_count() - $order->get_item_count_refunded();
	?>

    <div class="order-block">
      <div class="order-left">
        <div class="order-info">
          <span class="order-date">
            <time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
          </span>
          <span class="order-number">
            <?php echo esc_html( _x( 'Order #', 'hash before order number', 'woocommerce' ) . $order->get_order_number() ); ?>
          </span>
          <div class="order-amount">
            <?php
							/* translators: 1: formatted order total 2: total order items */
							echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ) );
						?>
          </div>
        </div>
        <div class="order-actions">
          <?php
						$actions = wc_get_account_orders_actions( $order );

						if ( ! empty( $actions ) ) {
							foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                if ($key == 'view') {
                  $newName = 'Details';
                }
                if ($key == 'invoice') {
                  $newName = 'View Invoice';
                }
								echo '<a href="' . esc_url( $action['url'] ) . '" class="account-btn '  . sanitize_html_class( $key ) . '">' . $newName . '</a>';
							}
						}
					?>
        </div>
      </div>
      <div class="order-right">
        <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>      
      </div>
    </div>

  <?php
		}
	?>
  
  <?php if ( 1 < $customer_orders->max_num_pages ) : ?>
      <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
        <?php if ( 1 !== $current_page ) : ?>
          <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
        <?php endif; ?>

        <?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
          <a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
        <?php endif; ?>
      </div>
	<?php endif; ?>

<?php else : ?>
	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
		<a class="woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php esc_html_e( 'Browse products', 'woocommerce' ); ?></a>
		<?php esc_html_e( 'No order has been made yet.', 'woocommerce' ); ?>
	</div>
<?php endif; ?>
</div>
<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
