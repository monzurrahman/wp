<?php 
/*
Plugin Name: Assets Ninja
Plugin URI: http://www.google.com
Author: Monzur Rahman
Author URI: http://www.bijoyjobs.com
Version: 0.0.1
License: GPL2 Or Latest
Text-domain: assets-ninja
Description: Assets Management Plugin
Domain Path: /languages/
*/

/*function anp_activation_func(){

}
register_activation_hook( __FILE__, "anp_activation_func");
function anp_deactivation_func(){

}
register_deactivation_hook( __FILE__, "anp_deactivation_func" );

function anp_plugin_text_domain_func(){
	load_plugin_textdomain( "post-to-qrcode", false, dirname( __FILE__ ).'/languages' );
}
add_action( "plugins_loaded", "anp_plugin_text_domain_func");
*/



class AssetsNinja{
	//$this->version = time();
	function __construct(){
		add_action( "plugins_loaded", array($this, 'load_assets') );
		add_action( "wp_enqueue_scripts", array($this,"load_front_assets"));
		add_action( "admin_enqueue_scripts", array($this,"load_admin_assets"));
		add_shortcode( "gbmedia", array($this,"asn_bgmedia_shortcode") );
	}

	function load_admin_assets($screen){
		$_screen = get_current_screen();
		if('edit-tags.php' == $screen && 'post_tag'==$_screen->taxonomy){
		wp_enqueue_script( "admin-script", plugin_dir_url( __FILE__ ).'assets/admin/js/admin.js', array( 'jquery' ), time(), true );
		}
	}

	function load_front_assets(){
		wp_enqueue_script( "assetsninja-main", plugin_dir_url(__FILE__).'assets/public/js/main.js', array('jquery'), time(), true );
		//wp_enqueue_script( "assetsninja-main-js", plugin_dir_url(__FILE__).'assets/public/js/another.js', array('jquery'), time(), true );

		$data= array(
		'name' => 'Monzur',
		'city' => 'Jashore'
		);
		wp_localize_script( "assetsninja-main", "sitedata", $data);
	}

	

	function load_assets(){
		load_plugin_textdomain( "assets-ninja", false, plugin_dir_url( __FILE__ ).'/languages/' );
	}

	function asn_bgmedia_shortcode($ttributes){
		$attachment_img_src= wp_get_attachment_image_src( $ttributes['id'], 'medium');
		$shortcode_output = <<<EOD
<div style="height: 300px; width: 300px; background-image:url({$attachment_img_src[0]});>

</div>

EOD;
	return $shortcode_output ;
	}
}

new AssetsNinja();