<?php
/**
 * Payment methods
 *
 * Shows customer payment methods on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/payment-methods.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();
$count         = count($saved_methods);

do_action( 'woocommerce_before_account_payment_methods', $has_methods ); ?>
<div class="account-payments">
<span class="account-back"><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ) ?>">Payments Methods</a></span>
<h1 class="account-heading"> Payment Methods </h1>

<div class="add-payment">
  <div class="add-payment-info"><span>Add cards to your wallet for simple & fast ordering.</span></div>
  <?php if ( WC()->payment_gateways->get_available_payment_gateways() ) : ?>
    <a class="account-btn" href="<?php echo esc_url( wc_get_endpoint_url( 'add-payment-method' ) ); ?>"><?php esc_html_e( 'Add new payment method', 'woocommerce' ); ?></a>
  <?php endif; ?>
</div>

<?php if ( $has_methods ) : ?>
  <div class="address-heading">Payment Methods(<?php echo $count ?>)</div>
  <div class="payment-blocks">
    <?php foreach ( $saved_methods as $type => $methods ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
      <?php foreach ( $methods as $method ) : ?>
        <div class="payment-block">
          <div class="payment-left">
            <div class="payment-type"><?php echo sprintf( esc_html__( '%1$s ending in %2$s', 'woocommerce' ), esc_html( wc_get_credit_card_type_label( $method['method']['brand'] ) ), esc_html( $method['method']['last4'] ) ); ?></div>
            <div class="payment-expire">Exp <?php echo esc_html( $method['expires'] ); ?></div>
          </div>
          <div class="payment-right">
            <div class="payment-action">
              <?php foreach ( $method['actions'] as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                echo '<a href="' . esc_url( $action['url'] ) . '" class="account-link ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>&nbsp;';
              } ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </div>
	

<?php else : ?>

	<p class="woocommerce-Message woocommerce-Message--info woocommerce-info"><?php esc_html_e( 'No saved methods found.', 'woocommerce' ); ?></p>

<?php endif; ?>
</div>
<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>


