<?php
/**
 * Customer on-hold order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-on-hold-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 7.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( __( 'Hi %s,', 'woocommerce' ), $order->get_billing_first_name() ); ?></p><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
<p>Thank you for your order!  You selected <b>"Invoice Payment"</b> as your method of payment during checkout.  If your company has been approved to purchase on account you're all set!</p>
<div style="background-color:#444;padding:10px;">
<p style="color:#fff;margin-bottom:2px;">If you have not filled out a credit application with Sanitary Fittings, <a href="https://sanitaryfittings.us/wp-content/uploads/2017/05/CreditApplication_SF.pdf">click here.</a></p>
<p style="color:#fff;margin-bottom:2px;">Completed credit applications can be emailed to:  <a href="mailto:john@sanitaryfittings.us?Subject=Credit%20Application">john@sanitaryfittings.us</a></p>
</div>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

?>
<p>
<?php _e( 'We look forward to fulfilling your order soon.', 'woocommerce' ); // phpcs:ignore WordPress.XSS.EscapeOutput ?>
</p>
<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );