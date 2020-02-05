<?php 
/*
Plugin Name: Motif WooCommerce Request a Quote
Plugin URI: http://demo.motif-solution.com/request-a-quote/shop/
Description: Motif WooCommerce Request a Quote
Author: motifcreatives
Version: 1.0.0
Developed By: motifcreatives
Author URI: http://demo.motif-solution.com/request-a-quote/shop/
Support: http://support@extendons.com
textdomain: motif-woocommerce-request-a-quote
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

// module constant
if ( !defined( 'mwrq_url' ) )
define( 'mwrq_url', plugin_dir_url( __FILE__ ) );

if ( !defined( 'mwrq_basename' ) )
define( 'mwrq_basename', plugin_basename( __FILE__ ) );

if ( ! defined( 'mwrq_dir' ) )
define( 'mwrq_dir', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'mwrq_inc' ) ) {
    define( 'mwrq_inc', mwrq_dir . '/includes/' );
}

if ( ! defined( 'mwrq_path' ) ) {
    define( 'mwrq_path', mwrq_dir . '/templates/' );
}

// woocommerce error
if( ! function_exists('mwrq_install_woocommerce_admin_notice') ){
	function mwrq_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'Motif Woocommerce Request A Quote is enabled but not effective. It requires WooCommerce in order to work.', '' ); ?></p>
        </div>
        <?php
    }
}

// check plguin is active or not
if ( ! function_exists( 'mwrq_plugin_install' ) ) {
	function mwrq_plugin_install() {
        if ( !function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'mwrq_install_woocommerce_admin_notice' );
            return false ;
        } else {
            do_action( 'motif_mwrq_init' );
        }
    }

    add_action( 'plugins_loaded', 'mwrq_plugin_install', 12 );
}

if ( ! function_exists( 'mwrq_constructor' ) ) {

	function mwrq_constructor() { 
        if ( ! function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'mwrq_install_woocommerce_admin_notice' );
           	return false ;
        }
        load_plugin_textdomain( 'motif-woocommerce-request-a-quote', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        
        // files include
        require_once( mwrq_inc . 'mwrq-function.php' );
        require_once( mwrq_inc . 'mwrq.php' );
        require_once( mwrq_inc . 'mwrq-main-premium.php' );

        MOTIF_Request_Quote_Frontend_Premium();
    }
    add_action( 'motif_mwrq_init', 'mwrq_constructor' );
}

if ( ! function_exists( 'mwrq_activation_plugin' ) ) {
    register_activation_hook( __FILE__, 'mwrq_activation_plugin');

    function mwrq_activation_plugin() {

        $mwrq_settings = array(
            "mwrq_user_type" => esc_html__('all', 'motif-woocommerce-request-a-quote'),
            "mwrq_button_type" => esc_html__('button', 'motif-woocommerce-request-a-quote'),
            "mwrq_button_lable" => esc_html__('Add to Quote', 'motif-woocommerce-request-a-quote'),
            "mwrq_browse_lable" => esc_html__('Browse the list Product Added', 'motif-woocommerce-request-a-quote'),
            "mwrq_already_in" => esc_html__('Already Added', 'motif-woocommerce-request-a-quote'),
            "mwrq_btn_text_color" => esc_html__('#0a0a0a', 'motif-woocommerce-request-a-quote'),
            "mwrq_btn_bg_color" => esc_html__('#2ab736', 'motif-woocommerce-request-a-quote'),
            "mwrw_showbtn_shop" => esc_html__('yes', 'motif-woocommerce-request-a-quote'),
            "mwrw_showbtn_single" => esc_html__('yes', 'motif-woocommerce-request-a-quote'),
            "mwrq_list_page" => esc_html__('','motif-woocommerce-request-a-quote'),
            "mwrw_display_sku" => esc_html__('yes', 'motif-woocommerce-request-a-quote'),
            "mwrw_hide_subtotal" => esc_html__('no', 'motif-woocommerce-request-a-quote'),
            "mwrw_return_shop" => esc_html__('yes', 'motif-woocommerce-request-a-quote'),
            "mwrq_returnshop" => esc_html__('Return to Shop', 'motif-woocommerce-request-a-quote'),
            "mwrq_returnshop_link" => get_permalink(wc_get_page_id('shop')),
            "mwrw_update_list" => esc_html__('yes', 'motif-woocommerce-request-a-quote'),
            "mwrq_update_lable" => esc_html__('Update Quote List', 'motif-woocommerce-request-a-quote'),
            "mwrw_display_form" => esc_html__('yes', 'motif-woocommerce-request-a-quote'),
            "mwrw_show_alltotal" => esc_html__('yes', 'motif-woocommerce-request-a-quote'),

        );

        update_option('mwrq_settings_array',$mwrq_settings);
    }
}

