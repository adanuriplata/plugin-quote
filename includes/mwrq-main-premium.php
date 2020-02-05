<?php
/**
 * This file belongs to MOTIFCREATIVES
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MOTIF_Request_Quote_Frontend_Premium' ) ) {

	class MOTIF_Request_Quote_Frontend_Premium extends MOTIF_Rquest_Quote {

		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {

			parent::__construct();

			$settings_mwrq = get_option('mwrq_settings_array');
            // enable shop for product single 
            if($settings_mwrq['mwrw_showbtn_shop'] == 'yes' ){
				if($settings_mwrq['mwrq_user_type'] == 'all' ) {
					add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_button_shop' ), 15 );
	            } else if($settings_mwrq['mwrq_user_type'] == 'login' && is_user_logged_in()) {
	            	add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_button_shop' ), 15 );
	            } else if($settings_mwrq['mwrq_user_type'] == 'guest' && !is_user_logged_in()) {
	            	add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_button_shop' ), 15 );
	            }
			}
			
		}

		public function ajax_refresh_quote_list() {
			$raq_content  = MOTIF_Rquest_Quote()->get_raq_return();
			$args         = array(
				'raq_content'      => $raq_content,
				'template_part'    => 'view',
				'title'            => isset( $_POST['title'] ) ? $_POST['title'] : '',
				'item_plural_name' => isset( $_POST['item_plural_name'] ) ? $_POST['item_plural_name'] : '',
				'item_name'        => isset( $_POST['item_name'] ) ? $_POST['item_name'] : '',
				'show_thumbnail'   => isset( $_POST['show_thumbnail'] ) ? $_POST['show_thumbnail'] : 1,
				'show_price'       => isset( $_POST['show_price'] ) ? $_POST['show_price'] : 1,
				'show_quantity'    => isset( $_POST['show_quantity'] ) ? $_POST['show_quantity'] : 1,
				'show_variations'  => isset( $_POST['show_variations'] ) ? $_POST['show_variations'] : 1,
				'widget_type'      => isset( $_POST['widget_type'] ) ? $_POST['widget_type'] : '',
			);
			$args['args'] = $args;

			wp_send_json(
				array(
					'large' => wc_get_template_html( 'widget/quote-list.php', $args, '', mwrq_path . '/' ),
				)
			);

			die();
		}

		public function add_button_shop() {

			// show in other pages
			$show_button = true;

			global $product;

			if ( ! $product ) {
				return false;
			}

			$type_in_loop = apply_filters( 'motif_mwrq_show_button_in_loop_product_type', array( 'simple', 'subscription', 'external' ) );

			if ( $show_button != 'yes' || ! $product->is_type( $type_in_loop ) ) {
				return false;
			}

			if ( ! function_exists( 'MOTIF_Rquest_Quote_Frontend' ) ) {
				require_once( mwrq_inc . 'mwrq-main-front.php' );
				MOTIF_Rquest_Quote_Frontend();
			}

			MOTIF_Rquest_Quote_Frontend()->print_button( $product );
		}
	}
}
function MOTIF_Request_Quote_Frontend_Premium() {
	return MOTIF_Request_Quote_Frontend_Premium::get_instance();
}

