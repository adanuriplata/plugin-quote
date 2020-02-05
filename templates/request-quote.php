<?php

global $wpdb, $woocommerce; ?>

<div class="woocommerce mwrq-wrapper">
    <div id="motif-mwrq-message">
    	<?php do_action( 'mwrq_raq_message' ) ?>	
    </div>
	<?php if ( isset( $_GET['raq_nonce'] ) )
		return ?>
	<?php wc_get_template( 'request-quote-' . $template_part . '.php', $args, '', mwrq_path . '/' ); ?>

	<?php if ( $args['show_form'] == 'yes' && count( $raq_content ) != 0 ): ?>
		<?php wc_get_template( 'request-quote-form.php', $args, '', mwrq_path . '/' ); ?>
	<?php endif ?>
</div>