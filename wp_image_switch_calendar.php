<?php

/*
Plugin Name: Image Switch Calendar
Plugin URI: 
Description: A calendar to switch image by day. you have to use [tamil_calender] this shortcode.
Author: Shahadat Hossain
Version: 0.0.1
Author URI: https://mshossain.me/
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'WP_ISC', '0.0.1' );
register_activation_hook( __FILE__, array( 'WP_Image_Switch_Calendar', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'WP_Image_Switch_Calendar', 'plugin_deactivation' ) );

add_action( 'plugins_loaded', array( 'WP_Image_Switch_Calendar', 'init' ) );


class WP_Image_Switch_Calendar {

	private static $initiated = false;

	function __construct() {
		$this->init_hooks();
		return $this;
	}


	public static function init() {

		if (!(self::$initiated instanceof WP_Image_Switch_Calendar )) {
			self::$initiated = new self();
		}

		return self::$initiated;
	}


	/**
	 * Initializes WordPress hooks
	 */
	private function init_hooks() {
		add_shortcode( 'wp_image_switch_calendar', array($this, 'wp_image_switch_calendar_shortcode') );
		add_action( 'wp_ajax_nopriv_get_image', array($this, 'isc_get_image')  );
		add_action( 'wp_ajax_get_image', array($this, 'isc_get_image') );
		// add_action( 'edit_attachment', array ( $this, 'save_attachment_mb_data'), 10, 1 );
		// add_filter( 'attachment_fields_to_edit', array( $this, 'applyFilter' ), 11, 2 );
		add_action('admin_enqueue_scripts', array($this, 'wp_isc_admin_enqueue_date_picker'));
		add_action( 'admin_init', array($this, 'add_wp_isc_attachment_meta' ));
		add_action('edit_attachment', array($this, 'save_wp_isc_attachment_meta') );
	}

	public function wp_image_switch_calendar_shortcode( $atts ) {
		$this->wp_isc_include_script ();
		$view = plugin_dir_path( __FILE__ ).'calendar_view.php';

		ob_start();
		if (file_exists($view))
			include($view);

		return ob_get_clean();
	}

	/**
	 * Load all require style and css;
	 * 
	 */
	public function wp_isc_include_script () {
		$file ='assest/tc.js';
		$css = 'assest/tc.css';

		wp_register_script( 
			'fancyjs',
			plugin_dir_url( __FILE__ ) .'assest/fancybox/dist/jquery.fancybox.min.js',
			[ 'jquery', 'jquery-ui-datepicker', 'jquery-touch-punch' ],
			time(),
			false
		);

		wp_enqueue_script( 
			'wp_isc_js',
			plugin_dir_url( __FILE__ ) . $file,
			[ 'jquery', 'jquery-ui-datepicker', 'jquery-touch-punch', 'fancyjs' ],
			filemtime( plugin_dir_path( __FILE__ ) . $file ),
			true
		);
		wp_localize_script( 'wp_isc_js', 'WP_ISC', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		));

		wp_enqueue_style(
			'fancy_css',
			plugin_dir_url( __FILE__ ) .'assest/fancybox/dist/jquery.fancybox.min.css',
			[],
			'0.0.1'
		);

		wp_enqueue_style(
			'wp_isc_css',
			plugin_dir_url( __FILE__ ) . $css,
			['fancy_css'],
			filemtime(plugin_dir_path( __FILE__ ).$css)
		);
		
		wp_enqueue_style(
			'tc-jquery-ui',
        	'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css'
        );
	}

	public function wp_isc_admin_enqueue_date_picker () {
		$file ='assest/tc-admin.js';
		wp_enqueue_script( 
			'wp_isc_admin',
			plugin_dir_url( __FILE__ ) . $file,
			[ 'jquery', 'jquery-ui-datepicker', 'jquery-touch-punch', 'media-editor' ],
			filemtime(plugin_dir_path( __FILE__ ).$file),
			true
		);
		$wp_scripts = wp_scripts();
		wp_enqueue_style(
			'tc-jquery-ui',
        	'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css'
        );
	}

// public function applyFilter( $form_fields, $post = null ) {
	
// 	$id = "attachments-{$post->ID}-tc-image";
	

// 	$form_fields['tc-image'] = array(
//         'label' => 'Image Date',
//         'class' => array('tc-datepicker'),
//         'input' => 'datepicker',
//         'id'	=> 'datepicker',
//         'value' => get_post_meta( $post->ID, 'tc-image-date', true ),
//         'helps' => 'This image will show with Tamil calendar',
//     );
// 	return $form_fields;
// }



// public function save_attachment_mb_data( $post_id ) {
//     error_log(print_r($post_id, true));
// }

	public function isc_get_image () {
		$get = $_GET;
		$args = [
			'post_type' => 'attachment',
			'meta_key' => 'tc-image-date',
			'meta_value' => $get['date'],
		];
		$post = get_posts( $args );
		wp_send_json_success($post);
	}

	public function add_wp_isc_attachment_meta(){
   		add_meta_box( 'custom-attachment-meta-box', 
             'Tamil Calndar Date', 
             array($this, 'wp_isc_attachment_meta_box_callback'),
             'attachment',
             'normal',
             'low');
	}



	public function wp_isc_attachment_meta_box_callback () {
		     global $post; 
     		$value = get_post_meta($post->ID, 'tc-image-date', 1);
		?>
		      <div class="tc-datepicker">
		      	
		      	<p>Date: <input type="text" id="tc-datepicker" name="tc-image-date" value="<?php echo $value; ?>"></p>
		      </div>

		    
		<?php
	}

	function save_wp_isc_attachment_meta(){
	     global $post; 
	     $tc = sanitize_text_field($_POST['tc-image-date']);

	     if( !empty( $tc ) ){
	           update_post_meta( $post->ID, 'tc-image-date', $tc );
	     }
	}





	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {

	}


		/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {

	}


}


