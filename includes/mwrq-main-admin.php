<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Motif_Request_Qoute_Admin' ) ) {

	class Motif_Request_Qoute_Admin {

		protected static $instance;

		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {

			add_action( 'init', array( $this, 'add_page' ) );

			add_action('admin_menu', array($this, 'mwrq_setting_page'));

			add_action('admin_enqueue_scripts', array($this,'mwrq_scripts_admin_settings'));

			add_action( 'wp_ajax_mwrq_admin_setting_save', array($this,'mwrq_admin_setting_save_callback') );

		}

		public function mwrq_admin_setting_save_callback() {
			$settings_data = array();
	        parse_str($_POST['data'], $settings_data);
	        
	        $settings_data['mwrw_showbtn_single'] = isset( $settings_data['mwrw_showbtn_single'] ) ? 'yes' : 'no';
	        $settings_data['mwrw_showbtn_shop'] = isset( $settings_data['mwrw_showbtn_shop'] ) ? 'yes' : 'no';
	        $settings_data['mwrw_display_sku'] = isset( $settings_data['mwrw_display_sku'] ) ? 'yes' : 'no';
	        $settings_data['mwrw_hide_subtotal'] = isset( $settings_data['mwrw_hide_subtotal'] ) ? 'yes' : 'no';
	        $settings_data['mwrw_update_list'] = isset( $settings_data['mwrw_update_list'] ) ? 'yes' : 'no';
	        $settings_data['mwrw_display_form'] = isset( $settings_data['mwrw_display_form'] ) ? 'yes' : 'no';
	        $settings_data['mwrw_return_shop'] = isset( $settings_data['mwrw_return_shop'] ) ? 'yes' : 'no';
	        $settings_data['mwrw_show_alltotal'] = isset( $settings_data['mwrw_show_alltotal'] ) ? 'yes' : 'no';
	        
	        update_option('mwrq_settings_array', $settings_data);

	        die();
		}

		public function mwrq_setting_page() {

	  		add_menu_page(
	            esc_html__('Request a Quote', 'motif-woocommerce-request-a-quote'), esc_html__('Request a Quote', 'motif-woocommerce-request-a-quote'), 'manage_options','mwrq-support',
	            array($this,'mwrq_setting_callback'),
	            mwrq_url.'assets/images/motif-menu-cion.png', 10
	        );
		}

		public function mwrq_setting_callback() {

			$settings_mwrq = get_option('mwrq_settings_array'); ?>

	        <div id="motif-tabs">

	            <div class="motif-tabs-ulli">
	                <div class="motif-logo-ui">
	                    <div class="motif_logo">
	                      <img src="<?php echo esc_url(mwrq_url).'assets/images/motif-logo.png'; ?>" />
	                    </div>
	                </div>

	                <ul class="motif_tab_ul">
	                    <li>
	                        <a href="#mwrq_button_list">
	                            <span class="dashicons dashicons-lightbulb"></span>
	                            <?php esc_html_e('Add to Quote Button', 'motif-woocommerce-request-a-quote'); ?></a>
	                    </li>
	                    <li>
	                        <a href="#mwrq_request_list">
	                            <span class="dashicons dashicons-universal-access-alt"></span>
	                            <?php esc_html_e('Request a Quote List', 'motif-woocommerce-request-a-quote'); ?></a>
	                    </li>
	                </ul>
	            </div>

	            <div class="motif-tabs-content">

	                <div class="motif-setting-header">
	                    <h2><?php esc_html_e('Motif WooCommerce Request a Quote', 'motif-woocommerce-request-a-quote'); ?></h2>
	                    <span class="motif_loading">
	                        <img src="<?php echo esc_url(mwrq_url).'assets/images/loading.gif'; ?>">
	                    </span>
	                    <span class="motif_style_wrap">
	                        <a href="javascript:void(0);" class="motif_button_setting motif_submit"><?php esc_html_e('Save all changes', 'motif-woocommerce-request-a-quote'); ?></a>
	                    </span>
	                </div>

	                <div class="motif_setting_optContainer">
	                    
	                    <form class="wordpress-ajax-form">

	                        <div class="motif_option_main">

	                            <!-- button quote list-->
	                            <div class="motif_subpage_container" id="mwrq_button_list">

	                              	<div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Show Button for Users', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Select Users to show Add to Quotes', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <select id="mwrq_user_type" name="mwrq_user_type" class="motif_select">
	                                                    <option <?php echo selected( $settings_mwrq['mwrq_user_type'], 'login' ); ?> value="login"><?php echo esc_html_e('Logged In Users', 'motif-woocommerce-request-a-quote'); ?></option>
	                                                    <option <?php echo selected( $settings_mwrq['mwrq_user_type'], 'guest' ); ?> value="guest"><?php echo esc_html_e('Guests', 'motif-woocommerce-request-a-quote'); ?></option>
	                                                    <option <?php echo selected( $settings_mwrq['mwrq_user_type'], 'all' ); ?> value="all"><?php echo esc_html_e('All', 'motif-woocommerce-request-a-quote'); ?></option>
	                                                </select>
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                              	<div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Set Display Position', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Select Request a Quote (Button Type)', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <select id="mwrq_button_type" name="mwrq_button_type" class="motif_select">
	                                                    <option <?php echo selected( $settings_mwrq['mwrq_button_type'], 'link' ); ?> value="link"><?php echo esc_html_e('Link', 'motif-woocommerce-request-a-quote'); ?></option>
	                                                    <option <?php echo selected( $settings_mwrq['mwrq_button_type'], 'button' ); ?> value="button"><?php echo esc_html_e('Button', 'motif-woocommerce-request-a-quote'); ?></option>
	                                                </select>
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Add to Quote Button Label', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Add to Quote Button Label', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <input type="text" name="mwrq_button_lable" value="<?php echo esc_attr($settings_mwrq['mwrq_button_lable']); ?>" />
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Browse List Text once product added', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Browse List Text once product added', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <input type="text" name="mwrq_browse_lable" value="<?php echo esc_attr($settings_mwrq['mwrq_browse_lable']); ?>" />
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Already in Quote Product Text', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Already in Quote Product Text', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <input type="text" name="mwrq_already_in" value="<?php echo esc_attr($settings_mwrq['mwrq_already_in']); ?>" />
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Button Text Color', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Add to Quote button text color', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <input id="mwrq_btn_text_color" type="text" name="mwrq_btn_text_color" value="<?php echo esc_attr($settings_mwrq['mwrq_btn_text_color']); ?>" />
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Button Background Color', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Add to Quote button background color', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <input id="mwrq_btn_bg_color" type="text" name="mwrq_btn_bg_color" value="<?php echo esc_attr($settings_mwrq['mwrq_btn_bg_color']); ?>" />
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Show Quote Button', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Show Quote Button On Archive/Shop Page', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <p class="mwrw_showbtn_shop">
	                                                    <input <?php echo checked( $settings_mwrq['mwrw_showbtn_shop'], 'yes'); ?> type="checkbox" id="mwrw_showbtn_shop" name="mwrw_showbtn_shop" ><label for="mwrw_showbtn_shop"></label>
	                                                </p>    
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Show Quote Button', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Show Quote Button On Single Product Page', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <p class="mwrw_showbtn_single">
	                                                    <input <?php echo checked( $settings_mwrq['mwrw_showbtn_single'], 'yes'); ?> type="checkbox" id="mwrw_showbtn_single" name="mwrw_showbtn_single" ><label for="mwrw_showbtn_single"></label>
	                                                </p>    
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                            </div>

	                            <!-- request quote list -->
	                            <div class="motif_subpage_container" id="mwrq_request_list">
	                            	
	                            	<div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Select Request a Quote List Page', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Please noted: by selecting page different from the one (plugin default) and allow customers to go and see their requests a quote list, you will need to add/insert the following shortcode [motif_request_to_quote]', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <select id="mwrq_list_page" name="mwrq_list_page" class="motif_select">
	                                                	<?php 

	                                                	$pages = get_pages(); 
  														foreach ( $pages as $page ) { ?>
	                                                    	<option <?php echo selected( $settings_mwrq['mwrq_list_page'], $page->post_name ); ?> value="<?php echo esc_attr($page->post_name); ?>">
	                                                    		<?php echo esc_attr($page->post_title).'-'.$page->post_name; ?>
	                                                    	</option>
	                                               	 	<?php } ?>
	                                                </select>
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Quote Product Sku', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('By check this product sku dispaly on Quote page', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <p class="mwrw_display_sku">
	                                                    <input <?php echo checked( $settings_mwrq['mwrw_display_sku'], 'yes'); ?> type="checkbox" id="mwrw_display_sku" name="mwrw_display_sku" ><label for="mwrw_display_sku"></label>
	                                                </p>    
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Hide SubTotal Column', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('By check this hide subtotal column in list page', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <p class="mwrw_hide_subtotal">
	                                                    <input <?php echo checked( $settings_mwrq['mwrw_hide_subtotal'], 'yes'); ?> type="checkbox" id="mwrw_hide_subtotal" name="mwrw_hide_subtotal" ><label for="mwrw_hide_subtotal"></label>
	                                                </p>    
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Show return to shop button', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('By check this show return to shop button on quote list', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <p class="mwrw_return_shop">
	                                                    <input <?php echo checked( $settings_mwrq['mwrw_return_shop'], 'yes'); ?> type="checkbox" id="mwrw_return_shop" name="mwrw_return_shop" ><label for="mwrw_return_shop"></label>
	                                                </p>    
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Return to shop Button Label', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Return to shop button lable in quote list', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <input type="text" name="mwrq_returnshop" value="<?php echo esc_attr($settings_mwrq['mwrq_returnshop']); ?>" />
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Return to shop button link', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Set return to shop button link in quote page', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <input type="text" name="mwrq_returnshop_link" value="<?php echo esc_attr($settings_mwrq['mwrq_returnshop_link']); ?>" />
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Quote Update List', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('By check this update list button show on quote page', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <p class="mwrw_update_list">
	                                                    <input <?php echo checked( $settings_mwrq['mwrw_update_list'], 'yes'); ?> type="checkbox" id="mwrw_update_list" name="mwrw_update_list" ><label for="mwrw_update_list"></label>
	                                                </p>    
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Update Button Label', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Set update button label', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <input type="text" name="mwrq_update_lable" value="<?php echo esc_attr($settings_mwrq['mwrq_update_lable']); ?>" />
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Quote Send Email Form', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('By check this email send form is display under Quote list', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <p class="mwrw_display_form">
	                                                    <input <?php echo checked( $settings_mwrq['mwrw_display_form'], 'yes'); ?> type="checkbox" id="mwrw_display_form" name="mwrw_display_form" ><label for="mwrw_display_form"></label>
	                                                </p>    
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                                <div class="motif_setting_row">
	                                    <h4><?php esc_html_e('Show All Total', 'motif-woocommerce-request-a-quote'); ?></h4>
	                                    <div class="motif_setting_control">
	                                        <div class="motif_control_description">
	                                            <p>
	                                                <?php esc_html_e('Show all total row in quote page list', 'motif-woocommerce-request-a-quote'); ?>
	                                            </p>
	                                        </div>
	                                        <div class="motif_control_field">
	                                            <span class="moif_style_wrap">
	                                                <p class="mwrw_show_alltotal">
	                                                    <input <?php echo checked( $settings_mwrq['mwrw_show_alltotal'], 'yes'); ?> type="checkbox" id="mwrw_show_alltotal" name="mwrw_show_alltotal" ><label for="mwrw_show_alltotal"></label>
	                                                </p>    
	                                            </span>
	                                        </div>
	                                        <div class="clear"></div>
	                                    </div>
	                                </div>

	                            </div>

	                        </div>

	                    </form>

	                </div>

	            </div>

	        </div>

		<?php }

		public function add_page() {
			global $wpdb;

			$option_value = get_option( 'mwrq_page_id' );

			if ( $option_value > 0 && get_post( $option_value ) ) {
				return;
			}

			$page_found = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = 'request-to-quote-motif' LIMIT 1;" );
			if ( $page_found ) :
				if ( ! $option_value ) {
					update_option( 'mwrq_page_id', $page_found );
				}

				return;
			endif;

			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => esc_sql( _x( 'request-to-quote-motif', 'page_slug', 'motif-woocommerce-request-a-quote' ) ),
				'post_title'     => __( 'Request a Quote', 'motif-woocommerce-request-a-quote' ),
				'post_content'   => '[motif_request_to_quote]',
				'post_parent'    => 0,
				'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $page_data );

			update_option( 'mwrq_page_id', $page_id );
		}

		public function mwrq_scripts_admin_settings() {

			wp_enqueue_script('jquery');

			wp_enqueue_script('jquery-ui-tabs');

			wp_enqueue_script('wp-color-picker');

        	wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_style( 'mwrq-backend', plugins_url( '/../assets/css/backend.css', __FILE__ ), false );

			wp_enqueue_script( 'mwrq-backend', plugins_url( '/../assets/js/setting.js', __FILE__ ), false );

			wp_localize_script( 'mwrq-backend', 'mwrq_data_vars', array(
	                'mwrq_nonce' => wp_create_nonce('mwrq_nonce'),
	                'ajax_url' => admin_url('admin-ajax.php')
	            )
	        );
		}

	}
}

function Motif_Request_Qoute_Admin() {
	return Motif_Request_Qoute_Admin::get_instance();
}