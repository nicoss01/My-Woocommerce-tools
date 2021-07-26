<?php
/*
 * Plugin Name: CodNex - WC Same Price for all Country
 * Plugin URI: https://www.codnex.net
 * Description: Sell product with the same price whatever the destination country including tax. same price regardless of taxes 
 * Author: Nicolas Grillet
 * Version: 1.0
 */

add_filter( 'woocommerce_adjust_non_base_location_prices', '__return_false' );
?>