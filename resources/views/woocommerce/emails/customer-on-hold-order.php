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
 * @package WooCommerce\Templates\Emails
 * @version 7.3.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>Ta wiadomość jest generowana automatycznie, prosimy na nią nie odpowiadać</p>

<p>Dzień dobry,</p>

<?php
// Inicjalizacja zmiennych na dane wydarzenia
$event_name = '';
$event_date = '';
$event_place = '';
$event_city = '';

// Pobierz produkty z zamówienia
$items = $order->get_items();

// Sprawdź, czy są produkty w zamówieniu
if ( ! empty( $items ) ) {
    // Pobierz pierwszy produkt z zamówienia
    $item = array_shift( $items );
    $product = $item->get_product();

    if ( $product ) {
        $product_id = $product->get_id();
        
        // Pobierz dane z pól ACF na podstawie ID produktu
        $event_name = $product->get_name();
        $event_date_raw = get_field('event_date', $product_id); // Format Ymd
        $event_place = get_field('miejsce', $product_id);
        $event_city = get_field('city', $product_id);

        // Sformatuj datę do czytelnej formy, np. "13-15 maja 2026"
        // Ta część może wymagać dostosowania, jeśli masz pole na datę końcową
        if($event_date_raw) {
            $date_obj = date_create_from_format('Ymd', $event_date_raw);
            if ($date_obj) {
                // Ustawienie polskiej lokalizacji dla nazw miesięcy
                setlocale(LC_TIME, 'pl_PL.UTF-8');
                $event_date = strftime('%e %B %Y', $date_obj->getTimestamp());
            }
        }
    }
}
?>

<p>bardzo dziękujemy za rejestrację na warsztaty <?php echo esc_html( $event_name ); ?>, które odbędą się w dniu <?php echo esc_html( $event_date ); ?> w <?php echo esc_html( $event_place ); ?>. <b>Twoje zgłoszenie oczekuje na płatność.</b></p>

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

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
