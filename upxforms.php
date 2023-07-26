<?php

/**
 * 
 * Plugin Name: UpxForms
 * Description: A WordPress Google Sheets Leads Forms Plugin
 * Version: 1.0
 * Author: Microframeworks
 * Author URI: https://microframeworks.com/web/
 * License: MIT
 *
 */

defined('ABSPATH') || exit;

if (empty($_SERVER['HTTP_HOST'])) return;

# Constants
define('UPXFORMS_PATH',	plugin_dir_path(__FILE__));
define('UPXFORMS_INC', 	UPXFORMS_PATH . 'inc/');
define('UPXFORMS_DOCS',	UPXFORMS_PATH . 'logs/');
define('UPXFORMS_URL', 	plugin_dir_url(__FILE__));
define('UPXFORMS_API', 	UPXFORMS_URL  . 'api/');

# Logs & Debug
$options = get_option('upxforms_api_settings');
$logs = empty($options['logs']) ? false : ($options['logs']=='yes');
define('UPXFORMS_LOGS_ON', $logs);
include UPXFORMS_INC . 'logs.php';

# Vericar Logs File
define('UPXFORMS_LOG', UPXFORMS_DOCS . 'logs.txt');

# Update Settings
function upxforms_settings_saved() {
	echo 
	'<div class="notice notice-success is-dismissible">
		<p><strong>Suas configurações foram salvas.</strong></p>
	</div>';
}

if (!empty($_POST['upxforms_email'])) {
	
	$options['email'] 		= $_POST['upxforms_email'] 			?? '';
	$options['pswd']  		= $_POST['upxforms_pswd'] 			?? '';
	$options['token'] 		= $_POST['upxforms_token'] 			?? '';
	$options['logs']  		= empty($_POST['upxforms_logs']) ? 'no' : 'yes';
	$options['search'] 		= $_POST['upxforms_search'] 		?? '';
	$options['result'] 		= $_POST['upxforms_result'] 		?? '';
	
	update_option('upxforms_api_settings', array_filter($options));
	
	add_action('admin_notices', 'upxforms_settings_saved');
}

# Style
function upxforms_style() {
	wp_enqueue_style('upxforms-style', plugin_dir_url(__FILE__) . 'upxforms-style.css', array(), '2.0');
}

add_action('admin_enqueue_scripts', 'upxforms_style');

# Menu
function upxforms_menu() {
	// Add an item to the menu.
	add_menu_page(
		'Gerenciar API UpxForms',
		'API UpxForms',
		'manage_options',
		'upxforms-settings',
		'upxforms_settings',
		'dashicons-admin-network'
	);
}

add_action('admin_menu', 'upxforms_menu');

# Settings
function upxforms_settings(){
	include UPXFORMS_INC . 'settings.php';
}

# API REST wp-json /wp-json/upxforms/v1/consulta
function upxforms_api_inc() {
    include UPXFORMS_INC . 'api.php';
}

function upxforms_api_route() {
    register_rest_route( 'upxforms/v1', '/consulta', array(
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'upxforms_api_inc',
		'permission_callback' => '__return_true'
    ) );
}

// add_action( 'rest_api_init', 'upxforms_api_route' );

# Process Core
function upxforms_verify_content($in){
	if (strpos($in, 'upxforms_search')!==false) {
		add_action('wp_footer', 'upxformsToolkitCode');
		return $in;
	}
	if (strpos($in, '[upxforms_start]')!==false) {
		if (!in_the_loop() || !is_singular() || !is_main_query()) {
			return $in;
		}
		return upxforms_result($in);
	}
	return $in;
}

add_filter('the_content', 'upxforms_verify_content');

# Vericar Toolkit
function upxformsToolkitCode(){
	
	echo '
	
<!-- Vericar Toolkit Code -->
<script type="text/javascript">

const upxforms_form = document.getElementById("upxforms_search");

if (upxforms_form) {
	upxforms_form.action = "' . site_url() . '/retorno-da-consulta";
}

const upxforms_submit = document.getElementById("upxforms_submit");

if (upxforms_submit) {
	upxforms_submit.addEventListener("click", function(event){
		if (document.getElementById("form-field-name").value!="") {
			upxforms_form.submit();
		}
	});
}


const upxforms_form2 = document.getElementById("upxforms_search2");

if (upxforms_form2) {
	upxforms_form2.action = "' . site_url() . '/retorno-da-consulta";
}

const upxforms_submit2 = document.getElementById("upxforms_submit2");

if (upxforms_submit2) {
	upxforms_submit2.addEventListener("click", function(event){
		if (document.getElementById("form-field-name2").value!="") {
			upxforms_form2.submit();
		}
	});
}

</script>
<!-- End Vericar Toolkit Code -->

';
}
