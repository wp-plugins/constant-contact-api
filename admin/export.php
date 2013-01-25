<?php
/**
 * Export submenu page callback function
 *
 * @global <type> $cc
 * @return <type>
 */
function constant_contact_export()
{
	global $cc;

	// Create the CC api object for use in this page.
	if(!constant_contact_create_object()) { return false; }

	$errors = false;
	$success = false;

	// view all lists
	$_lists = constant_contact_get_lists();
	$lists = array();

	if($_lists):
	foreach($_lists as $k => $v):
		$lists[$v['id']] = $v['Name'];
	endforeach;
	endif;

	if(isset($_POST['submit'])):
		$list_id = (isset($_POST['list_id'])) ? $_POST['list_id'] : 0;
		$format = (isset($_POST['format'])&&$_POST['format']=='TXT') ? 'TXT' : 'CSV';

		$status = $cc->export_contacts($list_id, $format);

		if($status):
			$success[] = __("The export request has been sent to the constant contact API and will be processed shortly, the ID for this activity is <code>$status</code> <a href='".admin_url('admin.php?page=constant-contact-activities&id='.$status)."' class='button-secondary'>View Activity</a>",'constant-contact-api');
		else:
			$errors[] = __('The subscribers could not be exported: ' . constant_contact_last_error($cc->http_response_code),'constant-contact-api');
		endif;
	endif;

?>

	<div class="wrap">
	<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>"><?php _e('Constant Contact Plugin', 'constant-contact-api'); ?> &gt;</a> <?php _e('Export Contacts', 'constant-contact-api'); ?></h2>
	<?php
	if($success):
	?>
		<div id="message" class="updated">
		<h3><?php _e("Success", 'constant-contact-api'); ?></h3>
		<ul>
			<?php
			foreach ($success as $txt):
				echo "<li>".$txt."</li>";
			endforeach;
			?>
		</ul>
		<br />
		</div>
	<?php
	elseif($errors):
	?>
		<div class="error">
		<h3><?php _e("Errors", 'constant-contact-api'); ?></h3>
		<ul>
			<?php
			foreach ($errors as $error):
				echo "<li>".$error."</li>";
			endforeach;
			?>
		</ul>
		<br />
		</div>
	<?php
	endif;
	?>
	<form name="export" id="export" method="post" action="" enctype="multipart/form-data">
		<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
			<tr>
				<th valign="top"  scope="row"><p><label for="link_image"><?php _e('File Format', 'constant-contact-api'); ?></label></p></th>
				<td>
					<label class="description" for="format_csv"><input type="radio" checked="checked" name="format" id="format_csv" value="CSV" /> <span><?php _e('CSV', 'constant-contact-api'); ?></span></label>
					<label class="description" for="format_txt" style="display:block;"><input type="radio" name="format" id="format_txt" value="TXT" /> <span><?php _e('TXT', 'constant-contact-api'); ?></span></label>
					<span class="description"><?php _e('Choose what format you want the exported file', 'constant-contact-api'); ?></span>
				</td>
			</tr>
			<?php
				$lists = $cc->get_all_lists('lists', 0);
				?>

				<tr valign="top">
					<th scope="row"><p><label for="list_id"><?php _e('Contact Lists', 'constant-contact-api'); ?></label></p></th>
					<td>
					<?php
					if($lists):
						echo '<select name="list_id" id="list_id">';
						foreach($lists as $k => $v):
							echo '<option value="'.$v['id'].'">'.$v['Name'].'</option>';
						endforeach;
						echo '</select>';
					endif;
					?>
					<span class="description"><br /><?php _e('Select the contact list you want to export contacts from.', 'constant-contact-api'); ?></span>
					</td>
				</tr>
		</table>

		<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="<?php _e('Export Subscribers', 'constant-contact-api'); ?>" />
		</p>

	</form>
</div>

<?php } ?>