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
 * Add 'Return to Home' button after displaying order details.
 */
add_action( 'woocommerce_thankyou', 'austeve_woocommerce_return_to_home', 50, 0 );
function austeve_woocommerce_return_to_home( ) { 
	error_log("Return home");

?>
    <div class='row columns text-center'>
    <a class='button' href='<?php echo site_url();?>'>Return home</a>
    </div>
<?php
}

?>