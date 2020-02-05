<?php do_action( 'mwrq_before_request_form' );
?>
<div class="motif-request-form-wrapper">
    <h3><?php esc_html_e('Send the request', 'motif-woocommerce-request-a-quote'); ?></h3>
    <form id="motif-mwrq-request-form" name="motif-mwrq-request-form"
          action="<?php echo esc_url( MOTIF_Rquest_Quote()->get_raq_page_url() ) ?>" method="post"
          enctype="multipart/form-data">


	    <?php do_action( 'mwrq_before_content_request_form' ); ?>

	    <p class="form-row ">
	    	<label for="first_name"><?php esc_html_e('First Name', 'motif-woocommerce-request-a-quote'); ?></label>
	    	<input type="text" name="first_name" class="input-text" required placeholder="">
	    </p>
	    <p class="form-row ">
	    	<label for="last_name"><?php esc_html_e('Last Name', 'motif-woocommerce-request-a-quote'); ?></label>
	    	<input type="text" name="last_name" class="input-text" placeholder="">
	    </p>
	    <p class="form-row">
	    	<label for="email"><?php esc_html_e('Email', 'motif-woocommerce-request-a-quote'); ?></label>
	    	<input type="email" name="email" class="input-text" placeholder="" required>
	    </p>
	    <p class="form-row ">
	    	<label for="message"><?php esc_html_e('Message', 'motif-woocommerce-request-a-quote'); ?></label>
	    	<textarea rows="10" cols="5" placeholder="" id="message" name="message"></textarea>
	    </p>
	    <p class="form-row form-row-wide">
            <input type="hidden" id="mwrq-mail-wpnonce" name="mwrq_mail_wpnonce" value="<?php echo wp_create_nonce( 'mwrq-request-request' ) ?>" />
            <input class="button raq-send-request" type="submit" value="Enviar Cotización">
        </p>

	    <?php do_action( 'mwrq_after_content_request_form' ); ?>
    </form>
		<p>
			* Al enviar acepta los terminos y condiciones de la cotización.
		</p>
</div>
