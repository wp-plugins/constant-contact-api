<?php // $Id$

// Register the settings we need on the settings page
function constant_contact_register_settings()
{
	$options_group = 'constant-contact';
	register_setting($options_group, 'cc_username');
	register_setting($options_group, 'cc_password');
	register_setting($options_group, 'cc_extra_field_mappings');
	register_setting($options_group, 'cc_register_page_method');
	register_setting($options_group, 'cc_default_opt_in');
	register_setting($options_group, 'cc_signup_title');
	register_setting($options_group, 'cc_signup_description');
	register_setting($options_group, 'cc_signup_widget_title');
	register_setting($options_group, 'cc_signup_widget_description');
	register_setting($options_group, 'cc_list_selection_format');
	register_setting($options_group, 'cc_uninstall_method');
	register_setting($options_group, 'cc_lists');
	register_setting($options_group, 'cc_exclude_lists');
	register_setting($options_group, 'cc_widget_lists');
	register_setting($options_group, 'cc_widget_exclude_lists');
	register_setting($options_group, 'cc_widget_show_list_selection');
	register_setting($options_group, 'cc_widget_show_firstname');
	register_setting($options_group, 'cc_widget_show_lastname');
	register_setting($options_group, 'cc_widget_list_selection_format');
	register_setting($options_group, 'cc_widget_redirect_url');
	register_setting($options_group, 'cc_widget_list_selection_title');
}

// show the admin settings page
function constant_contact_settings()
{
    // See if the user has posted us some information
    if(isset($_GET['updated'])):
		?>
		<div class="updated">
			<p><strong><?php _e('Your settings have been saved', 'mt_trans_domain' ); ?></strong></p>
		</div>
		<?php
    endif;
?>

	<div class="wrap">
	<h2>Constant Contact Settings</h2>
	<form method="post" action="options.php">
	<?php settings_fields( 'constant-contact' ); ?>

	<h3>Account Details</h3>
	<p>If your entering your username and password for the first time please save the page to see your available contact lists.</p>
	<table class="form-table">
	<tr valign="top">
		<th scope="row">Constant Contact Username</th>
		<td>
		<input type="text" name="cc_username" value="<?php echo get_option('cc_username'); ?>" autocomplete="off" size="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Constant Contact Password</th>
		<td>
		<input type="password" name="cc_password" value="<?php echo get_option('cc_password'); ?>" autocomplete="off" size="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Uninstall Method</th>
		<td>
		<input <?php echo (get_option('cc_uninstall_method')=='remove') ? 'checked="checked"':''; ?> type="radio" name="cc_uninstall_method" value="remove" /> Remove
		<input <?php echo (get_option('cc_uninstall_method')=='keep') ? 'checked="checked"':''; ?> type="radio" name="cc_uninstall_method" value="keep" /> Keep
		<span class="description"><br />When you deactivate the plugin you can keep your username and password or remove them, if your upgrading you should keep them but if your completely removing the plugin you should remove them, no other settings will be kept.</span>
		</td>
	</tr>
	</table>
	
	<h3>Register Page Settings</h3>
	<table class="form-table">
	<tr valign="top">
		<th scope="row">Register Page Subscribe Method</th>
		<td>
		<input <?php echo (get_option('cc_register_page_method')=='none') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" value="none" /> Disabled
		<input <?php echo (get_option('cc_register_page_method')=='checkbox') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" value="checkbox" /> Checkbox
		<input <?php echo (get_option('cc_register_page_method')=='lists') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" value="lists" /> List Selection
		<span class="description"><br />You can display a checkbox on the register page and automatically subscribe users to the contact lists you select below or you can display a contact list selection for the users to choose which lists to subscribe to.</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">List Selection Format</th>
		<td>
		<input <?php echo (get_option('cc_list_selection_format')=='select') ? 'checked="checked"':''; ?> type="radio" name="cc_list_selection_format" value="select" /> Multi-Select
		<input <?php echo (get_option('cc_list_selection_format')=='checkbox') ? 'checked="checked"':''; ?> type="radio" name="cc_list_selection_format" value="checkbox" /> Checkbox
		<span class="description"><br />If you use the "List Selection" method above you can change the format of the list selection to either a group of checkboxes or a multi-select drop down, the default is a multi-select drop down, this relates to how the contact lists are displayed on the register page.</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Opt-in users by default?</th>
		<td>
		<input <?php echo (get_option('cc_default_opt_in')) ? 'checked="checked"':''; ?> type="checkbox" name="cc_default_opt_in" value="1" />
		<span class="description"><br />This determines if the register page checkbox is checked by default or if using the "List Selection" method the user will have all lists selected by default, uncheck this box if you want users to have to manually subscribe.</span>
		</td>
	</tr>
	<?php
	$cc = constant_contact_create_object();
	
	if(is_object($cc)):
		$selected_lists = get_option('cc_lists');
		$selected_lists = (!is_array($selected_lists)) ? array() : $selected_lists;
		$cc_exclude_lists = get_option('cc_exclude_lists');
		$exclude_lists = (!is_array($cc_exclude_lists)) ? array() : $cc_exclude_lists;
		$lists = $cc->get_all_lists();
		?>

		<tr valign="top">
			<th scope="row">Contact Lists</th>
			<td>
			<?php
			if($lists):
			foreach($lists as $k => $v):
				if(in_array($v['id'], $selected_lists)):
					echo '<input name="cc_lists[]" type="checkbox" checked="checked" value="'.$v['id'].'" /> '.$v['Name'].'<br />';
				else:
					echo '<input name="cc_lists[]" type="checkbox" value="'.$v['id'].'" /> '.$v['Name'].'<br />';
				endif;
			endforeach;
			endif;
			?>
			<span class="description"><br />If you use the checkbox method above you should choose which lists the subscriber will be automatically added to, if using the "List Selection" method you should select which lists will be available to user explicity (hiding all others), if you do not select any lists here the user will be able to view all of your contact lists, apart from any hidden below.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Hide Contact Lists</th>
			<td>
			<?php
			if($lists):
			foreach($lists as $k => $v):
				if(in_array($v['id'], $exclude_lists)):
					echo '<input name="cc_exclude_lists[]" type="checkbox" checked="checked" value="'.$v['id'].'" /> '.$v['Name'].'<br />';
				else:
					echo '<input name="cc_exclude_lists[]" type="checkbox" value="'.$v['id'].'" /> '.$v['Name'].'<br />';
				endif;
			endforeach;
			endif;
			?>
			<span class="description"><br />When using the "List Selection" method you can hide any of the contact lists above from the user, this option has no effect if using the checkbox subscribe method.</span>
			</td>
		</tr>
		<?php
	endif;
	?>
	<tr valign="top">
		<th scope="row">Signup Title</th>
		<td>
		<input type="text" name="cc_signup_title" value="<?php echo get_option('cc_signup_title'); ?>" size="50" />
		<span class="description"><br />The title text displayed on the register page, if enabled</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Signup Description</th>
		<td>
		<input type="text" name="cc_signup_description" value="<?php echo get_option('cc_signup_description'); ?>" size="50" />
		<span class="description"><br />The description text displayed on the register page, if enabled</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Extra Field Mappings</th>
		<td>
		<textarea name="cc_extra_field_mappings" cols="50" rows="6" class="large-text code"><?php echo get_option('cc_extra_field_mappings'); ?></textarea>
		<span class="description"><br />Specify the mappings for your extra fields, if these fields are found in your register form they will be sent to constant contact, you should define these in the format FirstName:ActualFieldname and separate with a comma. You will only need to change the second value to match your form fieldnames.<br />You should use another plugin to add the fields to your register page.</span>
		</td>
	</tr>
	</table>
	
	
	<h3>Signup Widget Settings</h3>
	<a name="widget"></a>
	<table class="form-table">
	<tr valign="top">
		<th scope="row">Show First Name Field</th>
		<td>
		<input <?php echo (get_option('cc_widget_show_firstname')) ? 'checked="checked"':''; ?> type="radio" name="cc_widget_show_firstname" value="1" /> Yes
		<input <?php echo (!get_option('cc_widget_show_firstname')) ? 'checked="checked"':''; ?> type="radio" name="cc_widget_show_firstname" value="0" /> No
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Show Last Name Field</th>
		<td>
		<input <?php echo (get_option('cc_widget_show_lastname')) ? 'checked="checked"':''; ?> type="radio" name="cc_widget_show_lastname" value="1" /> Yes
		<input <?php echo (!get_option('cc_widget_show_lastname')) ? 'checked="checked"':''; ?> type="radio" name="cc_widget_show_lastname" value="0" /> No
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Signup Widget Title</th>
		<td>
		<input type="text" name="cc_signup_widget_title" value="<?php echo get_option('cc_signup_widget_title'); ?>" size="50" />
		<span class="description"><br />The title text displayed in the sidebar widget, if enabled</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Signup Widget Description</th>
		<td>
		<input type="text" name="cc_signup_widget_description" value="<?php echo get_option('cc_signup_widget_description'); ?>" size="50" />
		<span class="description"><br />The description text displayed in the sidebar widget below the title, if enabled</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Signup Widget Thanks Page</th>
		<td>
		<input type="text" name="cc_widget_redirect_url" value="<?php echo get_option('cc_widget_redirect_url'); ?>" size="50" />
		<span class="description"><br />You can optionally redirect the user to a thank you page upon successfully submitting the signup form, enter the full address including http:// or leave blank for no redirection</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Show List Selection?</th>
		<td>
		<input <?php echo (get_option('cc_widget_show_list_selection')) ? 'checked="checked"':''; ?> type="checkbox" name="cc_widget_show_list_selection" value="1" />
		<span class="description"><br />If you show the list selection the user can choose which contact lists they want to subscribe to, if you don't show the list selection you must select which contact lists the subscriber will be automatically added to below.</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">List Selection Title</th>
		<td>
		<input type="text" name="cc_widget_list_selection_title" value="<?php echo get_option('cc_widget_list_selection_title'); ?>" size="50" />
		<span class="description"><br />The title text displayed in the sidebar widget above the list selection, if enabled</span>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">List Selection Format</th>
		<td>
		<input <?php echo (get_option('cc_widget_list_selection_format')=='checkbox') ? 'checked="checked"':''; ?> type="radio" name="cc_widget_list_selection_format" value="checkbox" /> Checkboxes
		<input <?php echo (get_option('cc_widget_list_selection_format')=='select') ? 'checked="checked"':''; ?> type="radio" name="cc_widget_list_selection_format" value="select" /> Multi-Select
		</td>
	</tr>
<?php
	$cc = constant_contact_create_object();
	
	if(is_object($cc)):
		$selected_lists = get_option('cc_widget_lists');
		$selected_lists = (!is_array($selected_lists)) ? array() : $selected_lists;
		$cc_exclude_lists = get_option('cc_widget_exclude_lists');
		$exclude_lists = (!is_array($cc_exclude_lists)) ? array() : $cc_exclude_lists;
		$lists = $cc->get_all_lists();
		?>

		<tr valign="top">
			<th scope="row">Contact Lists</th>
			<td>
			<?php
			if($lists):
			foreach($lists as $k => $v):
				if(in_array($v['id'], $selected_lists)):
					echo '<input name="cc_widget_lists[]" type="checkbox" checked="checked" value="'.$v['id'].'" /> '.$v['Name'].'<br />';
				else:
					echo '<input name="cc_widget_lists[]" type="checkbox" value="'.$v['id'].'" /> '.$v['Name'].'<br />';
				endif;
			endforeach;
			endif;
			?>
			<span class="description"><br />If you show the list selection you can select which lists are available above, alternatively if you disable the list selection you should select which lists the user is automatically subscribed to (if you show the list selection and don't select any lists above all lists will be available to the user including newly created ones).</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Hide Contact Lists</th>
			<td>
			<?php
			if($lists):
			foreach($lists as $k => $v):
				if(in_array($v['id'], $exclude_lists)):
					echo '<input name="cc_widget_exclude_lists[]" type="checkbox" checked="checked" value="'.$v['id'].'" /> '.$v['Name'].'<br />';
				else:
					echo '<input name="cc_widget_exclude_lists[]" type="checkbox" value="'.$v['id'].'" /> '.$v['Name'].'<br />';
				endif;
			endforeach;
			endif;
			?>
			<span class="description"><br />If you show the list selection you can select which lists to always exclude from the selection.</span>
			</td>
		</tr>
	<?php
	endif;
	?>
	</table>
	
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	</div>
<?php
}
?>
<?php
// intro page
function constant_contact_api() {
?>
	<div class="wrap">
	<h2>Constant Contact API</h2>
		<p>This is the <a href="http://integrationservic.es/constant-contact/wordpress-plugin.php
" target="_blank">Constant Contact plugin for Wordpress</a>.</p>
		<p>If you do not have a constant contact account <a target="_blank" href="<?php echo CC_TRIAL_URL?>">Signup for a free 60-day trial</a>.</p>
		<p>The plugin is provided by <a target="_blank" href="http://justphp.co.uk">James Benson</a>.</p>
		<p>If you find the plugin useful please consider <a target="_blank" href="http://integrationservic.es/constant-contact/donate.php">donating</a> a few dollars towards future development costs.</p>
	</div>
<?php } ?>