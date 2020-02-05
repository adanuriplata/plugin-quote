<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mwrq_get_list_empty_message' ) ) {
	function mwrq_get_list_empty_message() {
		$label_return_to_shop    = apply_filters( 'motif_mwrq_return_to_shop_label', get_option( 'mwrq_return_to_shop_label' ) );
		$empty_list_message_text = apply_filters( 'mwrq_get_list_empty_message_text', __( 'Your list is empty, add products to the list to send a request', 'motif-woocommerce-request-a-quote' ) );
		$empty_list_message      = sprintf( '<p class="mwrq_list_empty_message">%s<p>', $empty_list_message_text );
		$shop_url                = apply_filters( 'motif_mwrq_return_to_shop_url', get_option( 'mwrq_return_to_shop_url' ) );
		$empty_list_message      .= sprintf( '<p class="return-to-shop"><a class="button wc-backward" href="%s">%s</a><p>', $shop_url, $label_return_to_shop );

		return apply_filters( 'mwrq_get_list_empty_message', $empty_list_message );
	}
}