<?php

# Logs

# Timelapse (180 days)
define('UPXFORMS_LOG_CLEAR', (60*60*24)*180);

# UpxForms Logs File
define('UPXFORMS_LOG', __DIR__ . '/logs.txt');

?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Logs do Módulo">
    <meta name="author" content="Microframeworks">
    <title>Logs » UpxForms</title>

<link href="https://getbootstrap.com/docs/5.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Favicons -->
<link rel="icon" href="https://microframeworks.com/ups/mf-icon-sm.png" type="image/png">
<meta name="theme-color" content="#260c59">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="form-validation.css" rel="stylesheet">
  </head>
  <body class="bg-light">
    
<div class="container">
  <main>
	<div class="py-5 text-center">
		<img class="d-block mx-auto mb-4" src="../upxforms.webp" alt="" width="172">
		<h2>Logs do Módulo</h2>
		<p class="lead">Lista em ordem cronológica decrescente dos registros de log.</p>
		<p class="lead">A documentação está <a href="../docs">aqui</a></p>
		<a href=".">Atualizar</a>
	</div>
	<div class="row g-5">
		<div class="col-md">
			<hr class="my-4">

<?php

$limit 	= time() - UPXFORMS_LOG_CLEAR;
$raw 	= [];
$ctn 	= array_filter(explode("\r\n",file_get_contents(UPXFORMS_LOG)));

foreach ($ctn as $line) {
	$tmp = explode('|', $line);
	if ($tmp[0]>=$limit) {
		$raw[] = $line;
	}
}

$data = implode("\r\n",$raw) . "\r\n";
file_put_contents(UPXFORMS_LOG, $data);

$raw = array_reverse($raw);

if (empty($raw)) {
    echo 'Nada por enquanto...';
} else {

	foreach($raw as $line) {
		$tmp = explode('|',$line);
		$mid = $tmp[0] ?? null;
		$mid = ($mid) ? date('H:i:s d/m/Y', $mid) : null;
		$msg = $tmp[2] ?? null;
		echo $mid . ': ' . $msg . '<br>';
	}
}
?>

		<hr class="my-4">
      </div>
    </div>
  </main>

  <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">&copy; <?= date('Y'); ?> Microframeworks</p>
    <ul class="list-inline">
      <li class="list-inline-item">
		<a href="https://microframeworks.com/web/suporte/" target="_blank">Suporte</a>
	  </li>
    </ul>
  </footer>
</div>
  </body>
</html>