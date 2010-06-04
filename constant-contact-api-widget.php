<?php // $Id$
/**
 * constant_contact_api_widget Class
 */

/*
Version 1.1
Changes made by Zack Katz; katzwebdesign on March 27, 2010
	Replaced $_SESSION with $GLOBALS in case servers have register_globals issues, which mine was having
	Converted trim() to tempty()
	Converted errors to <LI>s
	Converted errors to array for <LABEL>ing
	Used add_query_args for ?cc_success on referral page instead of referring to home page
	Added referral URL input in form using urlencode($this->curPageURL()) to generate current page
	Added 
	  apply_filters('constant_contact_form', $output); to widget output
	  apply_filters('constant_contact_form_success', $success);
	  apply_filters('constant_contact_form_description', $description);
	  apply_filters('constant_contact_form_errors', $errors);
	  apply_filters('constant_contact_form_submit', $submit_button);
	Converted widget code from echo to $output .=

Version 1.1.0.1
Changes made by Zack Katz; katzwebdesign on April 5, 2010
	Removed <br /> before widget form
	Fixed widget description and title display issues by renaming variables from `$title` to `$widget_title` and `$description` to `$widget_description`.

Version 1.1.1
Changes made by Zack Katz; katzwebdesign on May 27, 2010
	Added `cc_widget_lists_array` option to store Constant Contact lists, so that the API doesn't need to be called every page load. Now, API is only called when the plugin settings are saved.
	Wrapped the List Selection Title for the multi-select form element in a `label` tag, and removed line break.

Version 1.1.2
Changes made by Zack Katz; katzwebdesign on June 4, 2010
	Added is_array() to `if(is_array($auto_lists) && in_array($list_id, $auto_lists)):`
*/


class constant_contact_api_widget extends WP_Widget {

    /** constructor */
    function constant_contact_api_widget()
	{
		$options = array(
			'description' => 'Displays a Constant Contact signup form to your visitors',
			'classname' => 'constant-contact-signup',
		);
        parent::WP_Widget(false, $name = 'Constant Contact Signup', $options);	
    }
	
	

   /** @see WP_Widget::widget */
    function widget($args = array(), $instance = array())
	{
		$output = '';
		$errors = false;
		if(isset($GLOBALS['cc_errors'])):
			$errors = $GLOBALS['cc_errors'];
			unset($GLOBALS['cc_errors']);
		endif;
		
		$cc_lists = get_option('cc_widget_lists');
		$cc_lists_array = get_option('cc_widget_lists_array');
		$exclude_lists = get_option('cc_widget_exclude_lists');
		$exclude_lists = (!is_array($exclude_lists)) ? array() : $exclude_lists;
		
		$cc = constant_contact_create_object();
			
		if(!is_object($cc)):
			return;
		endif;
		
		// If the list has already been set, don't ask for it again.
		// This saves bandwidth
		if($cc_lists_array){
			$lists = unserialize($cc_lists_array);
		} else if($cc_lists) {
			// show only the lists they have selected
			$new_lists = array();
			foreach($cc_lists as $id):
				if(!in_array($id, $exclude_lists)):
					$new_lists[$id] = $cc->get_list($id);
				endif;
			endforeach;
			$lists = $new_lists;
			update_option('cc_widget_lists_array', maybe_serialize($lists));
		} else {
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
			update_option('cc_widget_lists_array', maybe_serialize($lists));
		}
		
        $widget_title = apply_filters('widget_title', get_option('cc_signup_widget_title'));
		$widget_description = get_option('cc_signup_widget_description');
        extract( $args );
        ?>
			<?php $output .= (isset($before_widget)) ? $before_widget : ''; ?>
			<?php $output .= (isset($before_title, $after_title)) ? $before_title : '<h2>'; ?>
			<?php $output .= (isset($widget_title)) ? $widget_title : ''; ?>
			<?php $output .= (isset($after_title, $before_title)) ? $after_title : '</h2>'; ?>
			
			<?php
			if(!empty($errors)):
				$error_output = '';
				$error_output .= '<div id="constant-contact-signup-errors">';
				$error_output .= '<ul>';
				foreach ($errors as $e) { 
					if(is_array($e)) { $error_output .= '<li><label for="'.$e[1].'">'.$e[0].'</label></li>'; } 
					else { $error_output .= '<li>'.$e.'</li>'; }
				}
				$error_output .= '</ul>';
				$error_output .= '</div>';
				$output .= apply_filters('constant_contact_form_errors', $error_output);
			elseif(isset($_GET['cc_success'])):
				$success = '<p>Success, you have been subscribed.</p>';
				$output .= apply_filters('constant_contact_form_success', $success);
			elseif($widget_description):
				$widget_description = wpautop($widget_description);
				$output .= apply_filters('constant_contact_form_description', $widget_description);
			endif;
			$output .=
			'<form action="'.$this->curPageURL().'" method="post" id="constant-contact-signup">';
			
			if(get_option('cc_widget_show_firstname')):
				$output .='
					<label for="cc_firstname">First Name:</label>
					<div class="input-text-wrap">
						<input type="text" name="cc_firstname" id="cc_firstname" value="'; 
						$output .= (isset($_POST['cc_firstname'])) ? htmlentities($_POST['cc_firstname']) : '';
						$output .= '" />
					</div>';
				endif;
				
				if(get_option('cc_widget_show_lastname')):
				$output .='
					<label for="cc_lastname">Last Name:</label>
					<div class="input-text-wrap">
						<input type="text" name="cc_lastname" id="cc_lastname" value="';
						$output .= (isset($_POST['cc_lastname'])) ? htmlentities($_POST['cc_lastname']) : '';
						$output .= '" />
					</div>';
				endif;
				
				$output .= '
				<label for="cc_email">Email:</label>
				<div class="input-text-wrap">
					<input type="text" name="cc_email" id="cc_email" value="';
					$output .= (isset($_POST['cc_email'])) ? htmlentities($_POST['cc_email']) : '';
				$output .= '" />
				</div>';
				
				if(get_option('cc_widget_show_list_selection')):
					if(get_option('cc_widget_list_selection_format') == 'select'):

					$output .= '<label for="cc_newsletter_select">'.get_option('cc_widget_list_selection_title') .'</label>
					<div class="input-text-wrap">
					<select name="cc_newsletter[]" id="cc_newsletter_select"  multiple size="5">';
						if($lists):
						foreach($lists as $k => $v):
							if(isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])):
								$output .=  '<option selected value="'.$v['id'].'">'.$v['Name'].'</option>';
							else:
								$output .=  '<option value="'.$v['id'].'">'.$v['Name'].'</option>';
							endif;
						endforeach;
						endif;
					$output .= '
					</select>
					</div>';
					
					elseif($lists):
						$output .=  get_option('cc_widget_list_selection_title');
						$output .=  '<div class="input-text-wrap">';
						$output .=  '<ul>';
						foreach($lists as $k => $v):
							if(isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])):
								$output .=  '<li><label for="cc_newsletter-'.$v['id'].'"><input checked="checked" type="checkbox" name="cc_newsletter[]" id="cc_newsletter-'.$v['id'].'" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '</label></li>'; // ZK added label, ID, converted to <LI>
							else:
								$output .=  '<li><label for="cc_newsletter-'.$v['id'].'"><input type="checkbox" name="cc_newsletter[]" id="cc_newsletter-'.$v['id'].'" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '</label></li>'; // ZK added label, ID
							endif;
						endforeach;
						$output .=  '</ul>';
						$output .=  '</div>';
					endif;
				endif;
				$output .= '
				<div>
					<input type="hidden" id="cc_referral_url" name="cc_referral_url" value="'.urlencode($this->curPageURL()).'" />';
					$submit_button = '<input type="submit" name="constant-contact-signup-submit" value="Signup" class="button submit" />';
					$output .= apply_filters('constant_contact_form_submit', $submit_button);
					$output .= '
				</div>
			</form>';
			$output .= (isset($after_widget)) ? $after_widget : ''; 
			
			// Modify the output by calling add_filter('constant_contact_form', 'your_function');
			// Passes the output to the function, needs return $output coming from the function.
			$output = apply_filters('constant_contact_form', $output);
			
			echo $output;
    }
	
   /*
	* From http://www.webcheatsheet.com/PHP/get_current_page_url.php
	*/
	function curPageURL() {
		 $pageURL = 'http';
		 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		 $pageURL .= "://";
		 if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		 } else {
		  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		 }
		 return remove_query_arg('cc_success',$pageURL);
	}
	
    /** @see WP_Widget::form */
    function form($instance)
	{
	?>
		<p><a href="admin.php?page=constant-contact-settings#widget">Edit the widget settings</a></p>
	<?php
    }

} // class constant_contact_api_widget

	function tempty($val) { 
		$val = trim($val);
    	return empty($val) && $val !== 0; 
	}
	
    /** function used to handle submitting the widget */
	function constant_contact_submit_widget()
	{
		$errors = array();
		
		// proces the submitted form
		if(!isset($_POST['constant-contact-signup-submit'], $_POST['cc_email'])):
			return; false;
		endif;
		
		$email = strip_tags($_POST['cc_email']);
		$fields = array();
		
		if(get_option('cc_widget_show_firstname') && isset($_POST['cc_firstname'])):
			if(tempty($_POST['cc_firstname'])):
				$errors[] = array('Please enter your first name', 'cc_firstname');
			else:
				$fields['FirstName'] = strip_tags($_POST['cc_firstname']);
			endif;
		endif;
			
		if(get_option('cc_widget_show_lastname') && isset($_POST['cc_lastname'])):
			if(tempty($_POST['cc_lastname'])):
				$errors[] = array('Please enter your last name', 'cc_lastname');
			else:
				$fields['LastName'] = strip_tags($_POST['cc_lastname']);
			endif;
		endif;
			
		if(tempty($email)):
			$errors[] = array('Please enter your email', 'cc_email');
		elseif(!is_email($email)):
			$errors[] = array('Please enter a valid email address', 'cc_email');
		endif;
			
			
		// return errors if any exist
		if($errors):
			$GLOBALS['cc_errors'] = $errors;
			return;
		endif;
			
		// subscribe user, redirect to thank you page if set
		$auto_lists = get_option('cc_widget_lists');
		$show_selection = get_option('cc_widget_show_list_selection');
		$selection_format = get_option('cc_widget_list_selection_format');
		$redirect_to = get_option('cc_widget_redirect_url');
				
		$cc = constant_contact_create_object();
		if(!is_object($cc)):
			return;
		endif;
					
		if($show_selection):
			$lists = (isset($_POST['cc_newsletter'])) ? $_POST['cc_newsletter'] : array();
					
			if($lists):
				$newlists = array();
				foreach($lists as $list_id):
					$list = $cc->get_list($list_id);
					$newlists[$list_id] = $list['Name'];
				endforeach;
				$lists = $newlists;
			endif;
					
		elseif(!$show_selection):
			$_lists = $cc->get_all_lists();
								
			if($_lists):
			foreach($_lists as $k => $v):
				$_lists[$k] = $v['id'];
			endforeach;
			endif;
							
			$newlists = array();
			foreach($_lists as $list_id):
				if(is_array($auto_lists) && in_array($list_id, $auto_lists)): // 1.1.2 added is_array() check
					$list = $cc->get_list($list_id);
					$newlists[$list['id']] = $list['Name'];
				endif;
			endforeach;
			$lists = $newlists;
		else:
			$lists = array();
		endif;
		
		if(!count($lists)):
			$GLOBALS['cc_errors'][] = 'Please select at least 1 ' . get_option('cc_widget_list_selection_title');
			return;
		endif;
					
		$cc->set_action_type('contact'); /* important, tell CC that the contact made this action */
		$contact = $cc->query_contacts($email);
				
		$lists = array_keys($lists);
		
		
		if($contact):
			$contact = $cc->get_contact($contact['id']);
			$status = $cc->update_contact($contact['id'], $email, $lists, $fields);
		else:
			$status = $cc->create_contact($email, $lists, $fields);
		endif;
			  
		if(!$status):
			$GLOBALS['cc_errors'][] = 'Sorry there was a problem, please try again later';
			return;
		elseif($redirect_to):
			header("Location: $redirect_to");
			exit;
		else:
			$url = add_query_arg('cc_success', true, urldecode($_POST['cc_referral_url']));
			header("Location: " . $url );
			exit;
		endif;
		
		// return false so we display no errors when viewing the form
		// the script should not get this far
		return false;
	}
?>