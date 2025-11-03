<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title></title>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
		<style>
			body {
				font-family: Nunito, serif !important;
				font-optical-sizing: auto;
				font-weight: 400;
				font-style: normal;
			}
		</style>
	</head>
	<body>
		<table width="100%" style="font-size: 13px !important;">
			<tbody>
				<tr>
					<td>Hello <?php echo $customer_name; ?>,</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
							if($is_for_admin == 0) {
						?>
								<p>Thank you for booking your appointment with <?php echo $company_name; ?>. Here are the details of your appointment:</p>
						<?php
							} else {
						?>
								<p><?php echo $customer_name; ?> recently booked appointment with <?php echo $company_name; ?>. Here are the details of your appointment:</p>
						<?php
							}
						?>
					</td>
				</tr>
				<tr>
					<td>Customer Name</td>
					<td>: <?php echo $customer_name; ?></td>
				</tr>
				<tr>
					<td>Customer Email</td>
					<td>: <?php echo $customer_email; ?></td>
				</tr>
				<tr>
					<td>Customer Phone</td>
					<td>: <?php echo $customer_phone; ?></td>
				</tr>
				<tr>
					<td>Appointment Date</td>
					<td>: <b><?php echo $booking_date; ?></b></td>
				</tr>
				<tr>
					<td>Total</td>
					<td>: <?php echo $currency." ".number_format($total,2); ?></td>
				</tr>
			</tbody>
		</table>
		<p>Appointment Details</p>
		<table width="100%;border: 1px solid #efefef;padding: 5px;">
			<thead>
				<tr>
					<th style="border: 1px solid #efefef;" align="left">No</th>
					<th style="border: 1px solid #efefef;">Service</th>
					<th style="border: 1px solid #efefef;">Time</th>
					<th style="border: 1px solid #efefef;">Duration</th>
					<th style="border: 1px solid #efefef;" align="right">Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$grand_total = 0;
					if($items) {
						$no = 0;
						foreach($items as $item) {
							$no++;
				?>
							<tr>
								<td style="border: 1px solid #efefef;font-size: 13px;" align="left"><?php echo $no; ?></td>
								<td style="border: 1px solid #efefef;font-size: 13px;" align="center"><?php echo $item['service']; ?></td>
								<td style="border: 1px solid #efefef;font-size: 13px;" align="center"><?php echo $item['time']; ?></td>
								<td style="border: 1px solid #efefef;font-size: 13px;" align="center"><?php echo $item['duration']; ?> Min.</td>
								<td style="border: 1px solid #efefef;font-size: 13px;" align="right"><?php echo number_format($item['price'],2); ?></td>
							</tr>
				<?php
							$grand_total = $grand_total + $item['price'];
						}
					}
				?>
						<tr>
							<td style="border: 1px solid #efefef;font-size: 13px;" colspan="4" align="right">Total Amount</td>
							<td style="border: 1px solid #efefef;font-size: 13px;" align="right"><?php echo $currency." ".number_format($grand_total,2); ?></td>
						</tr>
				<?php
				?>
			</tbody>
		</table>
		<?php
			if($is_for_admin == 0) {
		?>
				<table>
					<tbody style="font-size: 13px !important;">
						<tr>
							<td>If you need to make any changes or have any questions, feel free to contact us:</td>
						</tr>
						<tr>
							<td><p>Phone: <?php echo $company_phone; ?><br>WhatsApp: <?php echo $company_whatsapp; ?><br>Email: <?php echo $company_email; ?></p></td>
						</tr>
						<tr>
							<td><p>We look forward to seeing you!</p></td>
						</tr>
						<tr>
							<td>Warm regards,<br><?php echo $company_address; ?><br><?php echo $company_website_url; ?></td>
						</tr>
					</tbody>
				</table>
		<?php 
			}
		?>
	</body>
</html>