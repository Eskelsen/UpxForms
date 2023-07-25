<?php

/**
 * 
 * Plugin Name: API Vericar
 * Description: Gerenciamento de Chaves e Views da API Vericar
 * Version: 1.0
 * Author: Microframeworks
 * Author URI: https://microframeworks.com
 * License: Proprietary
 *
 */

defined('ABSPATH') || exit;

if (empty($_SERVER['HTTP_HOST'])) return;

# Constants
define('VERICAR_PATH', 	plugin_dir_path(__FILE__));
define('VERICAR_INC', 	VERICAR_PATH . 'inc/');
define('VERICAR_DOCS', 	VERICAR_PATH . 'logs/');
define('VERICAR_URL', 	plugin_dir_url(__FILE__));
define('VERICAR_API', 	VERICAR_URL  . 'api/');

# Logs & Debug
$options = get_option('vericar_api_settings');
$logs = empty($options['logs']) ? false : ($options['logs']=='yes');
define('VERICAR_LOGS_ON', $logs);
include VERICAR_INC . 'logs.php';

# Vericar Logs File
define('VERICAR_LOG', VERICAR_DOCS . 'logs.txt');

# Update Settings
function vericar_settings_saved() {
	echo 
	'<div class="notice notice-success is-dismissible">
		<p><strong>Suas configurações foram salvas.</strong></p>
	</div>';
}

if (!empty($_POST['vericar_email'])) {
	
	$options['email'] 		= $_POST['vericar_email'] 		?? '';
	$options['pswd']  		= $_POST['vericar_pswd'] 		?? '';
	$options['token'] 		= $_POST['vericar_token'] 		?? '';
	$options['logs']  		= empty($_POST['vericar_logs']) ? 'no' : 'yes';
	$options['search'] 		= $_POST['vericar_search'] 		?? '';
	$options['result'] 		= $_POST['vericar_result'] 		?? '';
	
	update_option('vericar_api_settings', array_filter($options));
	
	add_action('admin_notices', 'vericar_settings_saved');
}

# Style
function vericar_style() {
	wp_enqueue_style('vericar-style', plugin_dir_url(__FILE__) . 'vericar-style.css', array(), '2.0');
}

add_action('admin_enqueue_scripts', 'vericar_style');

# Menu
function wpdocs_add_my_custom_menu() {
	// Add an item to the menu.
	add_menu_page(
		'Gerenciar API Vericar',
		'API Vericar',
		'manage_options',
		'vericar-settings',
		'vericar_settings',
		'dashicons-admin-network'
	);
}

add_action('admin_menu', 'wpdocs_add_my_custom_menu');

# Settings
function vericar_settings(){
	include VERICAR_INC . 'settings.php';
}

# API REST wp-json /wp-json/vericar/v1/consulta
function vericar_api_inc() {
    include VERICAR_INC . 'api.php';
}

function vericar_api_route() {
    register_rest_route( 'vericar/v1', '/consulta', array(
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'vericar_api_inc',
		'permission_callback' => '__return_true'
    ) );
}

// add_action( 'rest_api_init', 'vericar_api_route' );

# Process Core
function vericar_verify_content($in){
	if (strpos($in, 'vericar_search')!==false) {
		add_action('wp_footer', 'vericarToolkitCode');
		return $in;
	}
	if (strpos($in, '[vericar_start]')!==false) {
		if (!in_the_loop() || !is_singular() || !is_main_query()) {
			return $in;
		}
		return vericar_result($in);
	}
	return $in;
}

add_filter('the_content', 'vericar_verify_content');

# Vericar Result
function vericar_result($in) {
    return include VERICAR_INC . 'result.php';
}

# Vericar Toolkit
function vericarToolkitCode(){
	
	echo '
	
<!-- Vericar Toolkit Code -->
<script type="text/javascript">

const vericar_form = document.getElementById("vericar_search");

if (vericar_form) {
	vericar_form.action = "' . site_url() . '/retorno-da-consulta";
}

const vericar_submit = document.getElementById("vericar_submit");

if (vericar_submit) {
	vericar_submit.addEventListener("click", function(event){
		if (document.getElementById("form-field-name").value!="") {
			vericar_form.submit();
		}
	});
}


const vericar_form2 = document.getElementById("vericar_search2");

if (vericar_form2) {
	vericar_form2.action = "' . site_url() . '/retorno-da-consulta";
}

const vericar_submit2 = document.getElementById("vericar_submit2");

if (vericar_submit2) {
	vericar_submit2.addEventListener("click", function(event){
		if (document.getElementById("form-field-name2").value!="") {
			vericar_form2.submit();
		}
	});
}

</script>
<!-- End Vericar Toolkit Code -->

';
}
