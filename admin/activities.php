<?php
	
add_action('load-constant-contact_page_constant-contact-activities', 'constant_contact_download_activity_file');
// download activity file
function constant_contact_download_activity_file()
{
	$cc = constant_contact_create_object();
	if(!is_object($cc)) {
		echo '<div id="message" class="error"><p><strong>Download failed:</strong> Could not create Constant Contact object.</p></div>'; 
	}
	
	if(isset($_GET['download'])):
		$filename = htmlentities($_GET['download']);
			
		$filext = strtolower(substr($filename, -4));
			
		if($filext == '.csv'):
			header('Content-type: text/csv');
		elseif($filext == '.txt'):
			header('Content-type: text/plain');
		else:
			header('Content-type: application/octet-stream');
		endif;
			
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		echo $cc->download_activity_file($filename);
		exit;
	
	endif;
}
		
/**
 * Activity log submenu page callback function
 * 
 * @global <type> $cc
 * @return <type> 
 */
function constant_contact_activities()
{
	global $cc;
	
	// Create the CC api object for use in this page. 
	constant_contact_create_object();

	$activities = array();
	if(isset($_GET['id'])):
		$id = htmlentities($_GET['id']);
		$activity = $cc->get_activity($id);
		
		if(!$activity):
			return '<p>Activity Not Found</p></div>';
		endif;
		
		
		// display single activity
		?>
		<div class="wrap nosubsub">
			<h2>Constant Contact Activity - <?php echo $id; ?></h2>
		
		<table class="form-table widefat" cellspacing="0">
		<?php	
		$html = '';
		
		$dateformat = 'jS F Y \- H:i:s';
		
		$html .= '<tr><th scope="row">ID</th><td>'.$activity['id'].'</td></tr>';
		$html .= '<tr><th scope="row">Type</th><td>'.$activity['Type'].'</td></tr>';
		$html .= '<tr><th scope="row">Status</th><td>'.$activity['Status'].'</td></tr>';
		$html .= '<tr><th scope="row">Errors</th><td>'.(isset($activity['Errors']) ? $activity['Errors'] : 'None').'</td></tr>';
		$html .= '<tr><th scope="row">Transactions</th><td>'.(isset($activity['TransactionCount']) ? $activity['TransactionCount'] : 'None').'</td></tr>';
		$html .= '<tr><th scope="row">Created</th><td>'.date($dateformat, $cc->convert_timestamp($activity['InsertTime'])).'</td></tr>';
		
		if(isset($activity['RunStartTime'], $activity['RunFinishTime'])):
			$html .= '<tr><th scope="row">Started</th><td>'.date($dateformat, $cc->convert_timestamp($activity['RunStartTime'])).'</td></tr>';
			$html .= '<tr><th scope="row">Finished</th><td>'.date($dateformat, $cc->convert_timestamp($activity['RunFinishTime'])).'</td></tr>';
			
			$runtime = $activity['RunFinishTime']-$activity['RunStartTime'];
			$html .= '<tr><th scope="row">Runtime</th><td>'.((!$runtime)?'Less than 1 second':"$runtime seconds").'</td></tr>';
		endif;
		$html .= '</table>';
		$html .= '<p class="submit"><a href="?page=constant-contact-activities" class="button-secondary">Return to Activity Log</a>';
		if(isset($activity['FileName'])):
			$html .= '&nbsp;<a href="?page=constant-contact-activities&download='.$activity['FileName'].'" target="_blank" class="preview button-primary">Download Generated File</a>';
		endif;
		$html .= '</p>';
		echo $html;
	
	else:
		
		$_activities = $cc->get_activities();
			
		if(!empty($_activities)):
		foreach($_activities as $k => $v):
			$activities[$v['id']] = $v;
		endforeach;
		endif;
		
		// display all activities
		?>

		<div class="wrap nosubsub">
			<h2>Constant Contact Activities</h2>
			<table class="form-table widefat" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name" style="">Created</th>
					<th scope="col" id="type" class="manage-column column-name" style="">Type</th>
					<th scope="col" id="status" class="manage-column column-name" style="">Status</th>
					<th scope="col" id="errors" class="manage-column column-name" style="">Errors</th>
					<th scope="col" id="transactions" class="manage-column column-name" style="">Transactions</th>
					<th scope="col" id="activity" class="manage-column column-name" style="">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			
			if(empty($_activities)) {
				echo '<tr><td colspan="6"><h3>No Activities Found</h3></td></tr></table>';
				return;
			}
				$alt = '';
				foreach($activities as $id => $v):
				if($alt == 'alt') { $alt = '';} else { $alt = 'alt'; }
				?>
				<tr id="link-2" valign="middle"  class="<?php echo $alt; ?>">
					<td class="column-date">
						<?php echo date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['InsertTime'])); ?>
					</td>
					<td class="column-name">
						<?php echo $v['Type']; ?>
					</td>
					<td class="column-title">
						<?php echo $v['Status']; ?>
					</td>
					<td class="column-id">
						<?php echo (isset($v['Errors']) ? $v['Errors'] : 'None'); ?>
					</td>
					<td class="column-name">
						<?php echo (isset($v['TransactionCount'])?$v['TransactionCount'] : 'None'); ?>
					</td>
					<td class="column-name">
						<a href="<?php echo $_SERVER['REQUEST_URI']?>&id=<?php echo $v['id']; ?>">View Activity</a>
					</td>
				</tr>
				<?php
				endforeach;
			?>
			</tbody>
			</table>
		</div>
	
		<?php
	endif;
	?></div><?php 
}
?>