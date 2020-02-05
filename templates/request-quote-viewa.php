<?php

$colspan = '5';
$tax_display_list = apply_filters( 'mwrq_tax_display_list', get_option( 'woocommerce_tax_display_cart' ) );
$total_tax = 0;

$settings_mwrq = get_option('mwrq_settings_array');
?>

<form id="motif-mwrq-form" name="motif-mwrq-form" action="<?php echo esc_url( MOTIF_Rquest_Quote()->get_raq_page_url() ) ?>" method="post">
	<table class="shop_table cart shop_table_responsive" id="motif-mwrq-list" cellspacing="0">
		<thead>
			<tr>
				<th class="mwrq-product-remove">&nbsp;</th>
                <?php if ( apply_filters('mwrq_item_thumbnail', true )) : ?>
				<th class="mwrq-product-thumbnail">&nbsp;</th>
                <?php endif; ?>
				<th class="mwrq-product-name">
					<?php esc_html_e( 'Product', 'motif-woocommerce-request-a-quote' ) ?>
				</th>
				<th class="mwrq-product-quantity"><?php esc_html_e( 'Quantity', 'motif-woocommerce-request-a-quote' ) ?>
				</th>
				<?php if ( $settings_mwrq['mwrw_hide_subtotal'] != 'yes'): ?>
					<th class="mwrq-product-subtotal"><?php esc_html_e( 'Total', 'motif-woocommerce-request-a-quote' ); ?></th>
				<?php endif ?>
			</tr>
		</thead>
		<tbody>
			<?php

			$total        = 0;
			$total_exc    = 0;
			$total_inc    = 0;
			foreach ( $raq_content as $key => $raq ):
				$product_id = ( isset( $raq['variation_id'] ) && $raq['variation_id'] != '' ) ? $raq['variation_id'] : $raq['product_id'];
				$_product = wc_get_product( $product_id );

				if ( ! $_product ) {
					continue;
				}

				$show_price = true;

				do_action( 'motif_before_request_quote_view_item', $raq_content, $key );
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'motif_mwrq_item_class', 'cart_item', $raq_content, $key ) ); ?>" <?php echo esc_attr( apply_filters( 'motif_mwrq_item_attributes', '', $raq_content, $key ) ); ?>>

					<!-- remove product -->
					<td class="product-remove">
						<?php
						echo apply_filters( 'motif_mwwrq_item_remove_link', sprintf( '<a href="#"  data-remove-item="%s" data-wp_nonce="%s"  data-product_id="%d" class="motif-mwrq-item-remove remove" title="%s">&times;</a>', $key, wp_create_nonce( 'remove-request-quote-' . $product_id ), $product_id, __( 'Remove this item', 'motif-woocommerce-request-a-quote' ) ), $key );
						?>

					</td>

					<!-- thumbnail -->
					<?php if ( apply_filters('mwrq_item_thumbnail', true )) : ?>
					<td class="product-thumbnail">
						<?php $thumbnail = $_product->get_image();

						if ( ! $_product->is_visible() ) {
							echo esc_attr($thumbnail);
						} else {
							printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
						}
						?>
					</td>
                   <?php endif; ?>

                   <!-- product name -->
                   <td class="product-name" data-title="<?php esc_html_e( 'Product', 'motif-woocommerce-request-a-quote' ); ?>">
						<?php
						$title = $_product->get_title();

						if ( $_product->get_sku() != '' && $settings_mwrq['mwrw_display_sku'] == 'yes' ) {
							$title .= ' ' . apply_filters( 'mwrq_sku_label', __( ' SKU--', 'motif-woocommerce-request-a-quote' ) ) . $_product->get_sku();
						}
						?>
                        <a href="<?php echo esc_attr($_product->get_permalink()); ?>"><?php echo apply_filters( 'mwrq_quote_item_name', $title, $raq, $key ) ?></a>
                        <?php
						// Meta data

						$item_data = array();

						// Variation data

						if ( ! empty( $raq['variation_id'] ) && is_array( $raq['variations'] ) ) {

							foreach ( $raq['variations'] as $name => $value ) {
								$label = '';

								if ( '' === $value ) {
									continue;
								}

								$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

								// If this is a term slug, get the term's nice name
								if ( taxonomy_exists( $taxonomy ) ) {
									$term = get_term_by( 'slug', $value, $taxonomy );
									if ( ! is_wp_error( $term ) && $term && $term->name ) {
										$value = $term->name;
									}
									$label = wc_attribute_label( $taxonomy );

								} else {

									if ( strpos( $name, 'attribute_' ) !== false ) {
										$custom_att = str_replace( 'attribute_', '', $name );

										if ( $custom_att != '' ) {
											$label = wc_attribute_label( $custom_att );
										} else {
											$label = $name;
										}
									}

								}

								$item_data[] = array(
									'key'   => $label,
									'value' => $value
								);
							}
						}

						$item_data = apply_filters( 'mwrq_request_quote_view_item_data', $item_data, $raq, $_product, $show_price );

						// Output flat or in list format
						if ( sizeof( $item_data ) > 0 ) {
							foreach ( $item_data as $data ) {
								echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "<br>";
							}
						} ?>

					</td>

					<td class="product-quantity" data-title="<?php esc_html_e( 'Cantidad', '' ); ?>">

						<?php if ( $_product->is_sold_individually() ) {

							$product_quantity = sprintf( '1 <input type="hidden" name="raq[%s][qty]" value="1" />', $key );

						} else {

							$product_quantity = woocommerce_quantity_input(
								array(
                                    'input_name'  => "raq[{$key}][qty]",
                                    'input_value' => apply_filters( 'mwrq_quantity_input_value', $raq['quantity'] ),
                                    'max_value'   => apply_filters( 'mwrq_quantity_max_value', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product ),
                                    'min_value'   => apply_filters( 'mwrq_quantity_min_value', 0, $_product ),
                                    'step'        => apply_filters( 'mwrq_quantity_step_value', 1, $_product )
                                ), $_product, false );

						}
						echo $product_quantity; ?>
					</td>

					<?php if ( $settings_mwrq['mwrw_hide_subtotal'] != 'yes'): ?>
					<td class="product-subtotal" data-title="<?php esc_html_e( 'Price', 'motif-woocommerce-request-a-quote' ); ?>">
						<?php
							do_action('mwrq_quote_adjust_price', $raq, $_product);
							$price = ( "incl" == $tax_display_list ) ? wc_get_price_including_tax( $_product, array( 'qty' => $raq['quantity'] ) ) : wc_get_price_excluding_tax( $_product, array( 'qty' => $raq['quantity'] ) );

							if ( $price ) {
								$price_with_tax    = wc_get_price_including_tax( $_product, array( 'qty' => $raq['quantity'] ) );
								$price_without_tax = wc_get_price_excluding_tax( $_product, array( 'qty' => $raq['quantity'] ) );
								$total             += floatval( $price );
								$total_tax         += floatval( $price_with_tax - $price_without_tax );
								$price             = apply_filters( 'motif_mwrq_product_price_html', WC()->cart->get_product_subtotal( $_product, $raq['quantity'] ), $_product, $raq );
							} else {
								$price = wc_price( 0 );
							}

							echo apply_filters( 'motif_mwrq_hide_price_template', $price, $product_id, $raq );
							?>
						</td>
					<?php endif ?>
				</tr>
				<?php do_action( 'mwrq_after_request_quote_view_item', $raq_content, $key ); ?>

			<?php endforeach ?>

				<?php if ( $settings_mwrq['mwrw_show_alltotal'] == 'yes' ): ?>
					<tr>
						<th colspan="3" >
						</th>
						<th>
							<?php esc_html_e( 'Total:', 'motif-woocommerce-request-a-quote' ) ?>
						</th>
						<td class="raq-totals" data-title="<?php esc_html_e( 'Total', 'motif-woocommerce-request-a-quote' ); ?>">
							<?php
	                            echo wc_price($total);
	                            if ( $total_tax > 0 && "incl" == $tax_display_list && apply_filters( 'mwrq_show_taxes_quote_list', false ) ){
		                            echo '<br><small class="includes_tax">' . sprintf( __( '(includes %s %s)', 'woocommerce' ), wc_price( $total_tax ), WC()->countries->tax_or_vat() ) . '</small>';
	                            }
							?>
						</td>
					</tr>
				<?php endif ?>



				<tr>
					<td colspan="<?php echo esc_attr($colspan); ?>" class="actions">
						<?php if ( $settings_mwrq['mwrw_return_shop'] == 'yes' ):
							$shop_url =  $settings_mwrq['mwrq_returnshop_link'];
							$label_return_to_shop = $settings_mwrq['mwrq_returnshop'];
							?>
							<a class="button wc-backward" href="<?php echo esc_attr($shop_url); ?>"><?php echo esc_attr($label_return_to_shop); ?></a>
						<?php endif ?>
						<?php

	                    if ( $settings_mwrq['mwrw_update_list'] == 'yes' ): ?>
							<input type="submit" class="button" name="update_raq" value="<?php echo esc_attr($settings_mwrq['mwrq_update_lable']); ?>">
						<?php endif ?>
						<input type="hidden" id="update_raq_wpnonce" name="update_raq_wpnonce" value="<?php echo wp_create_nonce( 'update-request-quote-quantity' ) ?>">
					</td>
				</tr>

			</tbody>
	</table>
</form>