<?php

# Settings

defined('ABSPATH') || exit;

# Get Settings
$options = get_option('vericar_api_settings');

$email  = $options['email']  ?? '';
$pswd   = $options['pswd']   ?? '';
$token  = $options['token']  ?? '';
$logs   = empty($options['logs']) ? '' : (($options['logs']=='yes') ? 'checked' : '');

?>

<div class="wrap vericar">
   <form method="post" id="mainform" action="" enctype="multipart/form-data">
	  <h1 class="wp-heading-inline">API Vericar</h1>
	  <hr class="wp-header-end">
	  
	  <p>Gerenciamento de credencias e telas da API. (<a href="<?= VERICAR_URL . 'docs'; ?>" target="_blank">Documentação</a>)</p>
	  
      <table class="form-table">
         <tbody>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="vericar_email">E-mail</label>
               </th>
			   
               <td class="forminp">
                  <fieldset>
                     <input class="widefield" type="text" name="vericar_email" id="vericar_email" style="" value="<?= $email; ?>" placeholder="">
                  </fieldset>
               </td>
            </tr>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="vericar_pswd">Senha</label>
               </th>
               <td class="forminp">
                  <fieldset>
                     <input class="widefield" type="password" name="vericar_pswd" id="vericar_pswd" style="" value="<?= $pswd; ?>" placeholder="">
                  </fieldset>
               </td>
            </tr>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="vericar_token">Token</label>
               </th>
               <td class="forminp">
                  <fieldset>
                     <input class="widefield" type="text" name="vericar_token" id="vericar_token" style="" value="<?= $token; ?>" placeholder="">
                  </fieldset>
               </td>
            </tr>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="vericar_logs">Logs</label>
               </th>
               <td class="forminp">
                  <fieldset>
                     <label for="logs">
                     <input class="widefield" type="checkbox" name="vericar_logs" id="vericar_logs" style="" value="1" <?= $logs; ?>>Gravar logs de uso</label><br>
                     <p class="description">Os logs da API ficam <a href="<?= VERICAR_URL . 'logs'; ?>" target="_blank">aqui</a>. Você também pode ler a documentação do plugin <a href="<?= VERICAR_URL . 'docs'; ?>" target="_blank">aqui</a>.</p>
                  </fieldset>
               </td>
            </tr>
			
         </tbody>
      </table>
	  
      <p class="submit">
         <button class="button-primary vericar-save-button" type="submit" value="Salvar alterações">Salvar alterações</button>		
      </p>
	  
   </form>
</div>
