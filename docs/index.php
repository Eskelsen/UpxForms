<?php

# Documentação

?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Documentação do Módulo">
    <meta name="author" content="Microframeworks">
    <title>Documentação » Vericar</title>

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
		<img class="d-block mx-auto mb-4" src="../vericar.webp" alt="" width="172">
		<h2>Documentação API Vericar</h2>
		<p class="lead">Alguns detalhes sobre o funcionamento do módulo.</p>
		<p class="lead">O registro de logs está <a href="../logs">aqui</a></p>
	</div>
	<div class="row g-5">
		<div class="col-md">
			<hr class="my-4">
<p><strong>Plugin</strong></p>
<p align="justify">O plugin API Vericar está configurado como qualquer outro plugin no WordPress. É possível instalá-lo em outros sites caso as id's e shortcodes seja incluídos nas views (telas).</p>
<a href="plugin.png" target="_blank"><img src="plugin.png" width="100%"></a>
<hr>
<p><strong>Logos</strong></p>
<p align="justify">Todas as logos estão dentro do plugin na pasta <code><a href="../logos" target="_blank">/logos</a></code></p>
<p align="justify">As logos estão convencionadas em letras minúsculas e os espaços substituídos por hifens: <code>nome-da-marca.png</code></p>
<p align="justify">Idealmente devem ser no formato <code>png</code> com transparência, dimensões até <code>300x300</code> e tratadas para terem menos de <code>32 Kb</code></p>
<hr>
<p><strong>Página inicial (landing page)</strong></p>
<p align="justify">Nesta página apenas o formulário é editado. Alguns elementos recebem um <strong>id específico</strong>:</p>
<p align="justify">Formulário: <code>vericar_search</code> (editado via Elementor)</p>
<p align="justify">Campo de preenchimento da placa: <code>form-field-name</code> (default)</p>
<p align="justify">Botão de enviar: <code>vericar_submit</code> (editado via Elementor)</p>
<p align="justify"><strong>Observação:</strong> havia outro formulário de pesquisa no rodapé, ele é idêntico ao do topo, mas seus id's terminam com 2, eg: <code>vericar_search2</code></p>
<hr>
<p><strong>Página de retorno</strong> <code>/retorno-da-consulta/</code></p>
<p align="justify">A página de retorno está protegida de acesso direto pelo plugin. Só é visível a partir do formulário e os campos de resultados são identificados por <strong>shortcodes</strong>:</p>
<p align="justify">Campo identificador para carregamento do plugin, é substituído pela imagem: <code>[vericar_start]</code></p>
<p align="justify">Campos substituídos por dados: <code>[placa], [marca], [renavam], [anodefabricacao], [modelo], [chassi], [anodomodelo]</code></p>
<p align="justify">Outros campos disponíveis (podem ser editados com o Elementor): <code>[local], [modelo], [cor], [combust]</code></p>
<a href="retorno.png" target="_blank"><img src="retorno.png" width="100%"></a>
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