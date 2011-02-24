<?php

// Retrofitted to make much more WordPressy 10/27/2010 by katzwebdesign

// lists
function constant_contact_lists()
{
	global $cc;

	// Create the CC api object for use in this page.
	constant_contact_create_object();

	$lists = array();
	
	if(isset($_GET['add'])):
		// add List
		
		$list_name = '';
		$sort_order = 99;
		
		?>
		<div class="wrap nosubsub">
			<h2>Constant Contact - Add New List</h2>
			
			<form name="addlist" id="addlist" method="post" action="<?php echo remove_query_arg(array('add', 'edit', 'delete', 'refresh_lists'));?>">
			<input type="hidden" name="add" value="1" />
			<div id="poststuff" class="metabox-holder">
			<div id="post-body">
			<div id="post-body-content">
			
			<div id="linkadvanceddiv" class="postbox " >
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class='hndle'><span>Options</span></h3>
			<div class="inside">
			
			<table class="form-table" cellspacing="0">
				<tr>
					<th valign="top"  scope="row"><p><label for="link_image"><span>List Name</span></label></p></th>
					<td>
						<input type="text" name="list_name" value="<?php echo $list_name; ?>" size="50" />
					</td>
				</tr>
				<tr>
					<th valign="top"  scope="row"><p><label for="link_image"><span>Sort Order</span></label></p></th>
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
				<a href="<?php echo remove_query_arg(array('add', 'edit', 'delete', 'refresh_lists'));?>" class="button-secondary">Cancel</a>
			</p>
			
			</div>
			</div>
			</form>

		</div>
		
		<?php
	
		
	elseif(isset($_GET['edit'])):
		// edit list
		
		$id = (int) $_GET['edit'];
		$list = $cc->get_list($id);
		
		if(!$list):
			return '<p>Contact List Not Found</p></div>';
		endif;
		
		$list_name = $list['Name'];
		$sort_order = $list['SortOrder'];
			
		?>
		<div class="wrap nosubsub">
			<h2>Constant Contact - Edit List</h2>
			
			<form name="editlist" id="editlist" method="post" action="<?php echo remove_query_arg(array('add', 'edit', 'delete', 'refresh_lists'));?>">
			<input type="hidden" name="edit" value="<?php echo $id; ?>" />
			<div id="poststuff" class="metabox-holder">
			<div id="post-body">
			<div id="post-body-content">
			
			<div id="linkadvanceddiv" class="postbox " >
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class='hndle'><span>Options</span></h3>
			<div class="inside">
			
			<table class="form-table" cellspacing="0">
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
			<a href="<?php echo remove_query_arg(array('add', 'edit', 'delete', 'refresh_lists'));?>" class="button-secondary">Cancel</a>
			</p>
			
			</div>
			</div>
			</form>

		</div>
		
		<?php
	
	else:
		$force = false;
		if(isset($_REQUEST['delete'])):
		// delete list		
			$id = (int) $_REQUEST['delete'];
			$list = $cc->get_list($id);
			
			if(!$list):
				_e('<div id="message" class="error"><p><strong>Failed to delete contact list:</strong> Contact List Not Found</p></div>');
			else:			
				$status = $cc->delete_list($id);
				
				if($status):
					_e('<div id="message" class="updated"><p>The contact list <strong>has been deleted</strong>.</p></div>');
				else:
					_e('<div id="message" class="error"><p><strong>Failed to delete contact list:</strong> ' .  constant_contact_last_error($cc->http_response_code).'</p></div>');
				endif;
			endif;
		endif;
		
		if(isset($_POST['edit'])):
			$list_name = $_POST['list_name'];
			$sort_order = (int)$_POST['sort_order'];
			$id = (int) $_POST['edit'];
			
			$status = $cc->update_list($id, $list_name, 'false', $sort_order);
			
			if($status):
				_e('<div id="message" class="updated"><p>The contact list '.$list_name.' <strong>has been edited</strong>.</p></div>');
			else:
				_e('<div id="message" class="error"><p>Failed to edit contact list: ' .  constant_contact_last_error($cc->http_response_code).'</p></div>');
			endif;
		endif;
		
		if(isset($_POST['add'])):
			$list_name = $_POST['list_name'];
			$sort_order = $_POST['sort_order'];
			
			$status = $cc->create_list($list_name, 'false', $sort_order);
			
			if($status):
				_e('<div id="message" class="updated"><p>The contact list '.$list_name.' <strong>has been created</strong>.</p></div>');
			else:
				_e('<div id="message" class="error"><p><strong>Failed to create contact list:</strong> ' .  constant_contact_last_error($cc->http_response_code).'</p></div>');
			endif;
		endif;
		
		// If you've changed your lists, let's get rid of the cached version.
		if(isset($_POST['add']) || isset($_POST['edit']) || isset($_POST['delete']) || isset($_GET['refresh_lists'])) { $force = true; }
		
		// view all lists
		$_lists = constant_contact_get_lists($force);
			
		if($_lists):
		foreach($_lists as $k => $v):
			$lists[$v['id']] = $v;
		endforeach;
		endif;
		
		if(!$_lists):
			return '<p>No Contact Lists Found</p>';
		endif;
		
		// display all lists
		?>

		<div class="wrap nosubsub">
			<h2>Constant Contact - Lists</h2>

			<p class="alignright"><label class="howto" for="refresh_lists"><span>Are the displayed lists inaccurate?</span> <a href="<?php echo add_query_arg('refresh_lists', true, remove_query_arg(array('add', 'edit', 'delete', 'refresh_lists'))); ?>" class="button-secondary action" id="refresh_lists">Refresh Lists</a></label></p>

			<table class="form-table widefat" cellspacing="0">
				<thead>
					<tr>
						<th scope="col" id="name" class="manage-column column-id">ID</th>
						<th scope="col" id="url" class="manage-column column-name">Name</th>
						<th scope="col" id="sort-order" class="manage-column column-id">Sort Order</th>
						<th scope="col" id="visible" class="manage-column column-date">Edit</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$alt ='';
					foreach($lists as $id => $v):
					if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
					?>
					<tr class="<?php echo $alt; ?>">
						<td class="column-id">
							<?php echo $v['id']; ?>
						</td>
						<td class="column-title">
							<?php echo $v['Name']; ?>
						</td>
						<td class="column-id">
							<?php echo $v['SortOrder']; ?>
						</td>
						<td class="column-date">
							<a href="<?php echo add_query_arg(array('edit'=>$v['id']), remove_query_arg(array('add', 'edit', 'delete', 'refresh_lists'))); ?>">Edit</a> |	<a onclick="return confirm('Really delete this contact list?');" href="<?php echo add_query_arg(array('delete'=>$v['id']), remove_query_arg(array('add', 'edit', 'delete', 'refresh_lists'))); ?>">Delete</a>
						</td>
					</tr>
					<?php
					endforeach;
				?>
				</tbody>
			</table>
			<p class="submit"><a href="<?php echo add_query_arg('add', true, remove_query_arg(array('add', 'edit', 'delete', 'refresh_lists'))); ?>" class="button-primary">Add New List</a></p>
		</div>
		<?php
	endif;
	
}
?>