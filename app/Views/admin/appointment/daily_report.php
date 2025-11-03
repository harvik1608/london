<?php 
	$total_price = $total_discount_amt = $total_final_amt = 0;
?>
<div class="table-responsive">
	<table class="table table-default table-bordered">
		<thead>
			<tr>
				<th width="5%"><small><b>No</b></small></th>
				<th><small><b>Name</b></small></th>
				<th width="10%"><small><b>Phone</b></small></th>
				<th width="10%"><small><b>Booking Type</b></small></th>
				<th width="10%"><small><b>Date</b></small></th>
				<th width="20%" align="center"><small><b>Services</b></small></th>
				<th width="15%" align="center"><small><b>Payment Method</b></small></th>
				<th width="10%"><small><b>Price</b></small></th>
				<th width="10%"><small><b>Dis. Amount</b></small></th>
				<th width="10%"><small><b>Final Amount</b></small></th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$total_price = $total_discount_amt = $total_final_amt = 0;
				if(!empty($appointments)) {
					$sr_no = 0;
					foreach($appointments as $appointment) {
						$sr_no++;						
			?>	
						<tr>
							<td><small><?php echo $sr_no; ?></small></td>
							<td>
								<small><?php echo $appointment['customer_name']; ?></small>
								<!-- < ?php echo $appointment['type'] == 'Y' ? "<br><small><b>(WALKIN)</b></small>" : ""; ?> -->
							</td>
							<td><small><?php echo $appointment['customer_phone']; ?></small></td>
							<td><small><?php echo $appointment['type'] == 'Y' ? "Appointment" : "Walkin"; ?></small></td>
							<td><small><?php echo date('d-m-Y',strtotime($appointment['bookingDate'])); ?></small></td>
							<td align="center">
								<?php 
									if(isset($appointment["carts"]) && !empty($appointment["carts"])) {
										foreach($appointment["carts"] as $cart) {
											echo "<small>".$cart['service_name']." - ".ucwords(strtolower($cart["fname"]." ".$cart["lname"]))."<br>".static_company_currency()." ".$cart["amount"]."<br>".$cart['stime']." To ".$cart["etime"]."</small><br>-----------------<br>";
										}
									}
								?>
							</td>
							<td align="center">
								<?php 
									if(isset($appointment["payment_list"]) && !empty($appointment["payment_list"])) {
										foreach($appointment["payment_list"] as $payment_list) {
											echo "<small>".$payment_list["name"]."<br>".static_company_currency()." ".$payment_list["paymentAmount"]."</small><br>-----------<br>";
										}
									}
								?>
							</td>
							<td align="right">
								<small>
									<b><?php echo static_company_currency()." ".$appointment['subTotal']; ?></b>
								</small>
							</td>
							<td align="right">
								<small>
									<b><?php echo static_company_currency()." ".($appointment['discountAmt']+$appointment['extra_discount']); ?></b>
								</small>
							</td>
							<td align="right">
								<small>
									<b><?php echo static_company_currency()." ".$appointment['subTotal']-($appointment['discountAmt']+$appointment['extra_discount']); ?></b>
								</small>
							</td>
						</tr>
			<?php
						$total_price = $total_price + $appointment['subTotal'];
						$total_discount_amt = $total_discount_amt + ($appointment['discountAmt']+$appointment['extra_discount']);
						$total_final_amt = $total_final_amt + $appointment['subTotal']-($appointment['discountAmt']+$appointment['extra_discount']);
					}
			?>
					<tr>
						<td colspan="7" align="right"><b>TOTAL</b></td>
						<td align="right"><b><?php echo static_company_currency()." ".$total_price; ?></b></td>
						<td align="right"><b><?php echo static_company_currency()." ".$total_discount_amt; ?></b></td>
						<td align="right"><b><?php echo static_company_currency()." ".$total_final_amt; ?></b></td>
					</tr>
			<?php		
				}
			?>
		</tbody>
	</table>
	<div class="row">
		<div class="col-lg-6">
			<table class="table table-default table-bordered table-striped">
				<tbody>
					<tr>
						<td align="right">Total Amount</td>
						<td align="right"><?php echo static_company_currency()." ".$total_price; ?></td>
					</tr>
					<tr>
						<td align="right">Total Discount </td>
						<td align="right">- <?php echo static_company_currency()." ".$total_discount_amt; ?></td>
					</tr>
					<tr>
						<td align="right"><b>Total Net Amount</b></td>
						<td align="right"><?php echo static_company_currency()." ".$total_final_amt; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-lg-6">
			<table class="table table-default table-bordered table-striped">
				<tbody>
					<?php
						$total_amt = 0;
						if(!empty($payment_methods)) {
							foreach($payment_methods as $payment_method) {
					?>
								<tr>
									<td align="right"><?php echo $payment_method['name']; ?></td>
									<td align="right">
										<?php 
											$total = 0;
											if(!empty($appointments)) {
												foreach($appointments as $appointment) {
													if(isset($appointment["payments"]) && !empty($appointment["payments"])) {
														foreach($appointment["payments"] as $payment) {
															if($payment["paymentMethod"] == $payment_method["id"])
																$total = $total + $payment["paymentAmount"];
														}
													}
												}
											}
											$total_amt = $total_amt + $total;
											echo static_company_currency()." ".$total;
										?>
									</td>
								</tr>
					<?php
							}
					?>
								<tr>
									<td align="right"><b>Total Net Amount</b></td>
									<td align="right"><?php echo static_company_currency()." ".$total_amt; ?></td>
								</tr>
					<?php
						} 
					?>
				</tbody>
			</table>
		</div>
		<hr>
		<div class="col-lg-12">
			<h2>Service Groups</h2><hr>
			<?php
				if(!empty($service_groups)) {
					foreach($service_groups as $service_group) {
						if(!empty($service_group["services"])) {
							$service_total = 0;
			?>
							<h6><?php echo $service_group['name']; ?></h6>
							<div class="table-responsive">
								<table class="table table-default table-bordered service-group-tbl">
									<tbody>
										<?php
											foreach($service_group["services"] as $service) {
										?>
												<tr>
													<td width="90%"><?php echo $service['name']; ?></td>
													<td width="10%" align="right">
														<?php 
															$total = 0;
															if(!empty($appointments)) {
																foreach($appointments as $appointment) {
																	if(isset($appointment["carts"]) && !empty($appointment["carts"])) {
																		foreach($appointment["carts"] as $cart) {
																			if($cart["serviceSubId"] == $service["id"])
																				$total = $total + $cart["amount"];
																		}
																	}
																}
															}
															$service_total = $service_total + $total;
															echo static_company_currency()." ".$total;
														?>
													</td>
												</tr>
										<?php
											} 
										?>
										<tr>
											<td align="right"><b>TOTAL</b></td>
											<td align="right"><b><?php echo static_company_currency()." ".$service_total; ?></b></td>
										</tr>
									</tbody>
								</table>
							</div>
			<?php
						} else {
			?>
			                <div class="table-responsive">
								<table class="table table-default table-bordered service-group-tbl">
									<tbody>
										<tr>
											<td>No appointment booked.</td>
										</tr>
									</tbody>
								</table>
							</div>
			<?php
						}
					}
				} 
			?>
		</div>
	</div>
</div>