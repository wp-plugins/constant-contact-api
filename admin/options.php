<?php // $Id$

// Hook settings registration function
add_action( 'admin_init', 'constant_contact_register_settings' );

// Register the settings we need on the settings page
function constant_contact_register_settings()
{
	$group = 'constant-contact';

	register_setting('constant-contact', 'cc_username');
	register_setting('constant-contact', 'cc_password');
	register_setting('constant-contact', 'cc_uninstall_method');
	register_setting('constant-contact-registration', 'cc_lists');
	register_setting('constant-contact-registration', 'cc_exclude_lists');
	register_setting('constant-contact-registration', 'cc_signup_title');
	register_setting('constant-contact-registration', 'cc_signup_description');
	register_setting('constant-contact-registration', 'cc_signup_description_position');
	register_setting('constant-contact-registration', 'cc_extra_field_mappings');
	register_setting('constant-contact-registration', 'cc_register_page_method');
	register_setting('constant-contact-registration', 'cc_default_opt_in');
	register_setting('constant-contact-registration', 'cc_list_selection_format');
	register_setting('constant-contact-registration', 'cc_default_select_option_text');
}

// show the admin settings page
function constant_contact_settings()
{
	global $cc, $cc_create_failed;

?>

	<div class="wrap">

		<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>"><?php _e('Constant Contact Plugin', 'constant-contact-api'); ?> &gt;</a> <?php _e('Settings', 'constant-contact-api'); ?></h2>
	<?php 	/**
	 * Show the account status message
	 */

	if(!get_option('cc_password') && !get_option('cc_username')) {
		constant_contact_get_signup_message();
	} else {
		constant_contact_create_object();

		if(!empty($cc_create_failed)) {
			echo "<div id='constant-contact-warning' class='error'>".wpautop($cc_create_failed)."</div>";
		}
		constant_contact_add_gravity_forms_notice();
	}
?>

	<form method="post" action="options.php">
		<?php
			settings_fields( 'constant-contact' );
			wp_nonce_field('constant_contact','update_cc_options');
		?>

		<h3><?php _e('Account Details', 'constant-contact-api'); ?></h3>

		<?php
	    if (get_option('cc_configured')) {
		    echo "<div id='message' class='updated'><p>".__('Your username and password seem to be working.', 'constant-contact-api')."</p></div>";
	    }
	    ?>
		<table class="form-table">
			<tr>
				<th scope="row"><p><label for="cc_username"><span><?php _e('Constant Contact Username', 'constant-contact-api'); ?></span></label></p></th>
				<td>
				<input type="text" name="cc_username" id="cc_username" value="<?php echo get_option('cc_username'); ?>" autocomplete="off" size="50" />
				</td>
			</tr>
			<tr>
				<th scope="row"><p><label for="cc_password"><span><?php _e('Constant Contact Password', 'constant-contact-api'); ?></span></label></th>
				<td>
				<input type="text" name="cc_password" id="cc_password" value="<?php echo get_option('cc_password'); ?>" autocomplete="off" size="50" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><p><label for="cc_uninstall_method_keep"><span><?php _e('Uninstall Method', 'constant-contact-api'); ?></span></label></th>
				<td>
				<p><label for="cc_uninstall_method_keep" class="howto"><input <?php echo (!get_option('cc_uninstall_method') || get_option('cc_uninstall_method')=='keep') ? 'checked="checked"':''; ?> type="radio" name="cc_uninstall_method" id="cc_uninstall_method_keep" value="keep" /> <span><?php _e(sprintf('%sKeep data in database%s, will be there if re-activated', '<strong>', '</strong>'), 'constant-contact-api'); ?></span></label>
				<label for="cc_uninstall_method_remove" class="howto"><input <?php echo (get_option('cc_uninstall_method')=='remove') ? 'checked="checked"':''; ?> type="radio" name="cc_uninstall_method" id="cc_uninstall_method_remove" value="remove" /> <span><?php _e(sprintf('%sRemove all data%s stored in database', '<strong>', '</strong>'), 'constant-contact-api'); ?></span></label></p>
				<p class="description"><?php _e('When you deactivate the plugin you can keep your username and password or remove them, if your upgrading you should keep them but if your completely removing the plugin you should remove them, no other settings will be kept.', 'constant-contact-api'); ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'constant-contact-api') ?>" />
		</p>

	</form>
<?php
}
?>