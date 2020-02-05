<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$tax_display_list = apply_filters( 'mwrq_tax_display_list', get_option( 'woocommerce_tax_display_cart' ) );
?>

<?php do_action( 'mwrq_before_raq_list_widget' ); ?>
	<div class="motif-mwrq-list-wrapper">
		<?php ?>

		<?php
		if ( count( $raq_content ) == 0 ):
			?>
			<p><?php esc_html_e( 'No products in the list', 'motif-woocommerce-request-a-quote' ) ?></p>
		<?php else: ?>
			<ul class="motif-mwrq-list">
				<?php foreach ( $raq_content as $key => $raq ):
					$_product = wc_get_product( isset( $raq['variation_id'] ) ? $raq['variation_id'] : $raq['product_id'] );
					if( ! $_product ){
						continue;
					}
					$thumbnail = ( $show_thumbnail ) ? $_product->get_image() : '';
					$product_name = $_product->get_title();
					?>

					<li class="motif-mwrq-list-item">
						<?php
						echo apply_filters( 'motif_mwrq_item_remove_link', sprintf( '<a href="#"  data-remove-item="%s" data-wp_nonce="%s"  data-product_id="%d" class="motif-mwrq-item-remove remove" title="%s">&times;</a>', $key, wp_create_nonce( 'remove-request-quote-' . $_product->get_id() ), $_product->get_id(), esc_html__( 'Remove this item', 'motif-woocommerce-request-a-quote' ) ), $key );
						?>

						<?php if ( ! $_product->is_visible() ) : ?>
							<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $_product->get_permalink() ); ?>">
								<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; ?>
							</a>
						<?php endif; ?>
						<?php if ( isset( $raq['variations'] ) && $show_variations ): ?>
							<small><?php motif_mwrq_get_product_meta( $raq ); ?></small>
						<?php endif ?>

						<?php if ( $show_quantity || $show_price ): ?>
							<span class="quantity">
                         <?php
						 echo ( $show_quantity ) ? $raq['quantity'] : '';
						 if ( $show_price ) {
							 $x = ( $show_quantity ) ? ' x ' : '';
							 do_action('mwrq_quote_adjust_price', $raq, $_product);
							 $price = ( "incl" == $tax_display_list ) ? wc_get_price_including_tax( $_product, array( 'qty' => $raq['quantity'] ) ) : wc_get_price_excluding_tax( $_product, array( 'qty' => $raq['quantity'] ) );
							 if ( $price ) {
								 $price             = apply_filters( 'motif_mwrq_product_price_html', WC()->cart->get_product_subtotal( $_product, $raq['quantity'] ), $_product, $raq );
							 } else {
								 $price = wc_price( 0 );
							 }

							 $x = ( $show_quantity ) ? ' x ' : '';
							 echo apply_filters( 'motif_mwrq_hide_price_template', $x . $price, $_product->get_id(), $raq );
						 } ?>
                          </span>
						<?php endif; ?>
					</li>

				<?php endforeach ?>


			</ul>
			<a href="<?php echo MOTIF_Rquest_Quote()->get_raq_page_url() ?>" class="button"><?php echo apply_filters( 'motif_mwrq_quote_list_button_label', esc_html__( 'View list', 'motif-woocommerce-request-a-quote' ) ) ?></a>
		<?php endif ?>
	</div>


<?php do_action( 'mwrq_after_raq_list_widget' ); ?>