<?php // $Id$
/**
 * This file contains actions for the user functions, for example:
 * 
 * register_form, register_post, show_user_profile, 
 * profile_update, delete_user and constant_contact_signup_widget
 */
	
	// deleting a user account
	function constant_contact_delete_user($user_id)
	{
		$user = get_userdata($user_id);
		
		if(is_object($user) && isset($user->user_email)):
			// unsubscribe user
			
			$cc = constant_contact_create_object();
			
			if(!is_object($cc)):
				return;
			endif;
			
			// find contact
			$contact = $cc->query_contacts($user->user_email);
									
			if($contact && $contact['Status'] == 'Active'):
				$cc->delete_contact($contact['id']);
			endif;
		endif;
	}
	

	// updating a user account
	function constant_contact_profile_update($user)
	{
		$email = get_user_option( 'user_email', $user );
		$subscribe_method = get_option('cc_register_page_method');
		
		if($subscribe_method == 'none'):
			return; /* disabled */
		endif;
		
		$selected_lists = array();
		
		$cc = constant_contact_create_object();
		if(!is_object($cc)):
			return;
		endif;
		
		if(isset($_POST['cc_newsletter'])):
			$lists = (is_array($_POST['cc_newsletter'])) ? $_POST['cc_newsletter'] : array();
			$fields = get_option('cc_extra_fields');
			$field_mappings = constant_contact_build_field_mappings();
		
			// get contact and selected lists
			$contact = $cc->query_contacts($email);
			
			if($subscribe_method == 'checkbox' && $_POST['cc_newsletter']):
				$lists = get_option('cc_lists');
			endif;
			
			// parse custom fields
			$extra_fields = array();
			if(is_array($fields)):
			foreach($fields as $field):
				$fieldname = str_replace(' ','', $field);
				if(isset($field_mappings[$fieldname]) && isset($_POST[$field_mappings[$fieldname]])):
					$extra_fields[$fieldname] = $_POST[$field_mappings[$fieldname]];
				endif;
			endforeach;
			endif;
			
			$cc->set_action_type('contact');
			
			if($contact):
				$status = $cc->update_contact($contact['id'], $email, $lists, $extra_fields);
			else:
				$status = $cc->create_contact($email, $lists, $extra_fields);
			endif;
			
			if(!$status):
				//echo constant_contact_last_error($cc->http_response_code);
				return;
			endif;
		else:
			$contact = $cc->query_contacts($email);
			$cc->set_action_type('contact');
			
			if($contact):
				$status = $cc->update_contact($contact['id'], $email, array());
			endif;
		endif;
	}
	
	// showing the user edit form
	function constant_contact_show_user_profile($user)
	{
		$register_page_method = get_option('cc_register_page_method');
		
		if($register_page_method == 'none'):
			return; /* disabled */
		endif;
		
		$cc_lists = get_option('cc_lists');
		$exclude_lists = get_option('cc_exclude_lists');
		$exclude_lists = (!is_array($exclude_lists)) ? array() : $exclude_lists;
		$cc_newsletter = array();
		
		$cc = constant_contact_create_object();
			
		if(!is_object($cc)):
			return;
		endif;
		
		$contact = $cc->query_contacts($user->data->user_email);
		
		if($cc_lists):
			// show only the lists they have selected
			$new_lists = array();
			foreach($cc_lists as $id):
				if(!in_array($id, $exclude_lists)):
					$new_lists[] = $cc->get_list($id);
				endif;
			endforeach;
			$lists = $new_lists;
		else:
			// show all lists and exclude any have may have selected
			$lists = $cc->get_all_lists();
			
			if($lists):
				$new_lists = array();
				foreach($lists as $k => $v):
					if(!in_array($v['id'], $exclude_lists)):
						$new_lists[] = $cc->get_list($v['id']);
					endif;
				endforeach;
				$lists = $new_lists;
			endif;
		endif;
		
		if($contact):
			$contact = $cc->get_contact($contact['id']);
			
			if($contact && $contact['Status'] == 'Active'):
				if($register_page_method == 'checkbox'):
					$cc_newsletter = 1;
				else:
					$cc_newsletter = $contact['lists'];
				endif;
			endif;
		endif;
		echo $cc_newsletter;
	?>
		<h3><?php echo get_option('cc_signup_title');?></h3>
		<p>
		<label style="display: block; margin-bottom: 5px;">
			<?php
			if($register_page_method == 'checkbox'):
				if($cc_newsletter):
					echo '<input checked="checked" type="checkbox" name="cc_newsletter" class="checkbox" value="1" />';
				else:
					echo '<input type="checkbox" name="cc_newsletter" class="checkbox" value="1" />';
				endif;
			elseif($register_page_method == 'lists'):
				if(get_option('cc_list_selection_format') == 'select'):
			?>
				<select name="cc_newsletter[]" multiple size="5">
					<?php
					if($lists):
					foreach($lists as $k => $v):
						if(in_array($v['id'], $cc_newsletter)):
							echo '<option selected value="'.$v['id'].'">'.$v['Name'].'</option>';
						else:
							echo '<option value="'.$v['id'].'">'.$v['Name'].'</option>';
						endif;
					endforeach;
					endif;
					?>
				</select>
			<?php
				elseif($lists):
				echo '<br />';
				foreach($lists as $k => $v):
					if(in_array($v['id'], $cc_newsletter)):
						echo '<input checked="checked" type="checkbox" name="cc_newsletter[]" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '<br />';
					else:
						echo '<input type="checkbox" name="cc_newsletter[]" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '<br />';
					endif;
				endforeach;
				endif;
			endif;
			?>
		</label>
		</p>
		<br />
	<?php
	}
	
	// registering a new user
	function constant_contact_register_post($login,$email,$errors)
	{
		if(get_option('cc_register_page_method') == 'none'):
			return; /* disabled */
		endif;
		
		$subscribe_method = get_option('cc_register_page_method');
		$selected_lists = array();
		$has_subscribed = false;
		
		if(get_option('cc_register_page_method') == 'checkbox'):
			if(isset($_POST['cc_newsletter']) && $_POST['cc_newsletter']):
				// subscribe or update the user to the lists admin have selected
				$has_subscribed = true;
			endif;
		else:
			if(isset($_POST['cc_newsletter']) && is_array($_POST['cc_newsletter']) && count($_POST['cc_newsletter']) > 0):
				// subscribe or update the user to the lists they have selected
				$has_subscribed = true;
			endif;
		endif;
		
		if($has_subscribed):
			$cc = constant_contact_create_object();
				
			if(!is_object($cc)):
				return;
			endif;
				
			$lists = $_POST['cc_newsletter'];
			$fields = get_option('cc_extra_fields');
			$field_mappings = constant_contact_build_field_mappings();
		
			// get contact and selected lists
			$contact = $cc->query_contacts($email);
			
			if($subscribe_method == 'checkbox'):
				$lists = get_option('cc_lists');
			endif;
			
			// parse custom fields
			$extra_fields = array();
			if(is_array($fields)):
			foreach($fields as $field):
				$fieldname = str_replace(' ','', $field);
				if(isset($field_mappings[$fieldname]) && isset($_POST[$field_mappings[$fieldname]])):
					$extra_fields[$fieldname] = $_POST[$field_mappings[$fieldname]];
				endif;
			endforeach;
			endif;
			
			if(!is_array($lists) || !count($lists)):
				$errors->add('cc_error',__('Please select a contact list'));
			endif;
			
			$cc->set_action_type('contact');
		
			if($contact):
				$status = $cc->update_contact($contact['id'], $email, $lists, $extra_fields);
			else:
				$status = $cc->create_contact($email, $lists, $extra_fields);
			endif;
			
			if(!$status):
				$errors->add('cc_error',constant_contact_last_error($cc->http_response_code));
			endif;
		endif;
		
	}
	
	// showing the user register form
	function constant_contact_register_form()
	{
		$register_page_method = get_option('cc_register_page_method');
		if($register_page_method == 'none'):
			return; /* disabled */
		endif;
		
		$cc_lists = get_option('cc_lists');
		$exclude_lists = get_option('cc_exclude_lists');
		$exclude_lists = (!is_array($exclude_lists)) ? array() : $exclude_lists;
		
		$cc = constant_contact_create_object();
			
		if(!is_object($cc)):
			return;
		endif;
		
		if($cc_lists):
			// show only the lists they have selected
			$new_lists = array();
			foreach($cc_lists as $id):
				if(!in_array($id, $exclude_lists)):
					$new_lists[$id] = $cc->get_list($id);
				endif;
			endforeach;
			$lists = $new_lists;
		else:
			// show all lists and exclude any have may have selected
			$lists = $cc->get_all_lists();
			
			$new_lists = array();
			if($lists):
				foreach($lists as $k => $v):
					if(!in_array($v['id'], $exclude_lists)):
						$new_lists[$v['id']] = $cc->get_list($v['id']);
					endif;
				endforeach;
			endif;
			$lists = $new_lists;
		endif;
					
					
		if(get_option('cc_default_opt_in') && $register_page_method == 'lists'):
			$_POST['cc_newsletter'] = array_keys($lists);
		endif;
	?>
		<p>
		<label style="display: block; margin-bottom: 5px;">
			<?php echo get_option('cc_signup_title');?><br />
			<?php
			if($register_page_method == 'checkbox'):
				if(isset($_POST['cc_newsletter']) && $_POST['cc_newsletter']):
					echo '<input checked="checked" type="checkbox" name="cc_newsletter" class="checkbox" value="1" />';
				else:
					echo '<input type="checkbox" name="cc_newsletter" class="checkbox" value="1" />';
				endif;
			elseif($register_page_method == 'lists'):
				if(get_option('cc_list_selection_format') == 'select'):
			?>
				<select name="cc_newsletter[]" multiple size="5">
					<?php
					if($lists):
					foreach($lists as $k => $v):
						if(isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])):
							echo '<option selected value="'.$v['id'].'">'.$v['Name'].'</option>';
						else:
							echo '<option value="'.$v['id'].'">'.$v['Name'].'</option>';
						endif;
					endforeach;
					endif;
					?>
				</select>
			<?php
				elseif($lists):
				echo '<br />';
				foreach($lists as $k => $v):
					if(isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])):
						echo '<input checked="checked" type="checkbox" name="cc_newsletter[]" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '<br />';
					else:
						echo '<input type="checkbox" name="cc_newsletter[]" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '<br />';
					endif;
				endforeach;
				endif;
			endif;
			?>
		</label>
		</p>
		<br />
	<?php
	}
?>