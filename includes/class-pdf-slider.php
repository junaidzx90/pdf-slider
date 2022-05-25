<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Pdf_Slider
 * @subpackage Pdf_Slider/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Pdf_Slider
 * @subpackage Pdf_Slider/includes
 * @author     Junayed <admin@easeare.com>
 */
class Pdf_Slider {

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $plugin_name;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$plugin_name = 'pdf-slider';
		if ( defined( 'PDF_SLIDER_VERSION' ) ) {
			$this->version = PDF_SLIDER_VERSION;
		} else {
			$this->version = '2.0';
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'pdf-slider', PDFSLIDER_URL . 'scripts/css/pdf-slider-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_register_script( 'pdfjs', PDFSLIDER_URL.'scripts/js/pdf.js', array(), $this->version, false );
		wp_register_script( 'pdf.warker', PDFSLIDER_URL.'scripts/js/pdf.warker.js', array('pdfjs'), $this->version, false );
		wp_register_script( 'pdfslider', PDFSLIDER_URL.'scripts/js/slider.js', array('pdf.warker'), $this->version, true );
	}

	function initial_callback(){
		/// wp_enqueue_media();
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'pdfjs' );
		wp_enqueue_script( 'pdf.warker' );
		wp_enqueue_script( 'pdf-slider', PDFSLIDER_URL . 'build/index.js', array( 'wp-blocks','pdfjs','pdf.warker','jquery' ) );
		register_block_type( 'pdf-slider/pdf-block', array(
			'editor_script' 	=> 'pdf-slider',
			'render_callback'	=> [$this, 'block_render_callback']
		) );
	}

	function frontScripts(){
		?>
		<style>
			@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css");
			:root{
				--txtc: <?php echo (get_option('slider_text_color')? get_option('slider_text_color') : '#2F4373') ?>;
			}
			div.pdfslider {
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-webkit-box-pack: center;
				    -ms-flex-pack: center;
				        justify-content: center;
				position: relative;
				-webkit-box-orient: vertical;
				-webkit-box-direction: normal;
				    -ms-flex-direction: column;
				        flex-direction: column;
				height: 100% !important;
				border: 1px solid #F0F5F9;
				border-radius: 5px;
				margin: 10px 0px;
				margin: 0 auto;
				background-color: <?php echo (get_option('slider_bg_color')? get_option('slider_bg_color') : '#789EC2') ?>;
				margin-bottom: 10px;
				display: none;
				overflow: hidden;
			}
			div.pdfslider.fullsize{
				border-radius: 0px !important;
				border: 0px !important;
			}
			.pdfsliderContents{
				width: 100%;
				height: 75%;
				margin: auto;
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-webkit-box-align: center;
				    -ms-flex-align: center;
				        align-items: center;
				-webkit-box-pack: center;
				    -ms-flex-pack: center;
				        justify-content: center;
			}

			.the-canvas{
				height: 100%;
			}
			.slideropt {
				width: 100%;
				text-align: center;
				background: <?php echo (get_option('slider_bottom_bg_color')? get_option('slider_bottom_bg_color') : '#F0F5F9') ?>;
				padding: 24px;
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-webkit-box-pack: justify;
				    -ms-flex-pack: justify;
				        justify-content: space-between;
				-webkit-box-align: center;
				    -ms-flex-align: center;
				        align-items: center;
				position: relative;
				z-index: 50;
				box-shadow: 0px 10px 18px 0px #3c7fac, 5px 5px 15px 5px rgb(0 0 0 / 0%);
			}
			.slideropt .centerposs {
				text-align: center;
				position: absolute;
				left: 0;
				right: 0;
				font-size: 20px;
				display: flex;
				align-items: center;
				line-height: 20px;
				justify-content: center;
			}
			.centerposs span.prevSlide, .centerposs span.nextSlide {
				cursor: pointer;
				color: var(--txtc);
				font-size: <?php echo (get_option('slider_icon_size')? get_option('slider_icon_size') : '28px') ?>;
			}
			.page_num{
				color: var(--txtc);
				font-size: 20px;
				margin-left: 20px;
    			padding-left: 6px;
				-webkit-user-select: none;
				   -moz-user-select: none;
				    -ms-user-select: none;
				        user-select: none;
			}
			.page_count{
				color: var(--txtc);
				font-size: 20px;
				margin-right: 20px;
    			padding-right: 6px;
				-webkit-user-select: none;
				   -moz-user-select: none;
				    -ms-user-select: none;
				        user-select: none;
			}
			.fullscreen{
				cursor: pointer;
				z-index: 100;
				color: var(--txtc);
				font-size: <?php echo (get_option('slider_icon_size')? get_option('slider_icon_size') : '28px') ?>;
				position: absolute;
				right: 15px;
			}
			div.pdfslider span.lbtn, div.pdfslider span.rbtn {
				background: transparent;
				width: 30%;
				position: absolute;
				display: inline-block;
				height: 100%;
				z-index: 10;
				font-size: <?php echo (get_option('slider_icon_size')? get_option('slider_icon_size') : '28px') ?>;
				-webkit-user-select: none;
				   -moz-user-select: none;
				    -ms-user-select: none;
				        user-select: none;
			}
			span.prevSlide{
				left: 0;
				opacity: .2;
				pointer-events: none;
				-webkit-user-select: none;
				   -moz-user-select: none;
				    -ms-user-select: none;
				        user-select: none;
			}
			span.nextSlide{
				right: 0;
				-webkit-user-select: none;
				   -moz-user-select: none;
				    -ms-user-select: none;
				        user-select: none;
			}
			span.prevSlide:hover, span.nextSlide:hover, .fullscreen:hover{
				opacity: .8;
				transition: .3s;
			}
			div.pdfslider span.lbtn:hover {
				cursor: url("<?php echo PDFSLIDER_URL.'src/left-pointer.png' ?>"), auto;
			}
			div.pdfslider span.rbtn:hover {
				cursor: url(<?php echo PDFSLIDER_URL.'src/right-pointer.png' ?>), auto;
			}
		</style>
		<?php
	}

	function block_render_callback($attributes){
		if(!is_admin(  ))
		{
			global $post;
			$pdf = $attributes['pdf'];
			
			ob_start();
			wp_enqueue_script( 'pdfjs' );
			wp_enqueue_script( 'pdf.warker' );
			wp_enqueue_script( 'pdfslider' );

			$output = 
			'<div style="width: '.(get_option('slider_width') ? get_option('slider_width') : '100').'%;" class="pdfslider">
				<span class="lbtn prevSlide"></span>
				<div class="pdfsliderContents">
					<canvas data="'.$pdf.'" class="the-canvas"></canvas>
				</div>
				<div class="slideropt">
					<div class="centerposs">
						<span class="prevSlide"><i class="fas fa-angle-left"></i></span>
						<span class="page_num"></span> / <span class="page_count"></span>
						<span class="nextSlide"><i class="fas fa-angle-right"></i></span>
					</div>
					<span class="fullscreen"><i class="fas fa-expand"></i></span>
				</div>
				<span class="rbtn nextSlide"></span>
			</div>';

			$output .= ob_get_contents();
			ob_get_clean();
			return $output;
		}
	}

	// Options
	function slider_options(){
		add_options_page( 'PDF Slider', 'PDF Slider', 'manage_options', 'pdf-slider-opt', [$this, 'pdf_slider_options_callback'], null );

		// options
		add_settings_section( 'pdfslider_settings_section', '', '', 'pdfslider_settings_page' );

		// thumb_widths
		add_settings_field( 'thumb_widths', 'Block thumbnail width', [$this, 'thumb_widths_ob'], 'pdfslider_settings_page', 'pdfslider_settings_section');
		register_setting( 'pdfslider_settings_section', 'thumb_widths');
		// slider_width
		add_settings_field( 'slider_width', 'Slider width', [$this, 'slider_width_ob'], 'pdfslider_settings_page', 'pdfslider_settings_section');
		register_setting( 'pdfslider_settings_section', 'slider_width');
		// slider_bg_color
		add_settings_field( 'slider_bg_color', 'Slider Background Color', [$this, 'slider_bg_color_ob'], 'pdfslider_settings_page', 'pdfslider_settings_section');
		register_setting( 'pdfslider_settings_section', 'slider_bg_color');
		// slider_bottom_bg_color
		add_settings_field( 'slider_bottom_bg_color', 'Slider Bottom Background', [$this, 'slider_bottom_bg_color_ob'], 'pdfslider_settings_page', 'pdfslider_settings_section');
		register_setting( 'pdfslider_settings_section', 'slider_bottom_bg_color');
		// slider_text_color
		add_settings_field( 'slider_text_color', 'Slider Text Color', [$this, 'slider_text_color_ob'], 'pdfslider_settings_page', 'pdfslider_settings_section');
		register_setting( 'pdfslider_settings_section', 'slider_text_color');
		// slider_icon_size
		add_settings_field( 'slider_icon_size', 'Slider Icon Size', [$this, 'slider_icon_size_ob'], 'pdfslider_settings_page', 'pdfslider_settings_section');
		register_setting( 'pdfslider_settings_section', 'slider_icon_size');
	}

	function slider_width_ob(){
		echo '<input type="number" name="slider_width" placeholder="% value" value="'.get_option('slider_width').'">';
	}

	function thumb_widths_ob(){
		echo '<input type="number" name="thumb_widths" placeholder="% value" value="'.get_option('thumb_widths').'">';
	}

	function slider_bg_color_ob(){
		echo '<input type="color" name="slider_bg_color" value="'.(get_option('slider_bg_color')? get_option('slider_bg_color') : '#789EC2').'">';
	}

	function slider_bottom_bg_color_ob(){
		echo '<input type="color" name="slider_bottom_bg_color" value="'.(get_option('slider_bottom_bg_color')? get_option('slider_bottom_bg_color') : '#F0F5F9').'">';
	}

	function slider_text_color_ob(){
		echo '<input type="color" name="slider_text_color" value="'.(get_option('slider_text_color')? get_option('slider_text_color') : '#2F4373').'">';
	}

	function slider_icon_size_ob(){
		echo '<input type="number" name="slider_icon_size" placeholder="28px" value="'.(get_option('slider_icon_size')? get_option('slider_icon_size') : '').'">';
	}

	// Options Callback
	function pdf_slider_options_callback(){
		?>
		<h3>PDF Slider</h3>
		<hr>

		<div class="pdf_slider">
		<form style="width: fit-content" method="post" action="options.php">
		<table class="widefat">
		<?php
		settings_fields( 'pdfslider_settings_section' );
		do_settings_fields( 'pdfslider_settings_page', 'pdfslider_settings_section' );
		?>
		</table>
		<br>
		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
		<a href="?page=pdf-slider-opt&action=resetsld" class="button-secondary">Reset</a>
		</form>
		</div>

		<?php
	}

	function reset_saved_options(){
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'resetsld'){
			delete_option( 'thumb_widths' );
			delete_option( 'slider_width' );
			delete_option( 'slider_bg_color' );
			delete_option( 'slider_bottom_bg_color' );
			delete_option( 'slider_text_color' );
			delete_option( 'slider_icon_size' );
			wp_safe_redirect( admin_url( '/options-general.php?page=pdf-slider-opt' ) );
		}
	}

	function adminscripts() { ?>
		<style>
		.pdf-slider{
			width: <?php echo (get_option('thumb_widths') ? get_option('thumb_widths') : 100).'%' ?>
		}
		</style>
		<?php
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		add_action( 'init', [$this, 'initial_callback'] );
		add_action( 'init', [$this, 'reset_saved_options'] );
		add_action( "admin_head", [$this, 'adminScripts'] );
		add_action( "wp_head", [$this, 'frontScripts'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_styles'] );
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'] );
		add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
		add_action( 'admin_menu', [$this, 'slider_options'] );
	}

}
