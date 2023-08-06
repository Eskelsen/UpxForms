<?php

# Cli Functions

function cli_get_options(){
	$cmd = 'wp db query "SELECT option_value FROM wp_options WHERE option_name =\'upxforms_api_settings\'";';
	$raw = trim(shell_exec($cmd));
	[$trash, $serial] = explode("\n", $raw);
	return unserialize(trim($serial));
}

function cli_set_options($options){
	$data = serialize($options);
	$cmd = 'wp db query "UPDATE wp_options SET option_value=\''
		. addslashes($data)
		. '\' WHERE option_name =\'upxforms_api_settings\'";';
	$u = shell_exec($cmd);
	return true;
}
