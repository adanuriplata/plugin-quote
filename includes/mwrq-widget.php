<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Motif_mwrq_widget_quote' ) ) {

	class Motif_mwrq_widget_quote extends WP_Widget {

		function __construct() {

			$widget_cssclass    = 'woocommerce widget_mwrq_list_quote';
			$widget_description = esc_html__( 'Show products added to your list', 'motif-woocommerce-request-a-quote' );
			$widget_idbase      = 'motif_mwrq_request_quote_list';
			$widget_name        = esc_html__( 'Motif WooCommerce Request a Quote List', 'motif-woocommerce-request-a-quote' );

			$widget_ops = array( 'classname' => $widget_cssclass, 'description' => $widget_description );

			parent::__construct( $widget_idbase, $widget_name, $widget_ops );

		}

		function widget( $args, $instance ) {
			extract( $args );

			$this->istance = $instance;
			$title         = isset( $instance['title'] ) ? $instance['title'] : '';
			$title         = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			if ( ! apply_filters( 'motif_mwrq_before_print_widget', true ) ) {
				return;
			}
			$instance['widget_type'] = 'large';

			echo $before_widget;

			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			$raq_content = MOTIF_Rquest_Quote()->get_raq_return();
			$args        = array(
				'raq_content'     => $raq_content,
				'template_part'   => 'view',
				'show_thumbnail'  => isset( $instance['show_thumbnail'] ) ? $instance['show_thumbnail'] : 0,
				'show_price'      => isset( $instance['show_price'] ) ? $instance['show_price'] : 0,
				'show_quantity'   => isset( $instance['show_quantity'] ) ? $instance['show_quantity'] : 0,
				'show_variations' => isset( $instance['show_variations'] ) ? $instance['show_variations'] : 0,
				'widget_type'     => $instance['widget_type'],
			);
			echo '<div class="motif-mwrq-list-widget-wrapper" data-instance="' . http_build_query( $instance ) . '">';
			wc_get_template( 'widget/quote-list.php', $args, '', mwrq_path . '/' );
			echo '</div>';
			echo $after_widget;
		}

		function update( $new_instance, $old_instance ) {
			$instance['title']           = strip_tags( stripslashes( $new_instance['title'] ) );
			$instance['show_thumbnail']  = isset( $new_instance['show_thumbnail'] ) ? 1 : 0;
			$instance['show_price']      = isset( $new_instance['show_price'] ) ? 1 : 0;
			$instance['show_quantity']   = isset( $new_instance['show_quantity'] ) ? 1 : 0;
			$instance['show_variations'] = isset( $new_instance['show_variations'] ) ? 1 : 0;

			$this->instance = $instance;

			return $instance;
		}

		function form( $instance ) {
			$defaults = array(
				'title'           => esc_html__( 'Quote List', 'motif-woocommerce-request-a-quote' ),
				'show_thumbnail'  => 1,
				'show_price'      => 1,
				'show_quantity'   => 1,
				'show_variations' => 1,
			);

			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
 
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'motif-woocommerce-request-a-quote' ) ?></label>
                <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                       value="<?php if ( isset ( $instance['title'] ) ) {
					       echo esc_attr( $instance['title'] );
				       } ?>"/>
            </p>

            <p>
                <label>
                    <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>"
                           name="<?php echo esc_attr( $this->get_field_name( 'show_thumbnail' ) ); ?>"
                           value="1" <?php checked( $instance['show_thumbnail'], 1 ) ?> />
					<?php esc_html_e( 'Show Thumbnail', 'motif-woocommerce-request-a-quote' ); ?>
                </label>
            </p>

            <p>
                <label>
                    <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_price' ) ); ?>"
                           name="<?php echo esc_attr( $this->get_field_name( 'show_price' ) ); ?>"
                           value="1" <?php checked( $instance['show_price'], 1 ) ?> />
					<?php esc_html_e( 'Show Price', 'motif-woocommerce-request-a-quote' ); ?>
                </label>
            </p>

            <p>
                <label>
                    <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_quantity' ) ); ?>"
                           name="<?php echo esc_attr( $this->get_field_name( 'show_quantity' ) ); ?>"
                           value="1" <?php checked( $instance['show_quantity'], 1 ) ?> />
					<?php esc_html_e( 'Show Quantity', 'motif-woocommerce-request-a-quote' ); ?>
                </label>
            </p>

            <p>
                <label>
                    <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_variations' ) ); ?>"
                           name="<?php echo esc_attr( $this->get_field_name( 'show_variations' ) ); ?>"
                           value="1" <?php checked( $instance['show_variations'], 1 ) ?> />
					<?php esc_html_e( 'Show Variations', 'motif-woocommerce-request-a-quote' ); ?>
                </label>
            </p>

			<?php
		}
	}
}