<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'Motif_Request_Default_Form' ) ) {

	class Motif_Request_Default_Form {

		protected static $instance;

		protected $attachments = array();

		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {

			add_action( 'wc_ajax_mwrq_submit_default_form', array( $this, 'mwrq_submit_default_form_callback' ) );	

			add_action('send_raq_mail', array($this, 'send_email_request'), 1);
		}

		public function send_email_request($filled_form_fields) {
			
			ob_start();
		    require_once(mwrq_path.'email-request-quote.php');
		    $message = ob_get_contents();
		    ob_end_clean();


			$to = get_option('admin_email');
			$subject = esc_html__('Request a Quote', 'motif-woocommerce-request-a-quote');
			$body = $message;
			$headers = array('Content-Type: text/html; charset=UTF-8');
			 
			wp_mail( $to, $subject, $body, $headers );
		}

		public function get_errors( $errors , $html = true ) {
			return implode( ( $html ? '<br />' : ', ' ), $errors );
		}

		public function mwrq_submit_default_form_callback() {

			if ( ! isset( $_REQUEST['mwrq_mail_wpnonce'] ) ) {
				return;
			}

			$posted = apply_filters( 'mwrq_default_form_posted_request', $_REQUEST );
			$errors = array();

			if ( MOTIF_Rquest_Quote()->is_empty() ) {
				$errors[] = mwrq_get_list_empty_message();
			}

			$errors = apply_filters( 'mwrq_request_validate_fields', $errors, $posted );

			if ( $errors ) {

					$results = array(
						'result'   => 'failure',
						'messages' => $this->get_errors( $errors ),
					);
			
			} else {
					
					try{

						$filled_form_fields['raq_content'] = MOTIF_Rquest_Quote()->get_raq_return();

						$username = '';

						if ( isset( $posted['first_name'] ) ) {
							$username = $posted['first_name'];
						}

						if ( isset( $posted['last_name'] ) ) {
							$username .= ' ' . $posted['last_name'];
						}

						$filled_form_fields['user_name'] = $username ? trim( $username ) : '';
						$filled_form_fields['user_email']   = $posted['email'];
						$filled_form_fields['user_message'] = isset( $posted['message']) ? $posted['message'] : '';


						do_action( 'send_raq_mail', $filled_form_fields );

						MOTIF_Rquest_Quote()->clear_raq_list();

						$results = array(
							'result'   => 'success',
							'redirect' => MOTIF_Rquest_Quote()->get_raq_page_url(),
						);

				} catch( Exception $e  ) {
					
					$results = array(
						'result'   => 'failure',
						'messages' => $e->getMessage(),
					);
				
				}
	        }
			
			wp_send_json( $results );
			exit();
		
		}
		
	}

	function Motif_Request_Default_Form() {
		return Motif_Request_Default_Form::get_instance();
	}

}