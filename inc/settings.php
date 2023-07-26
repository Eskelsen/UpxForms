<?php

# Settings

defined('ABSPATH') || exit;

# Get Settings
$options = get_option('upxforms_api_settings');

$email  = $options['email']  ?? '';
$pswd   = $options['pswd']   ?? '';
$token  = $options['token']  ?? '';
$logs   = empty($options['logs']) ? '' : (($options['logs']=='yes') ? 'checked' : '');

?>

<div class="wrap upxforms">
   <form method="post" id="mainform" action="" enctype="multipart/form-data">
	  <h1 class="wp-heading-inline">API UpxForms</h1>
	  <hr class="wp-header-end">
	  
	  <p>Gerenciamento de credencias e telas da API. (<a href="<?= UPXFORMS_URL . 'docs'; ?>" target="_blank">Documentação</a>)</p>
	  
      <table class="form-table">
         <tbody>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="upxforms_email">E-mail</label>
               </th>
			   
               <td class="forminp">
                  <fieldset>
                     <input class="widefield" type="text" name="upxforms_email" id="upxforms_email" style="" value="<?= $email; ?>" placeholder="">
                  </fieldset>
               </td>
            </tr>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="upxforms_pswd">Senha</label>
               </th>
               <td class="forminp">
                  <fieldset>
                     <input class="widefield" type="password" name="upxforms_pswd" id="upxforms_pswd" style="" value="<?= $pswd; ?>" placeholder="">
                  </fieldset>
               </td>
            </tr>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="upxforms_token">Token</label>
               </th>
               <td class="forminp">
                  <fieldset>
                     <input class="widefield" type="text" name="upxforms_token" id="upxforms_token" style="" value="<?= $token; ?>" placeholder="">
                  </fieldset>
               </td>
            </tr>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="upxforms_logs">Logs</label>
               </th>
               <td class="forminp">
                  <fieldset>
                     <label for="logs">
                     <input class="widefield" type="checkbox" name="upxforms_logs" id="upxforms_logs" style="" value="1" <?= $logs; ?>>Gravar logs de uso</label><br>
                     <p class="description">Os logs da API ficam <a href="<?= UPXFORMS_URL . 'logs'; ?>" target="_blank">aqui</a>. Você também pode ler a documentação do plugin <a href="<?= UPXFORMS_URL . 'docs'; ?>" target="_blank">aqui</a>.</p>
                  </fieldset>
               </td>
            </tr>
			
         </tbody>
      </table>
	  
      <p class="submit">
         <button class="button-primary upxforms-save-button" type="submit" value="Salvar alterações">Salvar alterações</button>		
      </p>
	  
   </form>
</div>
