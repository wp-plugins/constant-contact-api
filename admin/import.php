<?php
// import
function constant_contact_import()
{
	$errors = false;
	$success = false;
	
	$cc = constant_contact_create_object();
	if(!is_object($cc)):
		return 'Failed to create CC object';
	endif;
	
	$_lists = $cc->get_all_lists();
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
				$success[] = __("The imported contact data has been sent to the constant contact API and will be processed shortly, the ID for this activity is $status - <a href='?page=constant-contact-activities&id=$status'>View Activity</a>");
			else:
				$errors[] = __('Your subscribers could not be imported: ' . constant_contact_last_error($cc->http_response_code));
			endif;
		else:
			$errors[] = __('We could not recognise the file you uploaded');
		endif;
	endif;
	  
?>

	<div class="wrap">
	<h2>Constant Contact Import</h2>
	<?php
	if($success):
	?>
		<div class="success">
		<h3><?php _e("Success"); ?></h3>
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
		<h3><?php _e("Errors"); ?></h3>
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
<form name="import" id="import" method="post" action="" enctype="multipart/form-data">
<div id="poststuff" class="metabox-holder">
<div id="post-body">
<div id="post-body-content">

<div id="linkadvanceddiv" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class='hndle'><span>Options</span></h3>
<div class="inside">

<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
	<tr class="form-field">
		<th valign="top"  scope="row"><label for="link_image">CSV or TXT file</label></th>
		<td><input type="file" name="importfile" class="code" id="importfile" size="50" value="" style="width: 95%" />
		<span class="description"><br />Upload a CSV or TXT file containing your subscribers, see <a href="http://constantcontact.custhelp.com/cgi-bin/constantcontact.cfg/php/enduser/std_adp.php?p_faqid=2523" target="_blank">this page</a> for help with formatting the file</span>
		</td>
	</tr>
	<?php
	$cc = constant_contact_create_object();
	
	if(is_object($cc)):
		$selected_lists = get_option('cc_lists');
		$selected_lists = (!is_array($selected_lists)) ? array() : $selected_lists;
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
			<span class="description"><br />Select the contact lists the imported subscribers will be added to.</span>
			</td>
		</tr>
	<?php
	else:
	?>
	<tr class="form-field">
		<th valign="top"  scope="row" colspan="">Could not get contacts lists, please check your username and password are entered correctly on the settings page.</td>
	</tr>
	<?php
	endif;
	?>
</table>
</div>
</div>
</div>

<input type="hidden" name="import" value="1" />
<p class="submit">
<input type="submit" name="submit" class="button-primary" value="<?php _e('Import Subscribers') ?>" />
</p>

</div>
</div>
</form>
</div>

<?php } ?>