<div class="table-responsive">
	<table class="table table-default table-bordered" id="week-tbl">
		<tbody>
			<?php
				foreach($dates as $dt) {
					echo '<tr>';
					echo '<td align="center" valign="middle"><small><b>'.format_datetime($dt['date'],1).'</b></small></td>';
					echo '<td align="center" valign="middle"><small><b>'.strtoupper((date("D",strtotime($dt['date'])))).'</b></small></td>';
					if(!empty($dt["staffs"])) {
						foreach($dt["staffs"] as $staff) {
							echo '<td align="center" valign="middle"><small><b>'.ucwords((strtolower($staff['fname'].' '.$staff['lname']))).'</b></small></td>';
						}
					}
					echo '</tr>';
				} 
			?>
		</tbody>
	</table>
</div>