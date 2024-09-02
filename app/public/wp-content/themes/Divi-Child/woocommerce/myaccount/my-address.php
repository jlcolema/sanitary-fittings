<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
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

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}

$has_billing_address = ! empty( wc_get_account_formatted_address( 'billing', $customer_id ) );
$first_name = get_user_meta($customer_id, 'first_name', true);
$last_name = get_user_meta($customer_id, 'last_name', true);
$company = get_user_meta($customer_id, 'billing_company', true);
$address_1 = get_user_meta($customer_id, 'billing_address_1', true);
$address_2 = get_user_meta($customer_id, 'billing_address_2', true);
$city = get_user_meta($customer_id, 'billing_city', true);
$state = get_user_meta($customer_id, 'billing_state', true);
$zipcode = get_user_meta($customer_id, 'billing_postcode', true);

$has_shipping_address = ! empty( wc_get_account_formatted_address( 'shipping', $customer_id ) );
$shipping_first_name = get_user_meta($customer_id, 'shipping_first_name', true);
$shipping_last_name = get_user_meta($customer_id, 'shipping_last_name', true);
$shipping_company = get_user_meta($customer_id, 'shipping_company', true);
$shipping_address_1 = get_user_meta($customer_id, 'shipping_address_1', true);
$shipping_address_2 = get_user_meta($customer_id, 'shipping_address_2', true);
$shipping_city = get_user_meta($customer_id, 'shipping_city', true);
$shipping_state = get_user_meta($customer_id, 'shipping_state', true);
$shipping_zipcode = get_user_meta($customer_id, 'shipping_postcode', true);

$oldcol = 1;
$col    = 1;
?>

<span class="account-back"><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ) ?>">Addresses</a></span>
<h1 class="account-heading"> Addresses </h1>

<div class="account-addresses">
<?php if ( $has_billing_address ) {
    // User has a billing address
    echo '<div class="address-heading">Billing Address</div>
          <div class="address-block">
            <div class="address-left">
              <div class="address-names">
                <span class="address-name">'.$first_name.' '.$last_name.'</span> | '.$company.'
              </div>
              <div class="address-location">
              '.$address_1.', '.$city.', '.$state.' '.$zipcode.'
              </div>
            </div>
            <div class="address-right">
              <a href="'.esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ).'">Edit</a> 
            </div>
          </div>';
} else {
    // User doesn't have a billing address
    echo '<div class="address-heading">Billing Address</div>
          <a class="account-btn" href="'.esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ).'">Add new address</a>';
}

if ( $has_shipping_address ) {
    // User has a shipping address
    echo '<div class="address-heading">Shipping Address</div>
          <div class="address-block">
            <div class="address-left">
              <div class="address-names">
                <span class="address-name">'.$shipping_first_name.' '.$shipping_last_name.'</span> | '.$shipping_company.'
              </div>
              <div class="address-location">
              '.$shipping_address_1.', '.$shipping_city.', '.$shipping_state.' '.$shipping_zipcode.'
              </div>
            </div>
            <div class="address-right">
            <a href="'.esc_url( wc_get_endpoint_url( 'edit-address', 'shipping' ) ).'">Edit</a> 
            </div>                                                                                           
          </div>';
} else {
    // User doesn't have a shipping address
    echo '<div class="address-heading">Shipping Address</div>
          <a class="account-btn" href="'.esc_url( wc_get_endpoint_url( 'edit-address', 'shipping' ) ).'">Add new address</a>';
}?> 
</div>
