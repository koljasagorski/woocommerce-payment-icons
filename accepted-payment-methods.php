<?php
/*
Plugin Name: WooCommerce  Payment Icons
Plugin URI: https://github.com/koljasagorski/woocommerce-payment-icons
Version: 0.0.1
Description: Allows you display which payment methods your online store accepts.
Author: kolja sagorski
Tested up to: 5.8
Author URI: http://sagorski.org
Text Domain: woocommerce-payment-icons
Domain Path: /languages/

	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Localisation
 */
load_plugin_textdomain( 'woocommerce-payment-icons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	 * Accepted Payment Methods class
	 **/
	if ( ! class_exists( 'WC_apm' ) ) {

		class WC_apm {

			public function __construct() {

				// Init settings
				$this->settings = array(
					array(
						'name' => __( 'Accepted Payment Methods', 'woocommerce-payment-icons' ),
						'type' => 'title',
						'desc' => sprintf( __( 'To display the selected payment methods you can use the built in widget, the %s shortcode or the %s template tag.', 'woocommerce-payment-icons' ), '<code>[woocommerce_payment_icons]</code>', '<code>&lt;?php wc_payment_icons(); ?&gt;</code>' ),
						'id' => 'wc_apm_options'
					),
					array(
						'name' 		=> __( 'Visa', 'woocommerce-payment-icons' ),
						'desc' 		=> __( 'Display the Visa logo', 'woocommerce-payment-icons' ),
						'id' 		=> 'wc_apm_visa',
						'type' 		=> 'checkbox'
					),
					array(
						'name' 		=> __( 'Mastercard', 'woocommerce-payment-icons' ),
						'desc' 		=> __( 'Display the Master Card logo', 'woocommerce-payment-icons' ),
						'id' 		=> 'wc_apm_mastercard',
						'type' 		=> 'checkbox'
					),
					array(
						'name' 		=> __( 'Cash on Apple Pay', 'woocommerce-payment-icons' ),
						'desc' 		=> __( 'Display Apple Pay symbol', 'woocommerce-payment-icons' ),
						'id' 		=> 'wc_apm_applepay',
						'type' 		=> 'checkbox'
					),
					array(
						'name' 		=> __( 'Google Pay', 'woocommerce-payment-icons' ),
						'desc' 		=> __( 'Display the Google Pay logo', 'woocommerce-payment-icons' ),
						'id' 		=> 'wc_apm_googlepay',
						'type' 		=> 'checkbox'
					),
					array(
						'name' 		=> __( 'PayPal', 'woocommerce-payment-icons' ),
						'desc' 		=> __( 'Display the PayPal logo', 'woocommerce-payment-icons' ),
						'id' 		=> 'wc_apm_paypal',
						'type' 		=> 'checkbox'
					),
					
					array( 'type' => 'sectionend', 'id' => 'wc_apm_options' ),
				);

				// Default options
				add_option( 'wc_apm_label', 			'' );
				add_option( 'wc_apm_visa', 	'no' );
				add_option( 'wc_apm_mastercard', 			'no' );
				add_option( 'wc_apm_applepay', 		'no' );
				add_option( 'wc_apm_googlepay', 			'no' );
				add_option( 'wc_apm_paypal', 				'no' );
				add_option( 'wc_apm_bitcoin', 			'no' );

				// Admin
				add_action( 'woocommerce_settings_checkout', array( $this, 'admin_settings' ), 20 );
				add_action( 'woocommerce_update_options_checkout', array( $this, 'save_admin_settings' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'setup_styles' ) );


			}

	        /*-----------------------------------------------------------------------------------*/
			/* Class Functions */
			/*-----------------------------------------------------------------------------------*/

			function admin_settings() {
				woocommerce_admin_fields( $this->settings );
			}

			function save_admin_settings() {
				woocommerce_update_options( $this->settings );
			}

			// Setup styles
			function setup_styles() {
				wp_enqueue_style( 'apm-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );
			}

		}
		$WC_apm = new WC_apm();
	}

	/**
	 * Frontend functions
	 */
	// Template tag
	if ( ! function_exists( 'wc_accepted_payment_methods' ) ) {
		function wc_accepted_payment_methods() {
			$visa 		= get_option( 'wc_apm_visa' );
			$mastercard = get_option( 'wc_apm_mastercard' );
			$applepay 	= get_option( 'wc_apm_applepay' );
			$googlepay 		= get_option( 'wc_apm_googlepay' );
			$paypal 	= get_option( 'wc_apm_paypal' );
			$bitcoin 	= get_option( 'wc_apm_bitcoin' );

			// Display
			echo '<ul class="accepted-payment-methods">';
				if ( $visa == "yes" ) { echo '<li class="dankort"><span>Dankort</span></li>'; }
				if ( $amex == "yes" ) { echo '<li class="american-express"><span>American Express</span></li>'; }
				if ( $mastercard == "yes" ) { echo '<li class="bitcoin"><span>Bitcoin</span></li>'; }
				if ( $applepay == "yes" ) { echo '<li class="cash-on-delivery"><span>Cash on Delivery</span></li>'; }
				if ( $googlepay == "yes" ) { echo '<li class="discover"><span>Discover</span></li>'; }
				if ( $paypal == "yes" ) { echo '<li class="google"><span>Google</span></li>'; }
				if ( $bitcoin == "yes" ) { echo '<li class="maestro"><span>Maestro</span></li>'; }
			echo '</ul>';
		}
	}

	// The shortcode
	add_shortcode( 'woocommerce_accepted_payment_methods', 'wc_accepted_payment_methods' );

	// The widget
	class Accepted_Payment_Methods extends WP_Widget {

		function Accepted_Payment_Methods() {
			// Instantiate the parent object
			parent::__construct( false, 'WooCommerce Payment Icons' );
		}

		function widget( $args, $instance ) {
			// Widget output
			extract( $args );

			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;
				wc_accepted_payment_methods();
			echo $after_widget;
		}
		/**
		 * Sanitize widget form values as they are saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 */
		public function form( $instance ) {
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			}
			else {
				$title = __( 'Accepted Payment Methods', 'woocommerce-payment-icons' );
			}
			?>
			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'woocommerce-payment-icons' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
			<?php _e( 'Configure which payment methods your store accepts in the', 'woocommerce-payment-icons' ); ?> <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=checkout' ); ?>"><?php _e( 'WooCommerce settings', 'woocommerce-payment-icons' ); ?></a>.
			</p>
			<?php
		}

	}

	function apm_register_widgets() {
		register_widget( 'Accepted_Payment_Methods' );
	}

	add_action( 'widgets_init', 'apm_register_widgets' );

}
