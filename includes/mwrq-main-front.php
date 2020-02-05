<?php
/**
 * This file belongs to MOTIFCREATIVES
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'MOTIF_Rquest_Quote_Frontend' ) ) {

    class MOTIF_Rquest_Quote_Frontend {

        protected static $instance;

        public $shortcodes;

    	public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {

            add_action( 'wp_loaded', array( $this, 'update_raq_list' ) );
            
            add_action( 'wp_enqueue_scripts', array( $this, 'mwrq_front_scripts_loading' ),10 );

            $settings_mwrq = get_option('mwrq_settings_array');
            // enable shop for product single 
            if($settings_mwrq['mwrw_showbtn_single'] == 'yes' ) {
                if($settings_mwrq['mwrq_user_type'] == 'all' ) {
                add_action( 'woocommerce_before_single_product', array( $this, 'mwrq_show_button_single_page' ) );
                } else if($settings_mwrq['mwrq_user_type'] == 'login' && is_user_logged_in()) {
                    add_action( 'woocommerce_before_single_product', array( $this, 'mwrq_show_button_single_page' ) );
                } else if($settings_mwrq['mwrq_user_type'] == 'guest' && !is_user_logged_in() ) {
                    add_action( 'woocommerce_before_single_product', array( $this, 'mwrq_show_button_single_page' ) );
                }
            }
            
            $this->shortcodes = new Motif_RequestToQuote_Shortcodes();
        }

        public function mwrq_show_button_single_page() {
            
            global $product;

            if( ! $product ) {
                
                global  $post;
                
                if (  ! $post || ! is_object( $post ) || ! is_singular() ) {
                    return;
                }
                
                $product = wc_get_product( $post->ID);
            }

            if($product->is_in_stock() && $product->get_price() !== '' ) {
                if( $product->is_type('variable')  ) {
                    add_action( 'woocommerce_after_single_variation', array(  $this, 'mwrq_add_button_single_page' ),15 );
                } else {
                    add_action( 'woocommerce_after_add_to_cart_button', array(  $this, 'mwrq_add_button_single_page' ),15 );
                }
            }else{
                add_action( 'woocommerce_single_product_summary', array( $this, 'mwrq_add_button_single_page' ), 35 );
            }
        }

        public function mwrq_add_button_single_page() {

            $show_button = 'yes';
            if ( $show_button == 'yes' ) {
                $this->print_button();
            }
        }

        public function print_button( $product_id = false ) {

            $settings_mwrq = get_option('mwrq_settings_array');

            if ( ! $product_id ) {
                global $product;
            } else {
                $product = wc_get_product( $product_id );
            }


            $buttonStyle = ( $settings_mwrq['mwrq_button_type'] == 'button' ) ? 'button' : 'link';
            $product_id   = $product->get_id();

            $args         = array(
                'class'         => 'mwrq-quote-button ' . $buttonStyle,
                'wpnonce'       => wp_create_nonce( 'add-request-quote-' . $product_id ),
                'product_id'    => $product_id,
                'label'         => $settings_mwrq['mwrq_button_lable'],
                'label_browse'  => $settings_mwrq['mwrq_browse_lable'],
                'template_part' => 'button',
                'already_in'    => $settings_mwrq['mwrq_already_in'],
                'rqa_url'       => MOTIF_Rquest_Quote()->get_raq_page_url(),
                'exists'        => ( $product->is_type('variable') ) ? false : MOTIF_Rquest_Quote()->exists( $product_id ),
            );

            if( $product->is_type('variable')){
                $args['variations'] = implode( ',', MOTIF_Rquest_Quote()->raq_variations );
            }

            $args['args'] = $args;

            $template_button = 'add-to-quote.php';
            
            wc_get_template( $template_button, apply_filters('mwrq_args', $args), '', mwrq_path.'/');

        }

        public function mwrq_front_scripts_loading() {

            $settings_mwrq = get_option('mwrq_settings_array');

            wp_enqueue_script('jquery');

            wp_enqueue_script( 'mwrq-frontend', plugins_url( '/../assets/js/frontoffice.js', __FILE__ ), false );
            $localize_script_args =  array(
                'ajaxurl'                => WC_AJAX::get_endpoint( "%%endpoint%%" ),
                'current_lang'           => "",
                'no_product_in_list'     => mwrq_get_list_empty_message(),
                'block_loader'           => mwrq_url . 'assets/images/spinner.gif',
                'go_to_the_list'         => 'no',
                'rqa_url'                => MOTIF_Rquest_Quote()->get_redirect_page_url(),
                'current_user_id'        => is_user_logged_in() ? get_current_user_id() : '',
                'hide_price'             => 0,
                'select_quanitity'       => apply_filters('motif_mwrq_select_quantity_grouped_label',__('Set at least the quantity for a product',''))
            );

            wp_localize_script( 'mwrq-frontend', 'mwrq_frontend', apply_filters( 'motif_mwrq_frontend_localize', $localize_script_args ) );

            wp_enqueue_style( 'motif_frontend', plugins_url( '/../assets/css/frontoffice.css', __FILE__ ), false );

            $mwrq_custom_css = "
                .motif-mwrq-add-button.show a {
                    color: {$settings_mwrq['mwrq_btn_text_color']};
                    background-color: {$settings_mwrq['mwrq_btn_bg_color']};
                }";

            wp_add_inline_style( 'motif_frontend', $mwrq_custom_css );
        }

        public function update_raq_list() {

            if ( isset( $_POST['update_raq_wpnonce'] ) && isset( $_POST['raq'] ) && wp_verify_nonce( $_POST['update_raq_wpnonce'], 'update-request-quote-quantity' ) ) {

                foreach ( $_POST['raq'] as $key => $value ) {

                    if ( $value['qty'] != 0 ) {

                        MOTIF_Rquest_Quote()->update_item( $key, 'quantity', $value['qty'] );
                    }
                    else {
                        MOTIF_Rquest_Quote()->remove_item( $key );
                    }
                }
            }
        }

    } 

    function MOTIF_Rquest_Quote_Frontend() {
            return MOTIF_Rquest_Quote_Frontend::get_instance();
    }
}