<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );

$current_url = $_SERVER['REQUEST_URI'];
$url_parts = explode('/', rtrim($current_url, '/')); 
$last_segment = end($url_parts); // Get the last segment
$page_title;

if ($last_segment == 'edit-address') {
  $page_title = 'Addresses';
} elseif ($last_segment == 'payment-methods') {
  $page_title = 'Payment Methods';
} elseif ($last_segment == 'edit-account') {
  $page_title = 'Account Details';
} elseif ($last_segment == 'tax-exempt') {
  $page_title = 'Tax Exemptions';
} elseif ($last_segment == 'orders') {
  $page_title = 'Orders';
} elseif ($last_segment == 'billing') {
  $page_title = 'Billing';
} elseif ($last_segment == 'shipping') {
  $page_title = 'Shipping';
} elseif ($last_segment == 'my-account') {
  $page_title = 'Dashboard';
}
?>

<?php if ($page_title == 'Billing' || $page_title == 'Shipping' ) {
  echo '<div class="account-breadcrumbs">
          <a href="'.esc_url( wc_get_account_endpoint_url( 'dashboard' ) ).'">My Account</a>  /  <a href="'.esc_url( wc_get_account_endpoint_url( 'edit-address' ) ).'">Addresses</a>  /  <span class="account-active-page">'. $page_title .'</span>
        </div>';
} elseif ($page_title == 'Dashboard') {
  echo '<div class="account-breadcrumbs">
          My Account  /  <span class="account-active-page">'. $page_title .'</span>
        </div>';
} else {
  echo '<div class="account-breadcrumbs">
          <a href="'.esc_url( wc_get_account_endpoint_url( 'dashboard' ) ).'">My Account</a>  /  <span class="account-active-page">'. $page_title .'</span>
        </div>';
} ?>

<div class="my-account-nav">
  <div class="welcome-message">
    <?php if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    echo '<span class="user-welcome">Hello,<br><strong> '. esc_html ( $current_user->display_name ).'</strong></span> ';
    } ?>
  </div>

  <nav class="MyAccount-navigation">
    <section class="main-account-nav">
      <span class="account-nav-heading">Manage Account</span>
      <?php wp_nav_menu( array(
        'theme_location' => 'first-account-menu',
        'menu_class'     => 'first-account-menu',
      ) ); ?>
    </section>

    <section class="second-account-nav">
      <span class="account-nav-heading">Customer Service</span>
      <?php wp_nav_menu( array(
        'theme_location' => 'second-account-menu',
        'menu_class'     => 'second-account-menu',
      ) ); ?>
    </section>

    <section class="third-account-nav">    
      <ul class="third-account-menu">
        <li><a href="<?php echo wp_logout_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php _e( 'Sign Out', 'woocommerce' ); ?></a></li>
      </ul>
    </section>
  </nav>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
