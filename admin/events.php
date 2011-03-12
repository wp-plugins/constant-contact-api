<?php
		
/**
 * event log submenu page callback function
 * 
 * @global <type> $cc
 * @return <type> 
 */
function constant_contact_events()
{
	global $cc;
	
	// Create the CC api object for use in this page. 
	constant_contact_create_object();

	$events = array();
	
	// Single registrant view
	if(isset($_GET['id']) && isset($_GET['registrant'])) {
		$id = htmlentities($_GET['id']);
		$regid = htmlentities($_GET['registrant']);
		$v = $cc->get_event_registrant($id, $regid);
		$event = $cc->get_event($id);
		extract($v);
		unset($BusinessInformation['Label']);
		unset($PersonalInformation['Label']);

		if(!empty($v) && current_user_can('list_users')) { 
		?>
		<div class="wrap nosubsub">
			<h2><a href="<?php echo admin_url('admin.php?page=constant-contact-events'); ?>">Events</a> &gt;<?php echo_if_not_empty($event['Title'],'',' <a href="'.admin_url('admin.php?page=constant-contact-events&id='.$event['id']).'">&ldquo;'.$event['Title'].'&rdquo;</a> &gt;') ?> Registrant: <?php echo "{$LastName}, {$FirstName}"; ?></h2>
		
			<h3>Registration Information:</h3>
			<table class="widefat form-table" cellspacing="0">
				<tbody>
					<?php
					$RegistrationInformation = array('Registration Status'=>$RegistrationStatus, 'Registration Date' => date('jS F Y \- H:i', (int)$cc->convert_timestamp($RegistrationDate)),'Guest Count'=>get_if_not_empty($GuestCount,1),'Payment Status'=>$PaymentStatus,'Order Amount'=>$OrderAmount,'Currency Type'=>$CurrencyType,'Payment Type' => $PaymentType);
					$reg = '';
					foreach($RegistrationInformation as $key => $v) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',$key);
						$reg .= '<tr><th scope="row" id="'.sanitize_title($key).'" class="manage-column column-name" style=""><span>'.$key.'</span></th><td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
					}
					
					if(!empty($Costs)) {
						$reg .= '<tr><th scope="row" id="costs" class="manage-column column-name" style=""><span>Summary of Costs</span></th><td><ul class="ul-disc">';
					
						foreach($Costs as $v) {
							extract($v);
							$reg .= "<li><strong>{$FeeType}</strong>: {$Count} Guest(s) x {$Rate} = {$Total}</li>";
						}
						$reg .= '</ul></td></tr>';
					}
					echo $reg;
					?>
				</tbody>
			</table>
			
			<h3>Personal Information:</h3>
			<table class="widefat form-table" cellspacing="0">
				<tbody>
					<?php
					$per = '';
					
					$OtherPersonalInformation['LastName'] = $LastName;
					$OtherPersonalInformation['FirstName'] = $FirstName;
					$OtherPersonalInformation['EmailAddress'] = get_if_not_empty($EmailAddress,'', "<a href='mailto:{$EmailAddress}'>{$EmailAddress}</a>");
					foreach($OtherPersonalInformation as $key => $v) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',str_replace('Address', 'Address ',$key));
						$per .= '<tr><th scope="row" id="'.sanitize_title($key).'" class="manage-column column-name" style=""><span>'.$key.'</span></th><td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
					}
					foreach($PersonalInformation as $key => $v) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',str_replace('Address', 'Address ',$key));
						$per .= '<tr><th scope="row" id="'.sanitize_title($key).'" class="manage-column column-name" style=""><span>'.$key.'</span></th><td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
					}
					echo $per;
					?>
				</tbody>
			</table>
			
			
			<h3>Business Information:</h3>
			<table class="widefat form-table" cellspacing="0">
				<tbody>
					<?php
					$bus = '';
					foreach($BusinessInformation as $key => $v) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',str_replace('Address', 'Address ',$key));
						$bus .= '<tr><th scope="row" id="'.sanitize_title($key).'" class="manage-column column-name" style=""><span>'.$key.'</span></th><td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
					}
					echo $bus;
					?>
				</tbody>
			</table>
			
			<?php if(!empty($CustomInformation1)) {?>
			<h3>Custom Information:</h3>
			<table class="widefat form-table" cellspacing="0">
				<tbody>
					<?php
					$cus = ''; $cusnum = 0;
					foreach($CustomInformation1['CustomField'] as $key => $v) {
						$cusnum++;
						if($key == 'Question') {
							$cus .= '<tr><th scope="row" id="'.sanitize_title($key.$cusnum).'" class="manage-column column-name" style=""><span>'.$v.'</span></th>';
						} elseif($key == 'Answers') {
							if(is_array($v)) {
								foreach($v as $li) {
									if(preg_match('/^20[0-9]{2}\-/', $li)) {
										$li = date('jS F Y \- H:i', (int)$cc->convert_timestamp($li));
									}
									$vA = '<li>'.$li.'</li>';
								}
								$v = "<ul>{$vA}</ul>";
							}
							$cus .= '<td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
						}
					}
					foreach($CustomInformation2['CustomField'] as $key => $v) {
						$cusnum++;
						if($key == 'Question') {
							$cus .= '<tr><th scope="row" id="'.sanitize_title($key.$cusnum).'" class="manage-column column-name" style=""><span>'.$v.'</span></th>';
						} elseif($key == 'Answers') {
							if(is_array($v)) {
								foreach($v as $li) {
									if(preg_match('/^20[0-9]{2}\-/', $li)) {
										$li = date('jS F Y \- H:i', (int)$cc->convert_timestamp($li));
									}
									$vA = '<li>'.$li.'</li>';
								}
								$v = "<ul>{$vA}</ul>";
							}
							$cus .= '<td>'.get_if_not_empty($v, '<span class="description">(Empty)</span>').'</td></tr>';
						}					}
					echo $cus;
					?>
				</tbody>
			</table>
	<?php	} ?>
		<p class="submit"><a href="<?php echo remove_query_arg(array('registrant', 'refresh')); ?>" class="button-primary">Return to Event</a> <a href="<?php echo add_query_arg('refresh', 'registrant'); ?>" class="button-secondary alignright" title="Registrant data is stored for 1 hour. Refresh data now.">Refresh Registrant</a></p>
		</div>
	<?php 
		}
	} elseif(isset($_GET['id'])) {
		$id = htmlentities($_GET['id']);
		$v = $cc->get_event($id);
		if((int)$cc->convert_timestamp($v['EndDate']) > time()) { $completed = false; }
		else { $completed = true; }
	?>
		<div class="wrap nosubsub">
			<h2><a href="<?php echo admin_url('admin.php?page=constant-contact-events'); ?>">Events</a> &gt; Event<?php echo_if_not_empty($v['Title'],'',': &ldquo;'.$v['Title'].'&rdquo;'); ?></h2>			
			<h3>Event Stats:</h3>
			<table class="widefat form-table" cellspacing="0">
				<thead>
					<th scope="col" class="column-name">Registered</th>
					<th scope="col" class="column-title"><?php if($completed) { echo 'Attended'; } else { echo 'Attending'; }?></th>
					<th scope="col" class="column-title">Cancelled</th>
				</thead>
				<tbody>
					<tr valign="top">
				<?php
				$html = '';
					$cols = array('Registered', 'AttendedCount', 'CancelledCount');
					foreach($cols as $col) {
						$html .= '<td>'.htmlentities($v["{$col}"]).'</td>';
					}
					echo $html;
				?>	</tr>
				</tbody>
			</table>
		<h3>Event Details:</h3>
		<table class="form-table widefat" cellspacing="0">
			<?php
			if(!$v) {
				echo '<tbody><tr><td><p>Event Not Found</p></td></tr></tbody></table><p class="submit"><a href="'.admin_url('admin.php?page=constant-contact-events').'" class="button-primary">Return to Events</a></p></div>';
				return;
			}
			$html = '';
			
			$dateformat = 'jS F Y \- H:i:s';
			
			$v['StartDate'] = (int)$cc->convert_timestamp($v['StartDate']);
			$v['EndDate'] = (int)$cc->convert_timestamp($v['EndDate']);
			
			?>
			<tbody>
				<tr><th scope="row" id="name" class="manage-column column-name" style="">Name</th><td><?php echo $v['Name']; ?></td></tr>
				<tr class="alt"><th scope="row" id="description" class="manage-column column-name" style="">Description</th><td><?php echo_if_not_empty($v['Description']); ?></td></tr>
				<tr><th scope="row" id="title" class="manage-column column-name" style="">Title</th><td><?php echo $v['Title']; ?></td></tr>
				<tr class="alt"><th scope="row" id="created" class="manage-column column-name" style="">Created</th><td><?php echo date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['CreatedDate'])); ?></td></tr>
				<tr><th scope="row" id="status" class="manage-column column-name" style="">Status</th><td><?php echo $v['Status']; ?></td></tr>
				<tr class="alt"><th scope="row" id="type" class="manage-column column-name" style="">Type</th><td><?php echo $v['EventType']; ?></td></tr>
				<tr><th scope="row" id="start" class="manage-column column-name" style="">Start</th><td><?php echo (!empty($v['StartDate']) ? date('jS F Y \- H:i', $v['StartDate']) : 'None'); ?></td></tr>
				<tr><th scope="row" id="end" class="manage-column column-name" style="">End</th><td><?php echo (!empty($v['EndDate']) ? date('jS F Y \- H:i', $v['EndDate']) : 'None'); ?></td></tr>
				<tr><th scope="row" id="registrationurl" class="manage-column column-name" style="">Registration URL</th><td><?php echo_if_not_empty($v['RegistrationURL'], '', '<a href="'.$v['RegistrationURL'].'">'.$v['RegistrationURL'].'</a>'); ?></td></tr>
				<tr class="alt"><th scope="row" id="location" class="manage-column column-name" style="">Location</th><td><?php echo constant_contact_create_location($v['EventLocation']); ?></td></tr>
<!--
				<tr><th scope="row" id="registered" class="manage-column column-name" style="">Registered</th><td><?php echo_if_not_empty($v['Registered'], 0); ?></td></tr>
				<tr class="alt"><th scope="row" id="attending" class="manage-column column-name" style=""><?php if($completed) { echo 'Attended'; } else { echo 'Attending'; }?></th><td><?php echo_if_not_empty($v['Attending'], 0); ?></td></tr>
				<tr><th scope="row" id="cancelled" class="manage-column column-name" style="">Cancelled</th><td><?php echo_if_not_empty($v['Cancelled'],0); ?></td></tr>
-->
		<?php
			$types = '';
			foreach($v['RegistrationTypes']['RegistrationType'] as $k => $type) {
				if($k == 'EventFees') { continue; }
				$k = preg_replace('/(?!^)[[:upper:]]/',' \0',$k);

				if(is_array($type)) {
					$list = '';
					foreach($type as $key => $t) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',$key);
						$list .= '<li><strong>'.$key.'</strong>'.$t.'</li>';
					}
					$type = '<ul>'.$list.'</ul>';
				} 

				$types .= '<li><strong>'.$k.'</strong>: '.$type.'</li>';
			}
			$types = '<ul>'.$types.'</ul>';
			
			if(!empty($v['RegistrationTypes']['RegistrationType']['EventFees'])) {
			$fees = '';
			foreach($v['RegistrationTypes']['RegistrationType']['EventFees']['EventFee'] as $k => $fee) {
				$k = preg_replace('/(?!^)[[:upper:]]/',' \0',$k);

				if(is_array($fee)) {
					$list = '';
					foreach($fee as $key => $t) {
						$key = preg_replace('/(?!^)[[:upper:]]/',' \0',$key);
						$list .= '<li><strong>'.$key.'</strong>: '.$t.'</li>';
					}
					$fee = '<ul>'.$list.'</ul>';
				} 

				$fees .= '<li>'.$fee.'</li>';
			}
				$fees = '<ol class="ol-decimal">'.$fees.'</ol>';
			} else {
				$fees = 'Free';
			}
		?>
			<tr><th scope="row" id="cancelled" class="manage-column column-name" style="">Registration Types</th><td><?php echo $types; ?></td></tr>
			<tr><th scope="row" id="cancelled" class="manage-column column-name" style="">Event Fees</th><td><?php echo $fees; ?></td></tr>
			</tbody>
		</table>
		<p class="submit"><a href="<?php echo admin_url('admin.php?page=constant-contact-events'); ?>" class="button-primary">Return to Events</a> <a href="<?php echo add_query_arg('refresh', 'event'); ?>" class="button-secondary alignright" title="Event data is stored for 1 hour. Refresh data now.">Refresh Event</a></p>
		
		<?php
		
		$_registrants = $cc->get_event_registrants($id);
#		print_r($_registrants);

		if(!empty($_registrants) && current_user_can('list_users')) { 
		?>
		<h3>Event Registrants:</h3>
		<table class="widefat form-table" cellspacing="0" id="registrants">
			<thead>
				<tr>
					<th scope="col" id="registrant_lastname" class="manage-column column-name" style="">Last Name</th>
					<th scope="col" id="registrant_firstname" class="manage-column column-name" style="">First Name</th>
					<th scope="col" id="registrant_email" class="manage-column column-name" style="">Email</th>
					<th scope="col" id="registrant_status" class="manage-column column-name" style="">Registration Status</th>
					<th scope="col" id="registrant_date" class="manage-column column-name" style="">Registration Date</th>
					<th scope="col" id="registrant_guestcount" class="manage-column column-name" style="">Guest Count</th>
					<th scope="col" id="registrant_paymentstatus" class="manage-column column-name" style="">Payment Status</th>
					<th scope="col" id="registrant_details" class="manage-column column-name" style="">Details</th>
			</thead>
			<tbody>
				<?php 
				$alt = '';
				foreach($_registrants as $v) { 
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				?>
				<tr <?php echo $alt; ?>>
					<td><?php echo $v['LastName']; ?></td>
					<td><?php echo $v['FirstName']; ?></td>
					<td><?php echo_if_not_empty($v['EmailAddress'],'', "<a href='mailto:{$v['EmailAddress']}'>{$v['EmailAddress']}</a>"); ?></td>
					<td><?php echo $v['RegistrationStatus']; ?></td>
					<td><?php echo_if_not_empty($v['RegistrationDate'], 'None', date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['RegistrationDate']))); ?></td>
					<td><?php echo_if_not_empty($v['GuestCount'],1); ?></td>
					<td><?php echo $v['PaymentStatus']; ?></td>
					<td><a href="<?php echo add_query_arg('registrant', $v['id'], remove_query_arg('refresh')); ?>">View Details</a></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<p class="submit"><a href="<?php echo add_query_arg('refresh', 'registrants').'#registrants'; ?>" class="button-secondary alignright" title="Event registrants data is stored for 1 hour. Refresh data now.">Refresh Registrants</a></p>
		<?php } ?>
	</div>
<?php	
	} else {
		
		$_events = $cc->get_events();
		
		if(!empty($_events)):
		foreach($_events as $k => $v):
			$events[$v['id']] = $v;
		endforeach;
		endif;
		// display all events
		?>

		<div class="wrap nosubsub">
			<h2>Constant Contact Events</h2>
			<?php if(empty($_events)) {?>
	
<style type="text/css">
#free_trial {
background: url(<?php echo CC_FILE_URL; ?>admin/images/btn_free_trial_green.png) no-repeat 0px 0px;
margin: 0px 5px 0px 0px;
width: 246px;
}
a#free_trial,
a#see_how {
	display:block;
	text-indent:-9999px;
	overflow:hidden;
	float:left;
	height: 80px;
}

a#free_trial:hover,
a#see_how:hover {
	background-position: 0px -102px;
}
#see_how {
	background: url(<?php echo CC_FILE_URL; ?>admin/images/btn_see_how_blue.png) no-repeat 0px 0px;
	margin: 0px 10px 0px 0px;
	width: 216px;
}
</style>

<div class="widefat">
<div class="wrap"  style="padding:10px;"><h2 class="clear">Did you know that Constant Contact offers <a href="http://conta.cc/hB5lnC" title="Learn more about Constant Contact Event Marketing">Event Marketing</a>?</h2>
<a id="see_how" href="http://conta.cc/eIt0gy" target="winHTML">See How it Works!</a>
<a id="free_trial" href="http://conta.cc/guwuYh">Start Your Free Trial</a>
<ul class="ul-disc clear">
	<li>Affordable, priced for small business, discount for nonprofits. <a href="http://conta.cc/guwuYh">Start for FREE!</a></li>
	<li>Easy-to-use tools and templates for online event registration and promotion</li>
	<li>Professional &#8212; you, and your events, look professional</li>
	<li>Secure credit card processing &#8212; collect event fees securely with PayPal processing</li>
	<li>Facebook, Twitter links make it easy to promote your events online</li>
	<li>Track and see results with detailed reports on invitations, payments, RSVP's, <a href="http://conta.cc/f62LG7">and more</a></li>
</ul></div>
</div>
<?php } ?>
			
			<table class="form-table widefat" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name" style="">Name</th>
<!-- 					<th scope="col" id="description" class="manage-column column-name" style="">Description</th> -->
					<th scope="col" id="title" class="manage-column column-name" style="">Title</th>
					<th scope="col" id="created" class="manage-column column-name" style="">Created</th>
					<th scope="col" id="status" class="manage-column column-name" style="">Status</th>
					<th scope="col" id="type" class="manage-column column-name" style="">Type</th>
					<th scope="col" id="start" class="manage-column column-name" style="">Start</th>
					<th scope="col" id="end" class="manage-column column-name" style="">End</th>

					<th scope="col" id="registered" class="manage-column column-name" style=""># Registered</th>
<!-- 					<th scope="col" id="attending" class="manage-column column-name" style="">Attending</th> -->
					<th scope="col" id="cancelled" class="manage-column column-name" style=""># Cancelled</th>
					<th scope="col" id="details" class="manage-column column-name" style="">Details</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(empty($_events)) {?>
				<tr><td colspan="6">
				<h3>No events found&hellip;</h3>
				</td></tr></table>
			<?php
				return;
			}
				$alt = '';
				foreach($events as $id => $v):
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				?>
				<tr id="link-2" valign="middle"  class="<?php echo $alt; ?>">
					<td class="column-name">
						<?php echo $v['Name']; ?>
					</td>
<!--
					<td class="column-name">
						<?php echo empty($v['Description']) ? '' : $v['Description']; ?>
					</td>
-->
					<td class="column-name">
						<?php echo $v['Title']; ?>
					</td>
					<td class="column-date">
						<?php echo date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['CreatedDate'])); ?>
					</td>
					<td class="column-title">
						<?php echo $v['Status']; ?>
					</td>
					<td class="column-name">
						<?php echo $v['EventType']; ?>
					</td>
					<td class="column-id">
						<?php echo (isset($v['StartDate']) ? date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['StartDate'])) : 'None'); ?>
					</td>
					<td class="column-name">
						<?php echo (isset($v['EndDate']) ? date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['EndDate'])) : 'None'); ?>
					</td>
					<td class="column-id">
						<?php echo_if_not_empty($v['Registered'],0); ?>
					</td>
					<td class="column-id">
						<?php echo_if_not_empty($v['CancelledCount'],0); ?>
					</td>
					<td class="column-name">
						<a href="<?php echo add_query_arg('id', $v['id'], remove_query_arg('refresh')); ?>">Event Details &amp; Registrants</a>
					</td>
				</tr>
				<?php
				endforeach;
			?>
			</tbody>
			</table>
			 <p class="submit"><a href="<?php echo add_query_arg('refresh', 'events'); ?>" class="button-secondary alignright" title="Events data is stored for 1 hour. Refresh data now.">Refresh Event List</a></p>
		</div>
	
		<?php
	}
	?><?php 
}
?>