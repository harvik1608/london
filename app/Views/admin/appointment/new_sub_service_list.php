<?php 
	if($services)
	{
		$appointment_day = strtolower(date("D",strtotime($appointment_date)));
		echo "<h5>Service Name : <b>".$service_name."</b></h5>";
?>
		<table class="table table-bordered" style="width:100%">
			<thead>
				
			</thead>
		</table>
<?php
	}
?>