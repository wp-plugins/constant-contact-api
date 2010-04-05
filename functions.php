<?php // $Id$

	// load the custom widgets
	function constant_contact_load_widgets()
	{
		register_widget( 'constant_contact_api_widget' );
	}

	// Format field mappings into array
	function constant_contact_build_field_mappings()
	{
		if(isset($GLOBALS['cc_extra_field_mappings'])):
			return $GLOBALS['cc_extra_field_mappings'];
		endif;
		
		$mappings = get_option('cc_extra_field_mappings');
		$field_mappings = explode(',', $mappings);
		
		$GLOBALS['cc_extra_field_mappings'] = array();
		
		if($field_mappings):
		foreach($field_mappings as $mapping):
			$bits = explode(':', $mapping);
			
			if(is_array($bits) && isset($bits[0], $bits[1])):
				$GLOBALS['cc_extra_field_mappings'][trim($bits[0])] = trim($bits[1]);
			endif;
		endforeach;
		endif;
		
		return $GLOBALS['cc_extra_field_mappings'];
	}
	
	
	// This function determines what the last error was and returns a friendly error message
	function constant_contact_last_error($status_code = 0)
	{
		$last_error = false;
		$status_code = intval($status_code);
		
		if(!$status_code):
			return $last_error;
		endif;
		
		$last_error = 'Sorry there was a problem processing your request, the error given was: ';
		
		switch($status_code):
			case 400: /* Invalid Request */
				$last_error .= 'Invalid Request';
			break;
			case 401: /* Unauthorized */
				$last_error .= 'Unauthorized';
			break;
			case 404: /* Page Not Found */
				$last_error .= 'Page Not Found';
			break;
			case 409: /* Conflict */
				$last_error .= 'Conflict';
			break;
			case 415: /* Unsupported Media Type */
				$last_error .= 'Unsupported Media Type';
			break;
			case 500: /* Internal Server Error */
				$last_error .= 'Internal Server Error';
			break;
			default: /* Unknown Error */
				$last_error .= 'Unknown Error';
			break;
		endswitch;
		
		return $last_error;
	}

	
	// Used in many functions throughout to create an object of class.cc.php
	function constant_contact_create_object()
	{
		$username = get_option('cc_username');
		$password =  get_option('cc_password');
					  
		require_once CC_FILE_PATH . 'class.cc.php';
		$cc = new cc($username, $password);
		
		if(!$username || !$password):
			// issues an error using wp system
			return false;
		endif;
		
		if(is_object($cc) && $cc->get_service_description()) {
			// we have successfully connected
			return $cc;
		} elseif($cc->http_response_code) {
			// oops, problem occured and we have an error code
			$error = $cc->http_get_response_code_error($cc->http_response_code);
			
			// if we get an unauthorized 401 error code reset the username and password
			// if we don't do this the CC account will be temporarily blocked eventually
			if(intval($cc->http_response_code) === 401):
				// Leave the wrong info there, but show the error message // ZK - 1.1
				update_option('cc_username', '');
				update_option('cc_password', ''); 
				if(is_admin() && !isset($_POST['error_displayed'])) { // error_diplayed for showing only once, it was showing twice
					$_POST['error_displayed'] = true;
					echo "<div id='constant-contact-warning' class='error'>
						<p><strong>$error</strong></p>
					</div>";
				}
				
			endif;
			
		} // if http_response_code
		
		return false;
	}

?>