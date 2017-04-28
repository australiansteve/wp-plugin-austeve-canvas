<?php

/**
 * Auto Complete all WooCommerce orders.
 */
add_action( 'woocommerce_thankyou', 'austeve_woocommerce_auto_complete_order', 5, 1 );
function austeve_woocommerce_auto_complete_order( $order_id ) { 
    if ( ! $order_id ) {
        return;
    }

    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
}

/**
 * Process items as they are added to the cart
 */
function austeve_filter_woocommerce_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) { 

	$now = new DateTime();
	$wc_expiry = get_field('_expiration_date', $product_id);

	//If an event has already expired, stop it from being added to the cart
	if ($wc_expiry && $wc_expiry < $now)
	{
        throw new Exception( __( 'Sorry, this event has already expired and tickets can no longer be purchased.', 'woocommerce' ) );
	}

    return $cart_item_data; 
}; 
add_filter( 'woocommerce_add_cart_item_data', 'austeve_filter_woocommerce_add_cart_item_data', 10, 3 ); 

?>