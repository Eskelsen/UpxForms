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
define('UPXFORMS_LOGS',	UPXFORMS_PATH . 'logs/');
define('UPXFORMS_URL', 	plugin_dir_url(__FILE__));
define('UPXFORMS_API', 	UPXFORMS_URL  . 'api/');

# Logs & Debug
$options = get_option('upxforms_api_settings');
$logs = empty($options['logs']) ? false : ($options['logs']=='yes');
define('UPXFORMS_LOGS_ON', $logs);
include UPXFORMS_INC . 'logs.php';

# UpxForms Logs File
define('UPXFORMS_LOG_FILE', UPXFORMS_LOGS . 'logs.txt');

# Style
function upxforms_style() {
	wp_register_style('upxforms-style', plugin_dir_url(__FILE__) . 'upxforms-style.css');
	wp_enqueue_style('upxforms-style');
}

add_action('wp_enqueue_scripts', 'upxforms_style');

# Update Settings
function upxforms_settings_saved() {
	echo 
	'<div class="notice notice-success is-dismissible">
		<p><strong>Suas configurações foram salvas.</strong></p>
	</div>';
}

function upxforms_settings_json_fail() {
	echo 
	'<div class="notice notice-warning is-dismissible">
		<p><strong>JSON inválido ou corrompido.</strong></p>
	</div>';
}

if (!empty($_POST['upxforms_ctrl'])) {
	
	$options = get_option('upxforms_api_settings');
	
	$options['planilha'] 	= empty($_POST['upxforms_planilha']) ? '' : trim($_POST['upxforms_planilha']);
	$options['credenciais'] = empty($_POST['upxforms_credenciais']) ? '' : trim($_POST['upxforms_credenciais']);
	
	if ($options['credenciais']) {
		$options['credenciais'] = json_decode($options['credenciais'], 1);
		if (empty($options['credenciais'])) {
			add_action('admin_notices', 'upxforms_settings_json_fail');
		}
	}
	
	$options['logs'] = empty($_POST['upxforms_logs']) ? 'no' : 'yes';
	
	update_option('upxforms_api_settings', $options);
	
	add_action('admin_notices', 'upxforms_settings_saved');
}

# Menu
function upxforms_menu() {
	add_action('admin_enqueue_scripts', 'upxforms_style');
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
    register_rest_route( 'upxforms/v1', '/form', array(
        // 'methods'  => WP_REST_Server::READABLE, // GET
        'methods'  => ['GET', 'POST'], // 'GET" || ['GET', 'POST']
        'callback' => 'upxforms_api_inc',
		'permission_callback' => '__return_true'
    ) );
}

add_action( 'rest_api_init', 'upxforms_api_route' );

# Uninstall
function uninstall_upxforms(){
	delete_options('upxforms_api_settings');
}

register_uninstall_hook(__FILE__, 'uninstall_upxforms');

# UpxForms Toolkit
function upxformsToolkitCode(){
	
	$endpoint = get_rest_url(null, 'upxforms/v1/form/');
	
	echo "\n\n" . '<!-- UpxForms Toolkit Code -->
<script type="text/javascript">

const url_base = "' . $endpoint . '";

const form = document.getElementById("regForm");

function sendData(){

	if (!form.checkValidity()) {
		console.log("Por favor, preencha todos os campos obrigatórios corretamente.");
		return;
	}
	
	console.log("valido");
	event.preventDefault();
	
	let nome = document.getElementsByName("nome")[0].value;
	
	nome = nome.trim();
	
	let rsocial = document.getElementsByName("rsocial")[0].value;
	
	rsocial = rsocial.trim();

	let cnpj = document.getElementsByName("cnpj")[0].value;
	
	cnpj = cnpj.trim();
	
	let email = document.getElementsByName("email")[0].value;
	
	email = email.trim();
	
	let phone = document.getElementsByName("phone")[0].value;
	
	phone = phone.replace(/\D/g, "");
	
	let mensagem = document.getElementsByName("mensagem")[0].value;
	
	mensagem = mensagem.trim();
	
	let data = { 
		Sender:		"tbf-lebbe-form",

		nome:		nome,
		rsocial:	rsocial,
		cnpj:		cnpj,
		email:		email,
		phone:		phone,
		mensagem:	mensagem,
		// sec:	sec
	};
	
	fetch(url_base, {
	  method: "POST",
	  body: JSON.stringify(data),
	})
	.then((response) => response.text())
	.then((data) => {
		treatReturn(data);
	  })
	  .catch((error) => {
		console.error("Error:", error);
	});
}

function treatReturn(data){
	console.log("treatReturn");
	console.log(data);
}

</script>
<!-- End UpxForms Toolkit Code -->' . "\n\n";
}

# Shortcode com atributo id
function upxforms_shortcode($atts) {
	
    $atts = shortcode_atts(array(
        'id' => 0,
    ), $atts);

    $form = $atts['id'];
    $html = '';
	
	$file = __DIR__ . '/forms/' . $form;

    if (file_exists($file)) {
		add_action('wp_footer', 'upxformsToolkitCode');
        $html = file_get_contents($file);
    }

    return $html;
}

add_shortcode('upxforms', 'upxforms_shortcode');

# Process Core
function upxforms_load_content($in){
	# Verifica se alguma string confirma que nosso elemento está presente pra carregar nosso código
	if (strpos($in, 'upxforms_search')!==false) {
		add_action('wp_footer', 'upxformsToolkitCode');
		return $in;
	}
	# Verifica se um shortcode nosso está presente para carregarmos ou tratarmos o conteúdo
	if (strpos($in, '[upxforms_start]')!==false) {
		# Verifica se já não foi carregado
		if (!in_the_loop() || !is_singular() || !is_main_query()) {
			return $in;
		}
		# Carrega ou trata o conteúdo
		return upxforms_result($in);
	}
	return $in;
}

// add_filter('the_content', 'upxforms_load_content');
