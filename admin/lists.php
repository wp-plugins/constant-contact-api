<?php
		
// lists
function constant_contact_lists()
{
	$cc = constant_contact_create_object();
	if(!is_object($cc)):
		return '<p>Contact Lists Not Available</p></div>';
	endif;
	
	$lists = array();
	
	if(isset($_GET['delete'])):
		// delete list
		
		$id = (int) $_GET['delete'];
		$list = $cc->get_list($id);
		
		if(!$list):
			return '<p>Contact List Not Found</p></div>';
		endif;
		
		$status = $cc->delete_list($id);
		
		if($status):
			_e('The contact list has been deleted - <a href="?page=constant-contact-lists">OK</a>');
		else:
			_e('Failed to delete contact list: ' .  constant_contact_last_error($cc->http_response_code));
		endif;
		
	elseif(isset($_REQUEST['add'])):
		// add List
		
		$list_name = '';
		$sort_order = 99;
		
		if(isset($_POST['add'])):
			$list_name = $_POST['list_name'];
			$sort_order = $_POST['sort_order'];
			
			$status = $cc->create_list($list_name, 'false', $sort_order);
			
			if($status):
				_e('The contact list has been created - <a href="?page=constant-contact-lists">OK</a>');
			else:
				_e('Failed to create contact list: ' .  constant_contact_last_error($cc->http_response_code));
			endif;
		endif;
		?>
		<div class="wrap nosubsub">
			<h2>Constant Contact Contact List Add</h2>
			
			<form name="addlist" id="addlist" method="post" action="">
			<input type="hidden" name="add" value="1" />
			<div id="poststuff" class="metabox-holder">
			<div id="post-body">
			<div id="post-body-content">
			
			<div id="linkadvanceddiv" class="postbox " >
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class='hndle'><span>Options</span></h3>
			<div class="inside">
			
			<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
				<tr>
					<th valign="top"  scope="row"><label for="link_image">List Name</label></th>
					<td>
						<input type="text" name="list_name" value="<?php echo $list_name; ?>" size="50" />
					</td>
				</tr>
				<tr>
					<th valign="top"  scope="row"><label for="link_image">Sort Order</label></th>
					<td>
						<input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="10" />
					</td>
				</tr>
			</table>
			</div>
			</div>
			</div>
			
			<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="<?php _e('Create List') ?>" />
			</p>
			
			</div>
			</div>
			</form>

		</div>
		
		<?php
	
		
	elseif(isset($_REQUEST['edit'])):
		// edit list
		
		$id = (int) $_REQUEST['edit'];
		$list = $cc->get_list($id);
		
		if(!$list):
			return '<p>Contact List Not Found</p></div>';
		endif;
		
		$list_name = $list['Name'];
		$sort_order = $list['SortOrder'];
			
		if(isset($_POST['edit'])):
			$list_name = $_POST['list_name'];
			$sort_order = $_POST['sort_order'];
			
			$status = $cc->update_list($id, $list_name, 'false', $sort_order);
			
			if($status):
				_e('The contact list has been edited - <a href="?page=constant-contact-lists">OK</a>');
			else:
				_e('Failed to edit contact list: ' .  constant_contact_last_error($cc->http_response_code));
			endif;
		endif;
		?>
		<div class="wrap nosubsub">
			<h2>Constant Contact Contact List Edit</h2>
			
			<form name="editlist" id="editlist" method="post" action="">
			<input type="hidden" name="edit" value="<?php echo $id; ?>" />
			<div id="poststuff" class="metabox-holder">
			<div id="post-body">
			<div id="post-body-content">
			
			<div id="linkadvanceddiv" class="postbox " >
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class='hndle'><span>Options</span></h3>
			<div class="inside">
			
			<table class="form-table" style="width: 100%;" cellspacing="2" cellpadding="5">
				<tr>
					<th valign="top"  scope="row"><label for="link_image">List Name</label></th>
					<td>
						<input type="text" name="list_name" value="<?php echo $list_name; ?>" size="50" />
					</td>
				</tr>
				<tr>
					<th valign="top"  scope="row"><label for="link_image">Sort Order</label></th>
					<td>
						<input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="10" />
					</td>
				</tr>
			</table>
			</div>
			</div>
			</div>
			
			<p class="submit">
			<input type="submit" name="submit" class="button-primary" value="<?php _e('Save List') ?>" />
			</p>
			
			</div>
			</div>
			</form>

		</div>
		
		<?php
	
	else:
		// view all lists
		$_lists = $cc->get_all_lists();
			
		if($_lists):
		foreach($_lists as $k => $v):
			$lists[$v['id']] = $v;
		endforeach;
		endif;
		
		if(!$_lists):
			return '<p>No Contact Lists Found</p></div>';
		endif;
		
		// display all lists
		?>

		<div class="wrap nosubsub">
			<h2>Constant Contact Contact Lists</h2>
		</div>
		
		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
		<th scope="col" id="name" class="manage-column column-name" style="">ID</th>
		<th scope="col" id="url" class="manage-column column-name" style="">Name</th>
		<th scope="col" id="visible" class="manage-column column-name" style="" colspan="2">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
	
		<?php
			foreach($lists as $id => $v):
			?>
			<tr id="link-2" valign="middle"  class="alternate">
				<td class="column-name">
					<?php echo $v['id']; ?>
				</td>
				<td class="column-name">
					<?php echo $v['Name']; ?>
				</td>
				<td class="column-name">
				<a href="<?php echo $_SERVER['REQUEST_URI']?>&edit=<?php echo $v['id']; ?>">Edit</a>
				</td>
				<td class="column-name">
				<a onclick="return confirm('Really delete this contact list?');" href="<?php echo $_SERVER['REQUEST_URI']?>&delete=<?php echo $v['id']; ?>">Delete</a>
				</td>
			</tr>
			<?php
			endforeach;
		?>
		</tbody>
		</table>
		<p><a href="<?php echo $_SERVER['REQUEST_URI']?>&add">Add New Contact List</a></p>
		<?php
	endif;
	
}
?>