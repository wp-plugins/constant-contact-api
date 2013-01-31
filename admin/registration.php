<?php // $Id$

// show the admin settings page
function constant_contact_registration_settings()
{
    // See if the user has posted us some information
    if(isset($_GET['updated'])):
		?>
		<div class="updated">
			<p><strong><?php _e('Your settings have been saved', 'constant-contact-api' ); ?></strong></p>
		</div>
		<?php
    endif;

    /**
     * Fetch full list of contact lists for various purposes
     */
    $all_contact_lists = constant_contact_get_lists();

?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.moreInfo').click(function() {
				$('#registrationScreenshots').slideToggle();
				return false;
			});

			$('.wrap form input[name=cc_register_page_method],.wrap form input[name=cc_list_selection_format]').live('load click change', function() {
				updateListSelectionVisibility();
			});

			function updateListSelectionVisibility() {
				if($('input[name=cc_register_page_method]:checked').val() === 'none') {
					$('.wrap form table.form-table tr').not('.alwaysshow').hide();
					$('tr.cc_default_select_option_text').hide();
				} else {
					$('.wrap form table.form-table tr').not('.alwaysshow,.list_selection').show();

					if($('input[name=cc_register_page_method]:checked').val() === 'lists') {
						$('tr.list_selection').show();
						if($('input[name=cc_list_selection_format]:checked').val() === 'dropdown') {
							$('tr.cc_default_select_option_text').show();
						} else {
							$('tr.cc_default_select_option_text').hide();
						}
					} else {
						$('tr.list_selection, tr.cc_default_select_option_text').hide();
					}
				}
			}

			updateListSelectionVisibility();
		});
	</script>

	<div class="wrap">
		<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>"><?php _e('Constant Contact Plugin', 'constant-contact-api' ); ?> &gt;</a> <?php _e('Registration &amp; User Profile Settings', 'constant-contact-api'); ?></h2>
			<?php constant_contact_admin_refresh('lists'); ?>
	<form method="post" action="options.php">
	<?php settings_fields( 'constant-contact-registration' ); ?>
	<div class="alignright" style="width:510px; display:none;" id="registrationScreenshots">
		<h4 style="text-align:center;"><a href="<?php echo get_bloginfo('url'); ?>/wp-login.php?action=register">Blog Registration</a> Form Screenshots', 'constant-contact-api'); ?></h4>
		<div class="alignleft" style="width:250px;"><img src="<?php echo CC_FILE_URL; ?>images/registration-form-before.jpg" alt="registration-form-before" width="250" height="299"/><p class="caption howto">User subscription method: <strong>Disabled</strong></p></div>
		<div class="alignright" style="width:250px;"><img src="<?php echo CC_FILE_URL; ?>images/screenshot-1.jpg" alt="screenshot-1" width="250" height="367"/><p class="howto">User subscription method: List Selection, List Selection Format: <strong>Checkboxes</strong></p></div>
	</div>
	<h3><?php _e('Registration Screen and User Profile Settings', 'constant-contact-api'); ?></h3>
	<p class="description"><?php echo sprintf(__('Enabling this tool will add subscription options for logged-in users on your site in the WordPress <a href="%s">registration</a> and user profile screens. <strong><a href="#registrationScreenshots" class="moreInfo">View screenshots</a></strong>.</p>
	<p><strong>Note:</strong> If new user registration is disabled for your WordPress installation ("Anyone can register" in <strong>Settings &gt; General</strong>)  then visitors to your site will not be able to subscribe with this  method, but it will still be available to logged-in users via their  personal profile options.', 'constant-contact-api'), get_bloginfo( 'url' ).'/wp-login.php?action=register'); ?></p>

	<table class="form-table">
		<tr valign="top" class="alwaysshow">
			<th scope="row">
				<p>
					<label>
						<span><?php _e('User Subscription Method', 'constant-contact-api'); ?></span>
					</label>
				</p>
			</th>
			<td>
				<p>
					<label for="cc_register_page_method_none" class="howto">
							<input <?php checked(!get_option('cc_register_page_method') || get_option('cc_register_page_method')=='none') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" id="cc_register_page_method_none" value="none" />
							<span><?php _e('Disabled', 'constant-contact-api'); ?></span>
					</label>
					<label for="cc_register_page_method_checkbox" class="howto">
						<input <?php echo (get_option('cc_register_page_method')=='checkbox') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" id="cc_register_page_method_checkbox" value="checkbox" />
						<span><?php _e('Single Checkbox', 'constant-contact-api'); ?></span>
					</label>
					<label for="cc_register_page_method_lists" class="howto">
						<input <?php echo (get_option('cc_register_page_method')=='lists') ? 'checked="checked"':''; ?> type="radio" name="cc_register_page_method" id="cc_register_page_method_lists" value="lists" />
						<span><?php _e('List Selection', 'constant-contact-api'); ?></span>
					</label>
				</p>
			<p class="description"><?php _e('
				<strong>Single Checkbox</strong>: Shows users a checkbox which, if ticked, will automatically subscribe them to the lists you select below in the <strong>Active Contact Lists</strong> section.<br />
				<strong>List Selection</strong>: Displays the <strong>Active Contact Lists</strong> as a set of checkboxes/multi-select and lets the user decide which ones they want.', 'constant-contact-api'); ?></p>
			</td>
		</tr>
		<tr valign="top" class="list_selection hide-if-js" <?php if(get_option('cc_register_page_method')!=='lists') { echo ' style="display:none;"';} ?>>
			<th scope="row">
				<p>
					<label>
						<span><?php _e('List Selection Format', 'constant-contact-api'); ?></span>
					</label>
				</p>
			</th>
			<td>
				<p>
					<label for="cc_list_selection_format_checkbox" class="howto">
						<input <?php echo (!get_option('cc_list_selection_format') || get_option('cc_list_selection_format')=='checkbox') ? 'checked="checked"':''; ?> type="radio" id="cc_list_selection_format_checkbox" name="cc_list_selection_format" value="checkbox" />
						<span><?php _e('Checkboxes', 'constant-contact-api'); ?></span>
					</label>

					<label for="cc_list_selection_format_select" class="howto">
						<input <?php echo (get_option('cc_list_selection_format')=='select') ? 'checked="checked"':''; ?> type="radio" id="cc_list_selection_format_select" name="cc_list_selection_format" value="select" />
						<span><?php _e('Multi-Select', 'constant-contact-api'); ?></span>
					</label>

					<label for="cc_list_selection_format_dropdown" class="howto">
						<input <?php echo (get_option('cc_list_selection_format')=='dropdown') ? 'checked="checked"':''; ?> type="radio" id="cc_list_selection_format_dropdown" name="cc_list_selection_format" value="dropdown" />
						<span><?php _e('Dropdown List', 'constant-contact-api'); ?></span>
					</label>
				</p>
				<p class="description">
					<?php _e('This controls how the contact lists are displayed on the registration screen and user profile settings if you use the <strong>List Selection</strong> method above. <br />
					<strong>Checkboxes</strong> will offer separate checkboxes.
					<strong>Multi-Select</strong> will offer the list as a multi-select drop-down.', 'constant-contact-api'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top" class="cc_default_select_option_text">
			<th scope="row">
				<p>
					<label for="cc_default_select_option_text">
						<span><?php _e('Default Option Text', 'constant-contact-api'); ?></span>
					</label>
				</p>
			</th>
			<td>
			<p><label for="cc_default_select_option_text"><input type="text" class="text" size="50" id="cc_default_select_option_text" name="cc_default_select_option_text" value="<?php $option = get_option('cc_default_select_option_text'); echo ($option === false) ? __('Select a List&hellip;', 'constant-contact-api') : $option; ?>" /></label></p>
			<p class="description">
				<?php _e('If "Opt-in users by default" (below) is not checked, this will be the default option in the dropdown menu. Leave blank to not show this option.', 'constant-contact-api'); ?>
			</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<p>
					<label for="cc_default_opt_in">
						<span><?php _e('Opt-in users by default?', 'constant-contact-api'); ?></span>
					</label>
				</p>
			</th>
			<td>
			<p><label for="cc_default_opt_in"><input <?php echo (get_option('cc_default_opt_in')) ? 'checked="checked"':''; ?> type="checkbox" id="cc_default_opt_in" name="cc_default_opt_in" value="1" /> <span><?php _e('Yes, subscribe my users by default.', 'constant-contact-api'); ?></span></label></p>
			<p class="description">
				<?php _e('Should the opt-in checkbox be checked by default? If using the "List Selection" method, should lists be pre-selected by default.', 'constant-contact-api'); ?>
			</p>
			</td>
		</tr>
		<?php
		/**
		 * Contact Lists and Hidden Contact Lists options
		 * Only show them if we have lists from the API already
		 */
		if(!empty($all_contact_lists) && is_array($all_contact_lists)) {

			/**
			 * Get already-selected lists to pre-fill the selections
			 * Otherwise set it up as empty array.
			 */
			$active_lists = get_option('cc_lists');
			if (!is_array($active_lists))
				$active_lists = array();

			/**
			 * Get already-selected HIDDEN lists, oterwise set up empty array
			 */
			$hidden_lists = get_option('cc_exclude_lists');
			if (!is_array($hidden_lists))
				$hidden_lists = array();

			/**
			 * Output the lists of lists
			 */
			?>

			<tr valign="top">
				<th scope="row"><p><label><span><?php _e('Active Contact Lists', 'constant-contact-api'); ?></span></label></th>
				<td>
				<p class="widefat" style="padding:5px;margin:0 0 5px 0;max-height: 250px; overflow:auto;">
				<?php
				// Loop through all lists and output them as checkboxes
				foreach($all_contact_lists as $key => $details) {

					// Set up list_checked to contain checked=checked for lists that should be checked.
					$list_checked = '';
					if (in_array($details['id'], $active_lists))
						$list_checked = ' checked="checked"';

					// Echo the checkbox and label including the checked status determined above
					echo '<label for="cc_lists'.$details['id'].'">';
					echo '<input name="cc_lists[]" type="checkbox" value="'.$details['id'].'" '. $list_checked .' id="cc_lists'.$details['id'].'" /> ';
					echo $details['Name'] . '</label><br />';
				}
				?>
				</p>
				<p class="description">
					<?php _e('If you use the <strong>Single Checkbox</strong> method in the <strong>Register Page Subscribe Method</strong> option then users who check the box will be automatically subscribed to all contact lists chosen above.<br />
					If using the <strong>List Selection</strong> method then only the selected lists will be shown to the user to choose from.<br />
					<strong>If you do not select any lists above</strong> the user will be able to choose from all of your contact lists, apart from those set to be hidden using the setting below.', 'constant-contact-api'); ?>
				</p>
				</td>
			</tr>
			<tr valign="top" class="list_selection hide-if-js">
				<th scope="row"><p><label><span><?php _e('Hidden Contact Lists', 'constant-contact-api'); ?></span></label></th>
				<td>
				<p class="widefat" style="padding:5px;margin:0 0 5px 0; max-height: 250px; overflow:auto;">

				<?php
				foreach($all_contact_lists as $key => $details) {
					// Set up list_checked to contain checked=checked for lists that should be checked.
					$list_checked = '';
					if (in_array($details['id'], $hidden_lists))
						$list_checked = ' checked="checked"';

					// Echo the checkbox and label including the checked status determined above
					echo '<label for="cc_exclude_lists'.$details['id'].'">';
					echo '<input name="cc_exclude_lists[]" type="checkbox" value="'.$details['id'].'" '. $list_checked .' id="cc_exclude_lists'.$details['id'].'" /> ';
					echo $details['Name'] . '</label><br />';
				}
				?>
				</p>
				<p class="description">
					<?php _e('When using the <strong>List Selection</strong> method you can  select contact lists in this setting to hide them from users. This  option has no effect when you are using the Single Checkbox subscribe method.', 'constant-contact-api'); ?></p>
				</td>
			</tr>
			<?php
		} // End is_array($all_contact_lists)
		?>
		<tr valign="top">
			<th scope="row"><p><label for="cc_signup_title"><span><?php _e('Signup Title', 'constant-contact-api'); ?></span></label></th>
			<td>
			<input type="text" name="cc_signup_title" id="cc_signup_title" value="<?php echo get_option('cc_signup_title'); ?>" size="50" />
			<p class="description">
				 <?php _e('Title for the signup form displayed on the registration screen and user profile settings if enabled.', 'constant-contact-api'); ?>
			</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<p>
					<label for="cc_signup_description">
						<span><?php _e('Signup Description', 'constant-contact-api'); ?></span>
					</label>
				</p>
			</th>
			<td>
				<textarea name="cc_signup_description" id="cc_signup_description" cols="50" rows="4"><?php echo get_option('cc_signup_description'); ?></textarea>
				<p class="description">
					<?php _e('Signup form description text displayed on the registration screen and user profile setting, if enabled. HTML is allowed. Paragraphs will be added automatically like in posts.', 'constant-contact-api'); ?>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<p>
					<label>
						<span><?php _e('Signup Description Position', 'constant-contact-api'); ?></span>
					</label>
				</p>
			</th>
			<td>
				<label for="cc_signup_description_position_before" class="howto">
					<input <?php checked(get_option('cc_signup_description_position')==='before'); ?> type="radio" name="cc_signup_description_position" id="cc_signup_description_position_before" value="before" />
					<span><?php _e('Before the Opt-in', 'constant-contact-api'); ?></span>
				</label>
				<label for="cc_signup_description_position_after" class="howto">
					<input <?php checked(!get_option('cc_signup_description_position') || get_option('cc_signup_description_position')==='after'); ?> type="radio" name="cc_signup_description_position" id="cc_signup_description_position_after" value="after" />
					<span><?php _e('After the Opt-in', 'constant-contact-api'); ?></span>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<p>
					<label for="cc_extra_field_mappings">
						<span><?php _e('Extra Field Mappings', 'constant-contact-api'); ?></span>
					</label>
				</p>
			</th>
			<td>

				<textarea name="cc_extra_field_mappings" id="cc_extra_field_mappings" cols="50" rows="6" class="large-text code"><?php echo get_option('cc_extra_field_mappings'); ?></textarea>

				<?php if(function_exists('check_ccfg_compatibility') && check_ccfg_compatibility()) {?>
					<p class="description"><?php _e('Forms created using the Constant Contact widget support separate field mapping. This is for the registration form only.', 'constant-contact-api'); ?></p>
				<?php } ?>
				<p class="description"><?php _e('Specify the mappings for your extra fields, if these fields are found in your register form they will be sent to constant contact, you should define these in the format FirstName:ActualFieldname and separate with a comma. You will only need to change the second value to match your form fieldnames.</p><p class="description">Note: the fields are not automatically added; you must use another plugin such as <a href="http://wordpress.org/extend/plugins/register-plus/" target="_blank">Register Plus</a> to add the fields to your register page.', 'constant-contact-api'); ?>
				</p>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'constant-contact-api') ?>" />
	</p>
	</form>
	</div>
<?php
}
