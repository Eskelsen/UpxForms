<?php

# Settings

defined('ABSPATH') || exit;

$options = get_option('upxforms_api_settings');

$planilha	= $options['planilha']  	?? '';
$cred_temp	= $options['credenciais']   ?? '';
$logs		= empty($options['logs']) 	? '' : (($options['logs']=='yes') ? 'checked' : '');

$credenciais = empty($cred_temp) ? '' : json_encode($cred_temp);

?>

<div class="wrap upxforms">
   <form method="post" id="mainform" action="" enctype="multipart/form-data">
	  <h1 class="wp-heading-inline">UpxForms</h1>
	  <hr class="wp-header-end">
	  
	  <p>Configure o id da planilha no Google Sheets e insira o conteúdo do arquivo JSON de credenciais.</p>
	  
	  <input type="hidden" name="upxforms_ctrl" value="1">
	  
      <table class="form-table">
         <tbody>
			
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="upxforms_planilha">Planilha (ID)</label>
               </th>
			   
               <td class="forminp">
                  <fieldset>
                     <input class="widefield" type="text" name="upxforms_planilha" id="planilha" value="<?= $planilha; ?>" placeholder="ID da planilha">
                  </fieldset>
               </td>
            </tr>
					
            <tr valign="top">
               <th scope="row" class="titledesc">
                  <label for="upxforms_credenciais">Credenciais (JSON) <span class="upxforms-help-tip"></span></label>
               </th>
               
               <td class="forminp">
                  <fieldset>
                     <legend class="screen-reader-text"><span>Descrição</span></legend>
                     <textarea class="widefield" rows="6" class="input-text wide-input " type="textarea" name="upxforms_credenciais" id="credenciais" placeholder="Credenciais em formato JSON do arquivo gerado (credentials.json)."><?= $credenciais; ?></textarea>
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
                     <p class="description">Consulte os logs <a href="<?= UPXFORMS_URL . 'logs'; ?>" target="_blank">aqui</a>.</p>
                     <p class="description">Use essa mesma URL de logs para o redirecionamento do Google:</p>
					 <p class="description"><?= UPXFORMS_URL . 'google'; ?></p>
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
