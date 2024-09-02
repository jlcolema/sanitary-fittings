<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);
?>

<h1 class="account-heading"> Account Dashboard </h1>
<div class="welcome-message dashboard">
    <?php if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    echo '<span class="user-welcome">Hello,<br><strong> '. esc_html ( $current_user->display_name ).'</strong></span> ';
    } ?>
</div>
<div class="account-dashboard-cards">
  <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ) ?>" class="dashboard-link">
    <div class="dashboard-card orders">
      <span class="dashboard-card-heading">Orders</span>
      <span class="dashboard-card-body">Manage your shipping and billing addresses.</span>
      <span class="dashboard-card-link">View all</span>
    </div>
  </a>
  <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ) ?>" class="dashboard-link">
    <div class="dashboard-card addresses">
      <span class="dashboard-card-heading">Addresses</span>
      <span class="dashboard-card-body">Manage your shipping and billing addresses.</span>
      <span class="dashboard-card-link">Manage</span>
    </div>
  </a>
  <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'payment-methods' ) ) ?>" class="dashboard-link">
    <div class="dashboard-card payments">
      <span class="dashboard-card-heading">Payment Methods</span>
      <span class="dashboard-card-body">Manage your shipping and billing addresses.</span>
      <span class="dashboard-card-link">Manage</span>
    </div>
  </a>
  <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ) ?>" class="dashboard-link">
    <div class="dashboard-card details">
      <span class="dashboard-card-heading">Account Details</span>
      <span class="dashboard-card-body">Manage your shipping and billing addresses.</span>
      <span class="dashboard-card-link">Manage</span>
    </div>
  </a>
  <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'tax-exempt' ) ) ?>" class="dashboard-link">
    <div class="dashboard-card taxes">
      <span class="dashboard-card-heading">Tax Exemptions</span>
      <span class="dashboard-card-body">Manage your shipping and billing addresses.</span>
      <span class="dashboard-card-link">View</span>
    </div>
  </a>
  <a href="/contact-us" class="dashboard-link">
    <div class="dashboard-card contact">
      <span class="dashboard-card-heading">Need Help?</span>
      <span class="dashboard-card-link">Contact Us</span>
    </div>
  </a>
</div>
<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
