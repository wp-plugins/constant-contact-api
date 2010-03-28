<?php
	
// download activity file
function constant_contact_download_activity_file()
{
	$cc = constant_contact_create_object();
	if(!is_object($cc)):
		return '<p>Could not create constant contact ibject</p></div>';
	endif;
	
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
		
// activities
function constant_contact_activities()
{
	constant_contact_download_activity_file();
	
	$cc = constant_contact_create_object();
	if(!is_object($cc)):
		return '<p>Activities Not Available</p></div>';
	endif;
	
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
		</div>
		
		<?php	
		$html = '';
		$html .= '<table cellspacing="3" cellpadding="3" border="0">';
		
		$dateformat = 'jS F Y \- H:i:s';
		
		$html .= '<tr><td>ID</td><td>'.$activity['id'].'</td></tr>';
		$html .= '<tr><td>Type</td><td>'.$activity['Type'].'</td></tr>';
		$html .= '<tr><td>Status</td><td>'.$activity['Status'].'</td></tr>';
		$html .= '<tr><td>Errors</td><td>'.(isset($activity['Errors']) ? $activity['Errors'] : 'None').'</td></tr>';
		$html .= '<tr><td>Transactions</td><td>'.(isset($activity['TransactionCount']) ? $activity['TransactionCount'] : 'None').'</td></tr>';
		$html .= '<tr><td>Created</td><td>'.date($dateformat, $cc->convert_timestamp($activity['InsertTime'])).'</td></tr>';
		
		if(isset($activity['RunStartTime'], $activity['RunFinishTime'])):
			$html .= '<tr><td>Started</td><td>'.date($dateformat, $cc->convert_timestamp($activity['RunStartTime'])).'</td></tr>';
			$html .= '<tr><td>Finished</td><td>'.date($dateformat, $cc->convert_timestamp($activity['RunFinishTime'])).'</td></tr>';
			
			$runtime = $activity['RunFinishTime']-$activity['RunStartTime'];
			$html .= '<tr><td>Runtime</td><td>'.((!$runtime)?'Less than 1 second':"$runtime seconds").'</td></tr>';
		endif;
		
		$html .= '<tr><td>';
			
		$html .= '<a href="?page=constant-contact-activities">View Activities</a>';
		
		if(isset($activity['FileName'])):
			//$html .= '&nbsp; &asymp; &nbsp;<a href="?page=constant-contact-activities&download='.$activity['FileName'].'">Download File</a>';
		endif;
		$html .= '</td></tr>';
		$html .= '</table>';
		
		echo $html;
	
	else:
		$_activities = $cc->get_activities();
			
		if($_activities):
		foreach($_activities as $k => $v):
			$activities[$v['id']] = $v;
		endforeach;
		endif;
		
		if(!$_activities):
			return '<p>No Activities Found</p></div>';
		endif;
		
		// display all activities
		?>

		<div class="wrap nosubsub">
			<h2>Constant Contact Activities</h2>
		</div>
		
		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
		<th scope="col" id="name" class="manage-column column-name" style="">Created</th>
		<th scope="col" id="url" class="manage-column column-name" style="">Type</th>
		<th scope="col" id="categories" class="manage-column column-name" style="">Status</th>
		<th scope="col" id="rel" class="manage-column column-name" style="">Errors</th>
		<th scope="col" id="visible" class="manage-column column-name" style="">Transactions</th>
		<th scope="col" id="visible" class="manage-column column-name" style="">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
	
		<?php
			foreach($activities as $id => $v):
			?>
			<tr id="link-2" valign="middle"  class="alternate">
				<td class="column-name">
					<?php echo date('jS F Y \- H:i', (int)$cc->convert_timestamp($v['InsertTime'])); ?>
				</td>
				<td class="column-name">
					<?php echo $v['Type']; ?>
				</td>
				<td class="column-name">
					<?php echo $v['Status']; ?>
				</td>
				<td class="column-name">
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
		<?php
	endif;
	
}
?>