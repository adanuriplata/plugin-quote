<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Motif_RequestToQuote_Shortcodes {

    public function __construct() {

        add_shortcode( 'motif_request_to_quote', array( $this, 'mwrq_request_quote_callback' ) );
    }

    public function mwrq_request_quote_callback($atts, $content = null) {
    	
        $settings_mwrq = get_option('mwrq_settings_array');
        
    	$raq_content  = MOTIF_Rquest_Quote()->get_raq_return();

        $args = shortcode_atts( array(
            'raq_content'   => $raq_content,
            'template_part' => 'viewa',
            'show_form'     => $settings_mwrq['mwrw_display_form']
        ), $atts );

	    $args['args'] = apply_filters( 'mwrq_request_quote_page_args', $args, $raq_content );
        ob_start();

        wc_get_template('request-quote.php', $args, '', mwrq_path.'/' );

        return ob_get_clean();
    }

} new Motif_RequestToQuote_Shortcodes();