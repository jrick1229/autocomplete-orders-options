<?php

/**
 * Add settings tab
 */

class WC_autocomplete_Settings {
    
    public static $option_prefix = 'woocommerce_autocomplete_orders';
    
    public static function init() {
        add_filter( 'woocommerce_subscription_settings', __CLASS__ . '::add_settings', 10, 1 );
    }

    public static function add_settings( $settings ) {

		return array_merge( $settings, array(
			array(
				'name'     => __( 'Autocomplete Orders', 'woocommerce-autocomplete-orders' ),
				'type'     => 'title',
				'id'       => self::$option_prefix,
			),
			array(
                'name'     => __( 'Autocomplete Order Type', 'woocommerce-autocomplete-orders' ),
                'desc'     => __( 'Choose whether you would like all orders to autocomplete or just subscription orders here. Autocomplete will occur once payment has been made and cleared.' ),
                'id'       => self::$option_prefix,
                'css'      => 'min-width:150px;',
                'default'  => 'None',
                'type'     => 'select',
                'options'  => array(
                    'none'        => _x( 'Never (do not autocomplete any orders)', 'woocommerce-autocomplete-orders' ),
                    'subscription_orders'   => _x( 'Subscription orders only (only autocomplete orders that have a related subscription)', 'woocommerce-autocomplete-orders' ),
                    'all_orders' => _x( 'All Orders (autocomplete all orders after payment is completed)', 'woocommerce-autocomplete-orders' ),
                ),
                'desc_tip' => true,
            ),
			array( 'type' => 'sectionend', 'id' => self::$option_prefix ),
		) );
        
	}
        
}
WC_autocomplete_Settings::init();


/**
 * What to do based on the selected option from above
 */

if ( 'all_orders' == get_option( WC_autocomplete_Settings::$option_prefix ) ) {
    
    add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
    
    function custom_woocommerce_auto_complete_order( $order_id ) {
        
        if ( ! $order_id ) {
            return;
        }

        $order = wc_get_order( $order_id );
        $order->update_status( 'completed' );
    }
    
    add_filter( 'woocommerce_payment_complete_order_status', 'wcs_aco_return_completed', 10, 3);
    
    function wcs_aco_return_completed( $status, $order, $order_id ) {
        
        if( wcs_order_contains_subscription($order, array('parent','renewal') ) ) {
            return 'completed';
        }

        return $status;
    }
    
}
elseif ( 'subscription_orders' == get_option( WC_autocomplete_Settings::$option_prefix ) ) {

    add_filter( 'woocommerce_payment_complete_order_status', 'wcs_aco_return_completed', 10, 3);
    
    function wcs_aco_return_completed( $status, $order, $order_id ) {
        
        if( wcs_order_contains_subscription($order, array('parent','renewal') ) ) {
            return 'completed';
        }

        return $status;
    }
    
}
else {}
