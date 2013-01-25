<?php
/**
 * Import submenu page callback function
 *
 * @global <type> $cc
 * @return <type>
 */
function constant_contact_import()
{
	global $cc;

	// Create the CC api object for use in this page.
	if(!constant_contact_create_object()) { return false; }

	$errors = false;
	$success = false;

	// view all lists
	$_lists = constant_contact_get_lists(isset($_REQUEST['refresh_lists']));
	$lists = array();

	if($_lists):
	foreach($_lists as $k => $v):
		$lists[$v['id']] = $v['Name'];
	endforeach;
	endif;

	if(isset($_POST['submit'])):
		$lists = (isset($_POST['cc_lists'])) ? $_POST['cc_lists'] : array();

		if(trim($_FILES['importfile']['tmp_name']) != '' && is_uploaded_file($_FILES['importfile']['tmp_name'])):

			$status = $cc->create_contacts($_FILES['importfile']['tmp_name'], $lists);
			if($status):
				$success = __("<strong>Import success:</strong> The imported contact data has been sent to the constant contact API and will be processed shortly, the ID for this activity is <code>$status</code>. <a href='?page=constant-contact-activities&id=$status' class='action button'>View Activity</a>",'constant-contact-api');
			else:
				$errors[] = __('Your subscribers could not be imported: ' . constant_contact_last_error($cc->http_response_code),'constant-contact-api');
			endif;
		else:
			if(empty($_POST['file'])) {
				$errors[] = __('You did not select a file to upload!','constant-contact-api');
			} else {
				$errors[] = __('We could not recognise the file you uploaded','constant-contact-api');
			}
		endif;
	endif;

?>

	<div class="wrap">
	<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>"><?php _e('Constant Contact Plugin', 'constant-contact-api'); ?> &gt;</a> <?php _e('Import Contacts', 'constant-contact-api'); ?></h2>
	<?php
	if($success):
	?>
		<div id="message" class="updated">
			<p><?php echo $success; ?></p>
		</div>
	<?php

	elseif($errors):
	?>
		<div class="error">
		<h3><?php _e("Error"); ?></h3>
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
	<p class="alignright"><label class="howto" for="refresh_lists"><span><?php _e('Are the displayed lists inaccurate?', 'constant-contact-api'); ?></span> <a href="<?php echo add_query_arg('refresh', 'lists'); ?>" class="button-secondary action" id="refresh_lists"><?php _e('Refresh Lists', 'constant-contact-api'); ?></a></label></p>

<div class="clear"></div>

<form name="import" id="import" method="post" action="<?php echo remove_query_arg('refresh_lists'); ?>" enctype="multipart/form-data">

	<table class="form-table">
		<tr class="form-field">
			<th scope="row">
				<p><label for="importfile"><?php _e('CSV or TXT file', 'constant-contact-api'); ?></label></p>
				<p><span class="description howto"><?php _e('Upload a CSV or TXT file containing your subscribers', 'constant-contact-api'); ?></span></p>
			</th>
			<td>
				<p>
					<input type="file" name="importfile" class="code" id="importfile" size="50" value="" style="width: 95%" />
					<span class="howto"><?php echo sprintf(__('See %sthis page%s for help with formatting the file. You can also refer to the sample file (<a href="%s" target="_blank">email-import-sample.txt</a>) included in the plugin.', 'constant-contact-api'), '<a href="http://support2.constantcontact.com/articles/FAQ/2061" target="_blank">', '</a>', CC_FILE_URL.'email-import-sample.txt'); ?></span>
				</p>
			</td>
		</tr>
		<?php
			$selected_lists = get_option('cc_lists');
			$selected_lists = (!is_array($selected_lists)) ? array() : $selected_lists;
			$lists = constant_contact_get_lists();
			?>

			<tr>
				<th scope="row">
					<p><label><?php _e('Contact Lists', 'constant-contact-api'); ?></label></p>
					<p><span class="description howto"><?php _e('Select the contact lists the imported subscribers will be added to.', 'constant-contact-api'); ?></span></p>
				</th>
				<td>
				<?php
				if($lists):
				echo '<ul class="categorychecklist" style="height:250px; overflow:auto;">';
				foreach($lists as $k => $v):
					if(in_array($v['id'], $selected_lists)):
						echo '<li><label for="cc_lists_'.$v['id'].'"><input class="menu-item-checkbox" id="cc_lists_'.$v['id'].'" name="cc_lists[]" type="checkbox" checked="checked" value="'.$v['id'].'" /> '.$v['Name'].'</label></li>';
					else:
						echo '<li><label for="cc_lists_'.$v['id'].'"><input class="menu-item-checkbox" id="cc_lists_'.$v['id'].'" name="cc_lists[]" type="checkbox" value="'.$v['id'].'" /> '.$v['Name'].'</label></li>';
					endif;
				endforeach;
				echo '</ul>';
				endif;
				?>
				</td>
			</tr>
	</table>

	<p class="submit">
		<input type="hidden" name="import" value="1" />
		<input type="submit" name="submit" class="button-primary" value="<?php _e('Import Subscribers', 'constant-contact-api'); ?>" />
	</p>
</form>

<?php
}