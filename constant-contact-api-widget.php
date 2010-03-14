<?php // $Id$
/**
 * constant_contact_api_widget Class
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
		$errors = false;
		if(isset($_SESSION['cc_errors'])):
			$errors = $_SESSION['cc_errors'];
			unset($_SESSION['cc_errors']);
		endif;

		$cc_lists = get_option('cc_widget_lists');
		$exclude_lists = get_option('cc_widget_exclude_lists');
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

        $title = apply_filters('widget_title', get_option('cc_signup_widget_title'));
		$description = get_option('cc_signup_widget_description');
        extract( $args );
        ?>
			<?php echo (isset($before_widget)) ? $before_widget : ''; ?>
			<?php echo (isset($before_title, $after_title)) ? $before_title : '<h2>'; ?>
			<?php echo (isset($title)) ? $title : ''; ?>
			<?php echo (isset($after_title, $before_title)) ? $after_title : '</h2>'; ?>

			<?php
			if($errors):
				echo '<div id="constant-contact-signup-errors">';
				echo implode("<br />\n", $errors);
				echo '</div>';
			elseif(isset($_GET['cc_success'])):
				echo "<p>Success, you have been subscribed.</p>";
			elseif($description):
				echo "<p>$description</p>";
			endif;
			?>

			<br />
			<form action="<?php echo get_option('home'); ?>" method="post" id="constant-contact-signup">
				<?php
				if(get_option('cc_widget_show_firstname')):
				?>
					First Name:<br />
					<div class="input-text-wrap">
						<input type="text" name="cc_firstname" value="<?php echo (isset($_POST['cc_firstname'])) ? htmlentities($_POST['cc_firstname']) : ''?>" />
					</div>
				<?php
				endif;
				?>

				<?php
				if(get_option('cc_widget_show_lastname')):
				?>
					Last Name:<br />
					<div class="input-text-wrap">
						<input type="text" name="cc_lastname" value="<?php echo (isset($_POST['cc_lastname'])) ? htmlentities($_POST['cc_lastname']) : ''?>" />
					</div>
				<?php
				endif;
				?>

				Email:<br />
				<div class="input-text-wrap">
					<input type="text" name="cc_email" value="<?php echo (isset($_POST['cc_email'])) ? htmlentities($_POST['cc_email']) : ''?>" />
				</div>

				<?php
				if(get_option('cc_widget_show_list_selection')):
					if(get_option('cc_widget_list_selection_format') == 'select'):
				?>
					<?php echo get_option('cc_widget_list_selection_title'); ?><br />
					<div class="input-text-wrap">
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
					</div>
					<?php
					elseif($lists):
						echo get_option('cc_widget_list_selection_title');
						echo '<br /><div class="input-text-wrap">';
						foreach($lists as $k => $v):
							if(isset($_POST['cc_newsletter']) && in_array($v['id'], $_POST['cc_newsletter'])):
								echo '<input checked="checked" type="checkbox" name="cc_newsletter[]" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '<br />';
							else:
								echo '<input type="checkbox" name="cc_newsletter[]" class="checkbox" value="'.$v['id'].'" /> ' . $v['Name'] . '<br />';
							endif;
						endforeach;
						echo '</div>';
					endif;
					?>
				<?php
				endif;
				?>

				<br /><input type="submit" name="constant-contact-signup-submit" value="Signup" />
			</form>

			<?php echo (isset($after_widget)) ? $after_widget : ''; ?>
        <?php
    }

    /** @see WP_Widget::form */
    function form($instance)
	{
	?>
		<p><a href="admin.php?page=constant-contact-settings#widget">Edit the widget settings</a></p>
	<?php
    }

} // class constant_contact_api_widget



    /** function used to handle submitting the widget */
	function constant_contact_submit_widget()
	{
		$errors = false;

		// proces the submitted form
		if(!isset($_POST['constant-contact-signup-submit'], $_POST['cc_email'])):
			return; false;
		endif;

		$email = strip_tags($_POST['cc_email']);
		$fields = array();

		if(get_option('cc_widget_show_firstname') && isset($_POST['cc_firstname'])):
			if(trim($_POST['cc_firstname']) == ''):
				$errors[] = 'Please enter your first name';
			else:
				$fields['FirstName'] = strip_tags($_POST['cc_firstname']);
			endif;
		endif;

		if(get_option('cc_widget_show_lastname') && isset($_POST['cc_lastname'])):
			if(trim($_POST['cc_lastname']) == ''):
				$errors[] = 'Please enter your last name';
			else:
				$fields['LastName'] = strip_tags($_POST['cc_lastname']);
			endif;
		endif;

		if(trim($email) == ''):
			$errors[] = 'Please enter your email';
		elseif(!is_email($email)):
			$errors[] = 'Please enter a valid email address';
		endif;


		// return errors if any exist
		if($errors):
			$_SESSION['cc_errors'] = $errors;
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
				if(in_array($list_id, $auto_lists)):
					$list = $cc->get_list($list_id);
					$newlists[$list['id']] = $list['Name'];
				endif;
			endforeach;
			$lists = $newlists;
		else:
			$lists = array();
		endif;

		if(!count($lists)):
			$_SESSION['cc_errors'][] = 'Please select at least 1 ' . get_option('cc_widget_list_selection_title');
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
			$_SESSION['cc_errors'][] = 'Sorry there was a problem, please try again later';
			return;
		elseif($redirect_to):
			header("Location: $redirect_to");
			exit;
		else:
			header("Location: " . get_option('siteurl') . '?cc_success');
			exit;
		endif;

		// return false so we display no errors when viewing the form
		// the script should not get this far
		return false;
	}
?>