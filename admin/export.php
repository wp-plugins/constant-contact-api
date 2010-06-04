<?php
// export
function constant_contact_export()
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
		$list_id = (isset($_POST['list_id'])) ? $_POST['list_id'] : 0;
		$format = (isset($_POST['format'])&&$_POST['format']=='TXT') ? 'TXT' : 'CSV';
		
		$status = $cc->export_contacts($list_id, $format);
			
		if($status):
			$success[] = __("The export request has been sent to the constant contact API and will be processed shortly, the ID for this activity is $status - <a href='?page=constant-contact-activities&id=$status'>View Activity</a>");
		else:
			$errors[] = __('The subscribers could not be exported: ' . constant_contact_last_error($cc->http_response_code));
		endif;
	endif;
	  
?>

	<div class="wrap">
	<h2>Constant Contact Export</h2>
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
<form name="export" id="export" method="post" action="" enctype="multipart/form-data">
<div id="poststuff" class="metabox-holder">
<div id="post-body">
<div id="post-body-content">

<div id="linkadvanceddiv" class="postbox " >
<div class="handlediv" title="Click to toggle"><br /></div>
<h3 class='hndle'><span>Options</span></h3>
<div class="inside">

<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
	<tr>
		<th valign="top"  scope="row"><label for="link_image">File Format</label></th>
		<td>
		<input type="radio" checked="checked" name="format" id="format" value="CSV" /> CSV<br />
		<input type="radio" name="format" id="format" value="TXT" /> TXT
		<span class="description"><br />Choose what format you want the exported file</span>
		</td>
	</tr>
	<?php
	$cc = constant_contact_create_object();
	
	if(is_object($cc)):
		$lists = $cc->get_all_lists('lists', 0);
		?>
		
		<tr valign="top">
			<th scope="row">Contact Lists</th>
			<td>
			<?php
			if($lists):
				echo '<select name="list_id">';
				foreach($lists as $k => $v):
					echo '<option value="'.$v['id'].'">'.$v['Name'].'</option>';
				endforeach;
				echo '</select>';
			endif;
			?>
			<span class="description"><br />Select the contact list you want to export contacts from.</span>
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

<p class="submit">
<input type="submit" name="submit" class="button-primary" value="<?php _e('Export Subscribers') ?>" />
</p>

</div>
</div>
</form>
</div>

<?php } ?>