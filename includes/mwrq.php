<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'MOTIF_Rquest_Quote' ) ) {

    class MOTIF_Rquest_Quote {
        
        protected static $instance;

        public $session_class;

        public $raq_content = array();

        public $variation_raq = array();
   
    	public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {
            
            $this->start_more();

            add_action( 'init', array( $this, 'start_session' ));

            add_action( 'wc_ajax_motif_mwrq_action', array( $this, 'ajax' ) );

            add_action( 'wp_loaded', array( $this, 'add_to_quote_action' ), 30);

            add_action( 'wp_loaded', array( $this, 'init' ), 30 );
            add_action( 'wp', array( $this, 'maybe_set_raq_cookies' ), 99 );

            add_action( 'shutdown', array( $this, 'maybe_set_raq_cookies' ), 0 ); 

            add_action( 'widgets_init', array( $this, 'register_widgets' ) );
        }

        public function register_widgets() {
            register_widget( 'Motif_mwrq_widget_quote' );
        }

        private function start_more(){

            if ( ! class_exists( 'WC_Session' ) ) {
                require_once( WC()->plugin_path() . '/includes/abstracts/abstract-wc-session.php' );
            }
            require_once( mwrq_inc . 'mwrq-session.php' );

            // admin class include
            require_once( mwrq_inc . 'mwrq-main-admin.php' );
            Motif_Request_Qoute_Admin();

            // shortcode and fornt file include
            require_once( mwrq_inc . 'mwrq-shortcode.php' );
            require_once( mwrq_inc . 'mwrq-main-front.php' );
            MOTIF_Rquest_Quote_Frontend();

            require_once( mwrq_inc . 'mwrq-form.php' );
            Motif_Request_Default_Form();

            require_once( mwrq_inc . 'mwrq-widget.php' );

        }

        function start_session() {
            if ( ! isset( $_COOKIE['woocommerce_items_in_cart'] ) ) {
                do_action( 'woocommerce_set_cart_cookies', true );
            }
            $this->session_class = new Motif_Request_Session();
            $this->set_session();
        }

        function init() {
            $this->get_raq_for_session();
            $this->session_class->set_customer_session_cookie( true );
            $this->raq_variations = $this->get_variations_list();
        }

        // retur request to qoute page url
        public function get_raq_page_url() {

            $settings_mwrq = get_option('mwrq_settings_array');

            if(!empty($settings_mwrq['mwrq_list_page'])) {
                $obj_ess = get_page_by_path($settings_mwrq['mwrq_list_page']);
                $page_link = $obj_ess->guid;
            } else {
                $obj_es = get_page_by_path( 'request-to-quote-motif' );
                $page_link = $obj_es->guid;
            }

            return apply_filters( 'mwrq_request_page_url', $page_link );
        }
        
        function get_raq_return() {
            return $this->raq_content;
        }

        function get_variations_list() {
            $variations = array();
            if ( ! empty( $this->raq_content ) ) {
                foreach ( $this->raq_content as $item ) {
                    if ( isset( $item['variation_id'] ) && $item['variation_id'] != 0 ) {
                        $variations[] = $item['variation_id'];
                    }
                }
            }
            return $variations;
        }

        public function get_errors( $errors , $html = true ) {
            return implode( ( $html ? '<br />' : ', ' ), $errors );
        }

        public function is_empty() {
            return empty( $this->raq_content );
        }

        public function get_raq_item_number() {
            return count( $this->raq_content );
        }

        public function set_session( $raq_session = array(), $can_be_empty = false ) {
            if ( empty( $raq_session ) && ! $can_be_empty ) {
                $raq_session = $this->get_raq_for_session();
            }

            // Set raq  session data
            $this->session_class->set( 'raq', $raq_session );

            do_action( 'motif_mwrq_updated' );
        }

        public function unset_session() {
            $this->session_class->__unset( 'raq' );
        }

        function get_raq_for_session() {
            $this->raq_content = $this->session_class->get( 'raq', array() );
            return $this->raq_content;
        }

        function maybe_set_raq_cookies() {
            $set = true;

            if ( !headers_sent() ) {
                if ( sizeof( $this->raq_content ) > 0 ) {
                    $this->set_rqa_cookies( true );
                    $set = true;
                }
                elseif ( isset( $_COOKIE['motif_mwrq_items_in_raq'] ) ) {
                    $this->set_rqa_cookies( false );
                    $set = false;
                }
            }

            do_action( 'motif_mwrq_set_raq_cookies', $set );
        }

        private function set_rqa_cookies( $set = true ) {
            if ( $set ) {
                wc_setcookie( 'motif_mwrq_items_in_raq', 1 );
                wc_setcookie( 'motif_mwrq_hash', md5( json_encode( $this->raq_content ) ) );
            }
            elseif ( isset( $_COOKIE['motif_mwrq_items_in_raq'] ) ) {
                wc_setcookie( 'motif_mwrq_items_in_raq', 0, time() - HOUR_IN_SECONDS );
                wc_setcookie( 'motif_mwrq_hash', '', time() - HOUR_IN_SECONDS );
            }
            do_action( 'motif_mwrq_set_rqa_cookies', $set );
        }

        public function exists( $product_id, $variation_id = false, $postadata = false ) {

            $return = false;

            if ( $variation_id ) {
                $key_to_find = md5( $product_id . $variation_id );
            } else {
                $key_to_find = md5( $product_id );
            }

            if ( array_key_exists( $key_to_find, $this->raq_content ) ) {
                $this->errors[] = esc_html__( 'Product already in the list.', '' );
                $return         = true;
            }

            return apply_filters( 'mwrq_exists_in_list', $return, $product_id, $variation_id, $postadata, $this->raq_content );
        }

        public function add_item( $product_raq ) {

            $return = '';

            if ( ! ( isset( $product_raq['variation_id'] ) && $product_raq['variation_id'] != '' ) ) {

                $product      = wc_get_product( $product_raq['product_id'] );

                if ( $product->is_type('grouped') ) {
                    if ( is_array( $product_raq['quantity'] ) ) {

                        foreach ( $product_raq['quantity'] as $item_id => $quantity ) {
                            if ( ! $this->exists( $item_id ) && $quantity != 0 ) {
                                $raq = array(
                                    'product_id' => $item_id,
                                    'quantity'   => $quantity
                                );

                                $raq  = apply_filters( 'mwrq_add_item', $raq, $product_raq );
                                $this->raq_content[ apply_filters( 'mwrq_quote_item_id', md5( $item_id ), $product_raq ) ] = $raq;
                            }
                        }
                    }
                } else {
                    //single product
                    if ( ! $this->exists( $product_raq['product_id'] ) ) {

                        $product_raq['quantity'] = ( isset( $product_raq['quantity'] ) ) ? $product_raq['quantity'] : 1;

                        $raq = array(
                            'product_id' => $product_raq['product_id'],
                            'quantity'   => $product_raq['quantity']
                        );

                        $raq = apply_filters( 'mwrq_add_item', $raq, $product_raq );

                        $this->raq_content[ apply_filters( 'mwrq_quote_item_id', md5( $product_raq['product_id'] ), $product_raq ) ] = $raq;

                    } else {
                        $return = 'exists';
                    }
                }

            } else {

                //variable product
                if ( ! $this->exists( $product_raq['product_id'], $product_raq['variation_id'] ) ) {


                    $product_raq['quantity'] = ( isset( $product_raq['quantity'] ) ) ?  $product_raq['quantity'] : 1;

                    $raq = array(
                        'product_id'   => $product_raq['product_id'],
                        'variation_id' => $product_raq['variation_id'],
                        'quantity'     => $product_raq['quantity']
                    );

                    $raq = apply_filters( 'mwrq_add_item', $raq, $product_raq );

                    $variations = array();

                    foreach ( $product_raq as $key => $value ) {

                        if ( stripos( $key, 'attribute' ) !== false ) {
                            $variations[ $key ] = urldecode($value);
                        }
                    }

                    $raq ['variations'] = $variations;

                    $this->raq_content[ apply_filters( 'mwrq_quote_item_id', md5( $product_raq['product_id'] . $product_raq['variation_id'] ), $product_raq ) ] = $raq;

                } else {
                    $return = 'exists';
                }
            }

            if ( $return != 'exists' ) {

                $this->set_session( $this->raq_content );

                $return = 'true';

                $this->set_rqa_cookies( sizeof( $this->raq_content ) > 0 );


            }

            return $return;
        }

        public function remove_item( $key ) {
            if ( isset( $this->raq_content[$key] ) ) {
                unset( $this->raq_content[$key] );
                $this->set_session( $this->raq_content, true );
                $this->raq_variations = $this->get_variations_list();
                return true;
            }
            else {
                return false;
            }
        }

        public function clear_raq_list() {
            $this->raq_content = array();
            $this->set_session( $this->raq_content, true );
        }

        public function update_item( $key, $field = false, $value ) {

            if ( $field && isset( $this->raq_content[$key][$field] ) ) {
                $this->raq_content[$key][$field] = $value;
                $this->set_session( $this->raq_content );

            }
            elseif ( isset( $this->raq_content[$key] ) ) {
                $this->raq_content[$key] = $value;
                $this->set_session( $this->raq_content );
            }
            else {
                return false;
            }

            $this->set_session( $this->raq_content );
            return true;
        }

        public function ajax() {
            if ( isset( $_POST['mwrq_action'] ) ) {
                if ( method_exists( $this, 'ajax_' . $_POST['mwrq_action'] ) ) {
                    $s = 'ajax_' . $_POST['mwrq_action'];
                    $this->$s();
                }
            }
        }

        public function ajax_add_item() {

            $return  = 'false';
            $message = '';
            $errors = array();
            $product_id         = ( isset( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) ? (int) $_POST['product_id'] : false;
            $is_valid_variation = ( isset( $_POST['variation_id'] ) && ! empty( $_POST['variation_id'] ) ) ? is_numeric( $_POST['variation_id'] ) : true;

            $is_valid = apply_filters( 'mwrq_ajax_add_item_is_valid', $product_id && $is_valid_variation, $product_id );

            $postdata = $_POST;

            $postdata = apply_filters('mwrq_ajax_add_item_prepare', $postdata, $product_id );


            if ( !$is_valid ) {
                $errors[] = esc_html__( 'Error occurred while adding product to Request a Quote list.', 'motif-woocommerce-request-a-quote' );
            }
            else {
                $return = $this->add_item( $postdata );
            }

            if ( $return == 'true' ) {
                $message = esc_html__("Product added", "motif-woocommerce-request-a-quote");
            }
            elseif ( $return == 'exists' ) {
                $message = esc_html__("already in quote", "motif-woocommerce-request-a-quote");
            }
            elseif ( count( $errors ) > 0 ) {
                $message = apply_filters( 'motif_mwrq_error_adding_to_list_message', $this->get_errors($errors) );
            }

            wp_send_json( apply_filters( 'motif_mwrq_ajax_add_item_json',
                array(
                    'result'       => $return,
                    'message'      => $message,
                    'rqa_url'      => $this->get_raq_page_url(),
                    'variations'   => implode(',',$this->get_variations_list())
                ) )
            );
        }

        public function add_to_quote_action() {

            if ( empty( $_REQUEST['add-to-quote'] ) || ! is_numeric( $_REQUEST['add-to-quote'] ) ) {
                return;
            }

            $product_id      = apply_filters( 'woocommerce_add_to_quote_product_id', absint( $_REQUEST['add-to-quote'] ) );
            $adding_to_quote = wc_get_product( $product_id );

            if( ! $adding_to_quote ){
                return;
            }

            $variation_id    = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
            $quantity        = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
            $error           = false;
            $raq_data        = array();

            if ( $adding_to_quote->is_type('variation') ) {
                $var_id = yit_get_prop( $adding_to_quote, 'variation_id', true );
                if ( ! empty( $var_id ) ) {
                    $product_id   = $adding_to_quote->get_id();
                    $variation_id = $var_id;
                }
            }

            if ( $adding_to_quote->is_type('variable')  ) {
                if ( empty( $variation_id ) ) {
                    $data_store   = WC_Data_Store::load( 'product' );
                    $variation_id = $data_store->find_matching_product_variation( $adding_to_quote, wp_unslash( $_POST ) );
                }

                if ( ! empty( $variation_id ) ) {
                    $attributes = $adding_to_quote->get_attributes();
                    $variation  = wc_get_product( $variation_id );

                    foreach ( $attributes as $attribute ) {
                        if ( ! $attribute['is_variation'] ) {
                            continue;
                        }

                        $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

                        if ( isset( $_REQUEST[ $taxonomy ] ) ) {

                            // Get value from post data
                            if ( $attribute['is_taxonomy'] ) {
                                // Don't use wc_clean as it destroys sanitized characters
                                $value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
                            } else {
                                $value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
                            }

                            $variation_data = yit_get_prop($variation, 'data', true);
                            // Get valid value from variation
                            $valid_value = isset( $variation_data['attributes'][$attribute['name']] ) ? $variation_data['attributes'][$attribute['name']] : '';

                            // Allow if valid
                            if ( '' === $valid_value || $valid_value === $value ) {
                                $raq_data[ $taxonomy ] = $value;
                                continue;
                            }

                        } else {
                            $missing_attributes[] = wc_attribute_label( $attribute['name'] );
                        }
                    }

                    if ( ! empty( $missing_attributes ) ) {
                        $error = true;
                        wc_add_notice( sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), '' ), wc_format_list_of_items( $missing_attributes ) ), 'error' );
                    }
                } elseif ( empty( $variation_id ) ) {
                    $error = true;
                    wc_add_notice( __( 'Please choose product options&hellip;', 'motif-woocommerce-request-a-quote' ), 'error' );
                }

            }

            if ( $error ) {
                return;
            }

            $raq_data = array_merge( array(
                'product_id'   => $product_id,
                'variation_id' => $variation_id,
                'quantity'     => $quantity,
            ), $raq_data);

            $return = $this->add_item( $raq_data );

            if ( $return == 'true' ) {
                $message = 'product_added';
                wc_add_notice( $message, 'success' );
            } elseif ( $return == 'exists' ) {
                $message = 'already_in_quote';
                wc_add_notice( $message, 'notice' );
            }

        }

        public function ajax_remove_item() {
            $product_id = ( isset( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) ? (int) $_POST['product_id'] : false;
            $is_valid   = $product_id && isset( $_POST['key'] );
            if ( $is_valid ) {
                echo $this->remove_item( $_POST['key'] );
            }
            else {
                echo false;
            }
            die();
        }

        public function ajax_update_item_quantity() {
            $result = array();
            $is_valid = isset($_POST['key']) && isset($_POST['quantity']);
            if ($is_valid) {
                $updates = $this->update_item_quantity($_POST['key'], $_POST['quantity']);
            }

            wp_send_json($updates);
        }

        public function update_item_quantity( $key, $quantity ) {

            $min = $max = $quantity;

            if ( isset( $this->raq_content[ $key ] ) ) {
                $quantity                              = ( $quantity <= $min ) ? $min : $quantity;
                $quantity                              = ( $quantity >= $max && '' != $max ) ? $max : $quantity;
                $this->raq_content[ $key ]['quantity'] = $quantity;

                $this->set_session( $this->raq_content, true );

                return true;

            }

            return false;
        }

        public function ajax_variation_exist() {
            if ( isset( $_POST['product_id'] ) && isset( $_POST['variation_id'] ) ) {

                $message       = '';
                $label_browser = '';
                $product_id    = ( $_POST['variation_id'] != '' ) ? $_POST['variation_id'] : $_POST['product_id'];
                $product       = wc_get_product( $product_id );

                if ( ( ! YITH_Request_Quote_Premium()->check_user_type() || ( ywraq_show_btn_only_out_of_stock() && $product->is_in_stock() ) ) ) {
                    $message = apply_filters( 'motif_mwrq_product_not_quoted', __( 'This product is not quotable.', '' ) );
                } elseif ( $this->exists( $_POST['product_id'], $_POST['variation_id'], $_POST ) == 'true' ) {
                    $message       = 'already_in_quote';
                    $label_browser = 'browse_list';
                }

                $return = ( $message == '' ) ? false : true;

                wp_send_json(
                    array(
                        'result'       => $return,
                        'message'      => $message,
                        'label_browse' => $label_browser,
                        'rqa_url'      => $this->get_raq_page_url(),
                    )
                );
            }
        }

        public function get_raq_page_id() {
            $page_id = get_option( 'mwrq_page_id' );

            if ( function_exists( 'wpml_object_id_filter' ) ) {
                global $sitepress;

                if ( !is_null( $sitepress ) && is_callable( array( $sitepress, 'get_current_language' ) ) ) {
                    $page_id = wpml_object_id_filter( $page_id, 'post', true, $sitepress->get_current_language() );
                }
            }

            return apply_filters( 'mwrq_request_page_id', $page_id );
        }

        public function has_thank_you_page() {

            if ( get_option( 'mwrq_activate_thank_you_page', 'no' ) == 'no' ) {
                return false;
            }

            return ( get_option( 'mwrq_thank_you_page' ) ) ? get_permalink( get_option( 'mwrq_thank_you_page' ) ) : false;
        }

        function get_username( $hyb_user_login, $hyb_user_email ) {
            $yith_user_login = $hyb_user_login;
            if ( !empty( $hyb_user_login ) ) {
                if ( get_option( 'woocommerce_registration_generate_username' ) == 'yes' && !empty( $hyb_user_email ) ) {
                    $yith_user_login = sanitize_user( current( explode( '@', $hyb_user_email ) ) );
                    if ( username_exists( $hyb_user_login ) ) {
                        $append     = 1;
                        $o_username = $yith_user_login;

                        while ( username_exists( $yith_user_login ) ) {
                            $yith_user_login = $o_username . $append;
                            $append ++;
                        }
                    }
                }
            }

            return $yith_user_login;

        }

        public function add_user( $username, $user_email, $user_password ) {

            $password = get_option( 'woocommerce_registration_generate_password' ) == 'yes' ? wp_generate_password() : $user_password;
            $args     = array(
                'user_login' => $username,
                'user_pass'  => $password,
                'user_email' => $user_email,
                'remember'   => false,
                'role'       => apply_filters( 'mwrq_new_user_role', 'customer' )
            );

            $customer_id = wp_insert_user( $args );

            wp_signon( $args, false );

            do_action( 'woocommerce_created_customer', $customer_id, $args, $password );

            return $customer_id;
        }

        public function update_price(  $include, $product  ) {
            $include['price'] = '<ins><span class="amount">'. wc_price($this->subtotal).'</span></ins>';
            return $include;
        }

        public function woocommerce_quantity_input_args( $args ) {

            if( isset( $this->quantity) ){
                $args['input_value'] = $this->quantity;
            }
            return $args;
        }

        public function enabled_checkout( ) {
            $show_accept_link = get_option('mwrq_show_accept_link', 'yes');

            if( $show_accept_link == 'no' ){
                return false;
            }

            $accepted_page_id = $this->get_accepted_page();
            $checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
            $cart_page_id = get_option( 'woocommerce_cart_page_id' );

            if( $accepted_page_id == $checkout_page_id || $accepted_page_id == $cart_page_id){
                return true;
            }

            return false;
        }

        public function get_accepted_page() {
            global $sitepress;
            $has_wpml         = ! empty( $sitepress ) ? true : false;
            $accepted_page_id = get_option( 'mwrq_page_accepted' );
            if ( $has_wpml ) {
                $accepted_page_id = yit_wpml_object_id( $accepted_page_id, 'page', true );
            }

            return $accepted_page_id;
        }

        public function get_redirect_page_url(){

            if(  $thank_you_page = $this->has_thank_you_page() ){
                $redirect =  $thank_you_page;
            }else{
                $redirect = $this->get_raq_page_url();
            }

            return $redirect;

        }

    }
}


function MOTIF_Rquest_Quote() {
    return MOTIF_Rquest_Quote::get_instance();
}
