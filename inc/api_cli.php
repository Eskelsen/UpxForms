<?php

# UpxForms API Cli

# Dev Mode: Show Errors
ini_set('display_errors', true);
ini_set('display_startup_erros', true);
error_reporting(E_ALL);

echo json_encode(['status' => true, 'msg' => 'endpoint api cli']);
