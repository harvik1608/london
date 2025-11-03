<?php 
	if(!empty($appointments)) {
		$total = 0;
		foreach($appointments as $appointment) {
			$total = $total + $appointment['finalAmt'];
?>
			<tr>
				<td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>">
					<?php 
						echo "<small>".format_datetime($appointment['bookingDate'],1)."</small>"; 
						$stime = "";
						if(isset($appointment["items"]) && !empty($appointment["items"])) {
							$stime = " <br>".format_datetime($appointment["items"][0]['stime'],2); 
						}
						echo "<small>".$stime."</small>";
					?>
				</td>
				<td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>">
					<?php
						if(isset($appointment["items"]) && !empty($appointment["items"])) {
							foreach($appointment["items"] as $item) {
								echo $item["serviceNm"]."<hr>";
							}
						} 
					?>
				</td>
				<td align="center" style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>">
					<?php
					    if($appointment["is_old_data"] == 0) {
					        if(isset($appointment["items"]) && !empty($appointment["items"])) {
    							foreach($appointment["items"] as $item) {
    								echo $item["staff_name"]."<hr>";
    							}
    						}
					    } else {
					        echo "-";  
					    }
					?>
				</td>
				<td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>"><?php echo $appointment['note']; ?></td>
				<td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>">
					<?php
						switch ($appointment["bookedFrom"]) {
							case 1:
								echo "<span class='alert alert-success' style='padding: 5px 10px;'>Online</span>";
								break;

							case 2:
								echo "<span class='alert alert-success' style='padding: 5px 10px;'>Treatwell</span>";
								break;
							
							default:
								echo "<span class='alert alert-success' style='padding: 5px 10px;'>Salon</span>";
								break;
						}
					?>
				</td>
				<td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>">
					<?php
						switch ($appointment["status"]) {
							case 1:
								echo "<span class='alert alert-info' style='padding: 5px 10px;'>Confirmed</span>";
								break;

							case 2:
								echo "<span class='alert alert-success' style='padding: 5px 10px;'>Completed</span>";
								break;
							
							default:
								echo "<span class='alert alert-danger' style='padding: 5px 10px;'>No Show</span>";
								break;
						}
					?>
				</td>
				<td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>" align="right">
					<?php echo static_company_currency()." ".$appointment['finalAmt']; ?>
				</td>
				<!-- <td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>">
					<?php
						if(isset($appointment["items"]) && !empty($appointment["items"])) {
							foreach($appointment["items"] as $item) {
								echo "<small>".$item["service_name"]." <br> ".$item["duration"]." min. with ".$item["staff_name"]."</small><hr>";
							}
						} 
					?>
				</td>
				<td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>"><?php echo "<small>".$appointment['note']."</small>"; ?></td>
				<td style="color: <?php echo isset($appointment["status"]) && $appointment["status"] == 3 ? '#FF0000' : '#000000'; ?>"><?php echo "<small>".static_company_currency()." ".$appointment['finalAmt']."</small>"; ?></td> -->
			</tr>
<?php
		}
?>
		<tr>
			<td colspan="6" align="right"><b>TOTAL</b></td>
			<td align="right"><b><?php echo static_company_currency()." ".$total; ?></b></td>
		</tr>
<?php
	}
?>