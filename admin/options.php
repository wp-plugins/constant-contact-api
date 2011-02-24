<?php // $Id$

/* 
	Version 1.1.0.1
	Changes made by Zack Katz; katzwebdesign on April 5, 2010
		Converted settings page description fields to `<textarea>` to allow for better description visibility.
		
	Version 1.1.1
	Changes made by Zack Katz; katzwebdesign on May 27, 2010
		Wrapped list checkboxes in labels
*/

// Hook settings registration function
add_action( 'admin_init', 'constant_contact_register_settings' );

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
	register_setting($options_group, 'cc_use_legacy_widget');
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

    /**
     * Fetch full list of contact lists for various purposes
     */
    $all_contact_lists = constant_contact_get_lists(true);
	
?>

	<div class="wrap">
	<h2>Constant Contact Settings</h2>
	<?php 	/**
	 * Show the account status message
	 */
	 $cc = constant_contact_create_object(true);
	 ?>
	 	
	<?php if(is_object($cc)) { ?>
	<div class="widefat">
	<div class="wrap" style="padding-bottom:10px;">
	<h2>Plugin Pages</h2>
	<h3>Plugin Configuration</h3>
	<ul class="ul-disc">
		<li><a href="<?php echo admin_url('admin.php?page=constant-contact-registration'); ?>">Registration &amp; Profile</a> - Configure plugin settings for adding newletter signup capabilities to the WordPress registration form.</li>
		<?php if(defined('CC_FORM_GEN_PATH')) { ?>
		<li><a href="<?php echo admin_url('admin.php?page=constant-contact-forms'); ?>">Form Design</a> - Design a signup form from the ground up.</li>
		<?php } ?>
	</ul>
	<h3>Account Actions</h3>
	<ul class="ul-disc">
		<li><a href="<?php echo admin_url('admin.php?page=constant-contact-activities'); ?>">Activities</a> - View your account's recent activity, including: sent campaigns, exports, and imports.</li>
		<li><a href="<?php echo admin_url('admin.php?page=constant-contact-import'); ?>">Import</a> - Import contacts into your choice of user lists.</li>
		<li><a href="<?php echo admin_url('admin.php?page=constant-contact-export'); ?>">Export</a> - Export contacts to <code>.csv</code> and <code>.txt</code> format.</li>
		<li><a href="<?php echo admin_url('admin.php?page=constant-contact-lists'); ?>">Lists</a> - Add, remove, and edit your contact lists.</li>
		<li><a href="<?php echo admin_url('admin.php?page=constant-contact-campaigns'); ?>">Campaigns</a> - View details of your sent &amp; draft email campaigns, <strong>including email campaign stats</strong> such as # Sent, Opens, Clicks, Bounces, OptOuts, and Spam Reports.</li>
	</ul></div>
	</div>
<?php } ?>

	<form method="post" action="options.php">
	<?php settings_fields( 'constant-contact' ); ?>

	<h3>Account Details</h3>

	<?php


    if (is_object($cc)) :
	    echo "<div id='message' class='updated'><p>Your username and password seem to be working.</p></div>";
    else :
		?>
		<div class='error'>
			<p><strong>This plugin is not configured with a valid Constant Contact account.</strong></p>
			<p>Please enter a valid username and password then press the Save Changes button before continuing.</p>
		</div>
		<?php
    endif;

	?>
	<table class="form-table widefat">
	<tr>
		<th scope="row"><p><label for="cc_username"><span>Constant Contact Username</span></label></p></th>
		<td>
		<input type="text" name="cc_username" id="cc_username" value="<?php echo get_option('cc_username'); ?>" autocomplete="off" size="50" />
		</td>
	</tr>
	<tr>
		<th scope="row"><p><label for="cc_password"><span>Constant Contact Password</span></label></th>
		<td>
		<input type="password" name="cc_password" id="cc_password" value="<?php echo get_option('cc_password'); ?>" autocomplete="off" size="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><p><label for="cc_uninstall_method_keep"><span>Uninstall Method</span></label></th>
		<td>
		<p><label for="cc_uninstall_method_keep" class="howto"><input <?php echo (!get_option('cc_uninstall_method') || get_option('cc_uninstall_method')=='keep') ? 'checked="checked"':''; ?> type="radio" name="cc_uninstall_method" id="cc_uninstall_method_keep" value="keep" /> <span><strong>Keep data in database</strong>, will be there if re-activated</span></label>
		<label for="cc_uninstall_method_remove" class="howto"><input <?php echo (get_option('cc_uninstall_method')=='remove') ? 'checked="checked"':''; ?> type="radio" name="cc_uninstall_method" id="cc_uninstall_method_remove" value="remove" /> <span><strong>Remove all data</strong> stored in database</span></label></p>
		<p class="description">
			When you deactivate the plugin you can keep your username and password or remove them, if your upgrading you should keep them but if your completely removing the plugin you should remove them, no other settings will be kept.
		</p>
		</td>
	</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	</div>
<?php
}
?>