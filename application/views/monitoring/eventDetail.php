<div class="row-fluid">
	<?php 
		if(isset($eventInfo)) {
	?>
	<table class="table-no-border table vertical-table">
		<tr>
			<td><span class="thead">Subject</span></td>
			<td><?php echo $eventInfo['Subject']; ?></td>
		</tr>
		<tr>
			<td><span class="thead">Server</span></td>
			<td><?php echo $eventInfo['ExternalServer']; ?></td>
		</tr>
		<tr>
			<td><span class="thead">Sql Script</span></td>
			<td><pre class="long-script condensed"><?php echo $eventInfo['QuerySQL']; ?></pre></td>
		</tr>
		<tr>
			<td><span class="thead">Satus</span></td>
			<td><?php echo $eventInfo['isActive']?'<span class="label label-success">Active</span>':'<span class="label label-warn">Deactive</span>'; ?></td>
		</tr>
		<tr>
			<td><span class="thead">History</span></td>
			<td>
				<div class="panel-group" id="logHistory">
				<?php
					if(count($eventLog)>0)
					{
						foreach ($eventLog as $item) {
							$logPanel =	'<div class="panel panel-default">'.
											'<div class="panel-heading accordion-toggle" data-toggle="collapse" data-parent="#logHistory"  href="#logid-'.$item['LogId'].'">'.
												$item['LogTitle'].
											'</div>'.
											'<div id="logid-'.$item['LogId'].'" class="panel-collapse collapse">'.
								  				'<div class="panel-body">'.
												$item['LogMessage'].
								  				'</div>'.
							  				'</div>'.
						  				'</div>';
						  	echo $logPanel;
						}
					}
					else
					{
						echo 'no history yet.';
					}
				?>
				</div>
			</td>
		</tr>
	</table>
	<?php 
		}
		else
		{
			echo 'not available.';
		}
	?>
</div>