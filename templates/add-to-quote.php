<?php

$data_variations = ( isset( $variations ) && !empty( $variations ) ) ? ' data-variation="'.$variations.'" ' : ''; ?>

<div class="motif-mwrq-quote motif-addto-quote-<?php echo esc_attr($product_id); ?>" <?php echo esc_attr($data_variations); ?>>
    
    <?php if ( ! is_product() && apply_filters('motif_mwrq_quantity_loop', false) ) woocommerce_quantity_input(); ?>
    
    <div class="motif-mwrq-add-button <?php echo ( $exists ) ? 'hide': 'show' ?>" style="display:<?php echo ( $exists ) ? 'none': 'block' ?>" >
        <?php wc_get_template( 'add-to-quote-' . $template_part . '.php', $args, '', mwrq_path.'/' );  ?>
    </div>
    
    <div class="motif_mwrq_add_item_response-<?php echo esc_attr($product_id);  ?> motif_mwrq_add_item_response_message <?php echo ( !$exists ) ? 'hide': 'show' ?>" style="display:<?php echo ( !$exists ) ? 'none': 'block' ?>">
    	<?php echo esc_attr($already_in); ?>	
    </div>
    
    <div class="motif_mwrq_add_item_browse-list-<?php echo esc_attr($product_id); ?> motif_mwrq_add_item_browse_message  <?php echo ( !$exists ) ? 'hide': 'show' ?>" style="display:<?php echo ( !$exists ) ? 'none': 'block' ?>">
    	<a href="<?php echo  esc_url($rqa_url); ?>">
    		<?php echo esc_attr($label_browse); ?>		
    	</a>
    </div>
    
    <div class="motif_mwrq_add_item_product-response-<?php echo esc_attr($product_id); ?> motif_mwrq_add_item_product_message hide" style="display:'none'"></div>

</div>

<div class="clear"></div>
