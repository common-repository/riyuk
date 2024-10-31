<div class="wrap">  
<h2>RIYUK Plugin</h2>  
  
<form method="post" action="options.php">  
  
    <?php settings_fields('ryt-settings-group'); ?>  
    <table class="form-table">  
  
  		<tr valign="top">  
  		<th scope="row">Youtube Plugin aktiv?</th>  
  		<td><select name="ryt-youtube">
  			<?php $selected = get_option('ryt-youtube'); ?>
			<option value="0"<?= ( !strlen( $selected ) ? ' selected' : '' ) ?>>Nein</option>
			<option value="1"<?= ( $selected == '1' ? ' selected' : '' ) ?>>Ja</option>
  		</select></td>  
  		</tr> 
  
        <tr valign="top">  
        <th scope="row">Youtube JS Effect</th>  
        <td><select name="ryt-effect"><?php $selected = get_option('ryt-effect'); ?>
        	<option value=""<?= ( !strlen( $selected ) ? ' selected' : '' ) ?>>None</option>
        	<option value="fade"<?= ( $selected == 'fade' ? ' selected' : '' ) ?>>Fading</option>
        	<option value="slide"<?= ( $selected == 'slide' ? ' selected' : '' ) ?>>Sliding</option>
        </select></td>  
        </tr>
        
        <tr valign="top">  
        <th scope="row">Bit.ly Username</th>  
        <td><input name="ryt-bitly-username" type="text" value="<?= get_option( 'ryt-bitly-username' ) ?>" /></td>  
        </tr>  
        <tr valign="top">  
        <th scope="row">Bit.ly API Key</th>  
        <td><input name="ryt-bitly-api" type="text" value="<?= get_option( 'ryt-bitly-api' ) ?>" /></td>  
        </tr>  
  
    </table>  
  
    <p class="submit">  
    <input type="submit" class="button-primary" value="<?php __('Save Changes', 'riyuk') ?>" />  
    </p>  
  
</form>  
</div>  