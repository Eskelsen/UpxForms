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
	# Dev Mode: Show Errors
	ini_set('display_errors', true);
	ini_set('display_startup_erros', true);
	error_reporting(E_ALL);

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

# UpxForms Logs File
define('UPXFORMS_LOG', UPXFORMS_DOCS . 'logs.txt');

# Update Settings
function upxforms_settings_saved() {
	echo 
	'<div class="notice notice-success is-dismissible">
		<p><strong>Suas configurações foram salvas.</strong></p>
	</div>';
}

if (!empty($_POST)) { // editar
	
	$options['email'] 		= $_POST['upxforms_email'] 			?? '';
	$options['pswd']  		= $_POST['upxforms_pswd'] 			?? '';
	$options['token'] 		= $_POST['upxforms_token'] 			?? '';
	$options['logs']  		= empty($_POST['upxforms_logs']) ? 'no' : 'yes';
	$options['search'] 		= $_POST['upxforms_search'] 		?? '';
	$options['result'] 		= $_POST['upxforms_result'] 		?? '';
	
	update_option('upxforms_api_settings', $options);
	
	add_action('admin_notices', 'upxforms_settings_saved');
}

# Style
function upxforms_style() {
	wp_enqueue_style('upxforms-style', plugin_dir_url(__FILE__) . 'upxforms-style.css', array(), '2.0');
}

add_action('admin_enqueue_scripts', 'upxforms_style');

# Menu
function upxforms_menu() {
	add_menu_page(
		'Gerenciar UpxForms',
		'UpxForms',
		'manage_options',
		'upxforms-settings',
		'upxforms_settings',
		'dashicons-media-spreadsheet'
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

# Uninstall
function uninstall_upxforms(){
	delete_options('upxforms_api_settings');
}

register_uninstall_hook(__FILE__, 'uninstall_upxforms');

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

# UpxForms Toolkit
function upxformsToolkitCode(){
	
	echo '
	
<!-- UpxForms Toolkit Code -->
<script type="text/javascript">



</script>
<!-- End UpxForms Toolkit Code -->

';
}
