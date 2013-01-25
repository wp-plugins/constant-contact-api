<?php

// campaigns
function constant_contact_campaigns()
{
	global $cc;

	if(!constant_contact_create_object()) { echo '<div id="message" class="error"><p>Campaigns Not Available. Check your '.admin_url('admin.php?page=constant-contact-api').' API settings.</p></div>'; return; }

	$campaigns = array();

	if(isset($_GET['add'])) {

		echo __('This feature is not yet ready.', 'constant-contact-api');
		return;

		if(isset($_POST['add_campaign']) && wp_verify_nonce($_POST['add_campaign'],'cc_campaign')) {

			if(!empty($_POST['post_id'])) {

				$post = get_post($_POST['post_id']);

				$content = apply_filters('the_content', $post->post_content);
				$content = str_replace(']]>', ']]&gt;', $content);

				$id = $cc->create_campaign($post->post_title.rand(0,1000), $post->post_title, '', htmlentities2($post->post_content) , strip_tags($post->post_content), $contact_lists = array(),array(), 'HTML');
				die($id);
			} else {
				$errors['id'] = __('Please select a post.', 'constant-contact-api');
			}
		}

		if(!empty($errors)) {
			echo '
			<div class="error wrap">
				<p><strong>'.__('The email was not created because:', 'constant-contact-api').'</strong></p>
				<ul class="ul-disc">';
			foreach($errors as $error) {
				echo '
					<li>'.$error.'</li>';
			}
			echo '
				</ul>
			</div>';
		}
	?>
		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> <a href="<?php echo remove_query_arg(array('add')); ?>">Campaigns</a> &gt; <?php _e('Add New Campaign', 'constant-contact-api'); ?></h2>

			<p><?php _e('Select a post, then submit the form. When the form is submitted, the content of the post will be sent to Constant Contact as a new Email. You can continue editing the email in Constant Contact.', 'constant-contact-api'); ?></p>

			<h3>Select a Post:</h3>
			<form name="editcontact" id="editcontact" method="post" action="<?php echo remove_query_arg(array('delete', 'refresh'));?>">
	<?php
					$posts = get_posts();

		$listhtml = '<select name="post_id">
			<option value="">Select a Post</option>';
		foreach($posts as $post) {
			$listhtml .= '<option value="'.$post->ID.'">'.$post->post_title.'</option>';
		}
		$listhtml .= '</select>';

		$lists = constant_contact_get_lists();
		$alt = '';

		$listhtml .= '<h3>'.sprintf(__('Select %s'), _n('List:', 'Lists:', sizeof($lists))).'</h3>';

		if(!empty($lists)) {
			foreach($lists as $key => $details) {
				$listhtml .= '
					<li>
						<label for="cc_lists'.$details['id'].'">
								<input name="lists[]" type="checkbox" value="'.$details['id'].'" '. $list_checked .' id="cc_lists'.$details['id'].'" /> ' . $details['Name'] . '
						</label>
					</li>';
			}
		}
		echo $listhtml;
?>
				<p class="submit">
					<?php wp_nonce_field( 'cc_campaign', 'add_campaign'); ?>
					<input type="submit" value="Add Campaign" class="button-primary" /> <a href="<?php echo remove_query_arg('add'); ?>" class="button-secondary alignright"><?php _e('Cancel &amp; Return to Contacts Page', 'constant-contact-api'); ?></a>
				</p>
			</form>
<?php
	} else if (isset($_GET['id'])) {
		$id = htmlentities($_GET['id']);
		$campaign = $cc->get_campaign($id);

		if(!$campaign) {
			echo '<p>Campaign Not Found</p></div>';
		};


		// display single activity
		?>
		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Campaigns - <?php echo $id; ?></h2>
			<?php constant_contact_admin_refresh(); ?>

			<h3><?php _e('Campaign Stats:', 'constant-contact-api'); ?></h3>
			<table class="widefat form-table" cellspacing="0">
				<thead>
					<th scope="col" class="column-name"><?php _e('Sent', 'constant-contact-api'); ?></th>
					<th scope="col" class="column-title"><?php _e('Opens', 'constant-contact-api'); ?></th>
					<th scope="col" class="column-title"><?php _e('Clicks', 'constant-contact-api'); ?></th>
					<th scope="col" class="column-title"><?php _e('Bounces', 'constant-contact-api'); ?></th>
					<th scope="col" class="column-title"><?php _e('Forwards', 'constant-contact-api'); ?></th>
					<th scope="col" class="column-title"><?php _e('OptOuts', 'constant-contact-api'); ?></th>
					<th scope="col" class="column-title"><?php _e('Spam Reports', 'constant-contact-api'); ?></th>
				</thead>
				<tbody>
					<tr valign="top">
				<?php
				$html = '';
					$cols = array('Sent', 'Opens', 'Clicks', 'Bounces', 'Forwards', 'OptOuts', 'SpamReports');
					foreach($cols as $col) {
						$html .= '<td>'.htmlentities($campaign[$col]).'</td>';
					}
					echo $html;
				?>	</tr>
				</tbody>
			</table>
			<h3><?php _e('Campaign Details:', 'constant-contact-api'); ?></h3>
			<table class="widefat form-table" cellspacing="0">
				<thead>
					<th scope="col" class="column-name"><?php _e('Name', 'constant-contact-api'); ?></th>
					<th scope="col" class="column-title"><?php _e('Data', 'constant-contact-api'); ?></th>
				</thead>
				<tbody>
				<?php
				$html = $alt = '';
					foreach($campaign as $id => $v) {
						if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
						if(!is_array($v)) {
							$id = preg_replace('/([A-Z])/',' $1', $id);
							$html .= '<tr class="'.$alt.'"><th scope="row" valign="top" class="column-name">'.$id.'</th><td>'.htmlentities($v).'</td></tr>';
						}
					}
					echo $html;
				?>
				</tbody>
			</table>
			<p class="submit"><a href="<?php echo admin_url('admin.php?page=constant-contact-campaigns'); ?>" class="button-primary"><?php _e('Return to Campaigns', 'constant-contact-api'); ?></a></p>
		</div>
	<?php
	} else {
		$_campaigns = $cc->get_campaigns();
		if($_campaigns) {
			foreach($_campaigns as $k => $v) {
				$campaigns[$v['id']] = $v;
			}
		}

		// display all campaigns
		?>

		<div class="wrap nosubsub">
			<h2 class="cc_logo"><a class="cc_logo" href="<?php echo admin_url('admin.php?page=constant-contact-api'); ?>">Constant Contact Plugin &gt;</a> Campaigns<?php /* <a href="<?php echo add_query_arg(array('add' => 1), remove_query_arg('refresh')); ?>" class="button add-new-h2" title="Add New Contact">Add New</a> */ ?></h2>
			<?php constant_contact_admin_refresh(); ?>

		<?php if(!$_campaigns) { ?>
			<div id="message" class="updated"><p><?php _e('No Campaigns Found', 'constant-contact-api'); ?></p></div>
		<?php
			return;
		}
		?>
		<table class="widefat fixed ctct_table" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Name', 'constant-contact-api'); ?></th>
					<th scope="col" id="date" class="manage-column column-name" style=""><?php _e('Type', 'constant-contact-api'); ?></th>
					<th scope="col" id="status" class="manage-column column-name" style=""><?php _e('Last Edited', 'constant-contact-api'); ?></th>
					<th scope="col" id="view" class="manage-column column-name" style="">&nbsp;</th>
				</tr>
			</thead>
			<tbody>

		<?php
			$alt = $html = '';
			foreach($campaigns as $id => $v) {
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				$html .= '
				<tr class="'.$alt.'">
					<td class="column-name">'.htmlentities($v['Name']).'</td>
					<td class="column-name">'.htmlentities($v['Status']).'</td>
					<td class="column-name">'.date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['Date'])).'</td>
					<td class="column-name">
						<a href="https://ui.constantcontact.com/rnavmap/evaluate.rnav/?activepage=ecampaign.view&pageName=ecampaign.view&agent.uid='.$v['id'].'&action=edit" class="button button-secondary" target="_blank" title="Edit '.esc_html('"'.$v['Name'].'"').' on ConstantContact.com">Edit Campaign Externally</a> <span class="hide-if-js">|</span> <a href="'.admin_url('admin.php?page=constant-contact-campaigns&id='.$v['id']).'" class="button button-secondary">Campaign Details</a>
					</td>
				</tr>';
			}
			echo $html;
		?>
			</tbody>
		</table>
		<?php
	}

}
?>