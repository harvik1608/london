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
					<td>This is a reminder for your upcoming appointment today at <?php echo $start_time; ?> with <b><?php echo $company_name; ?></b> team, <?php echo $company_address; ?></td>
				</tr>
			</tbody>
		</table>
		<p>Appointment Details</p>
		<table width="100%;border: 1px solid #efefef;padding: 5px;">
			<thead>
				<tr>
					<th style="border: 1px solid #efefef;padding: 10px;" align="left">No</th>
					<th style="border: 1px solid #efefef;padding: 10px;" align="left">Service</th>
					<th style="border: 1px solid #efefef;padding: 10px;" align="left">Time</th>
					<th style="border: 1px solid #efefef;padding: 10px;" align="left">Duration</th>
					<th style="border: 1px solid #efefef;padding: 10px;" align="right">Price</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$grand_total = 0;
					if($items) {
						$no = 0;
						foreach($items as $item) {
							$no++;
							$time = date("h:i A",strtotime($item['stime']))." To ".date("h:i A",strtotime($item['etime']));
				?>
							<tr>
								<td style="border: 1px solid #efefef;font-size: 13px;padding: 10px;" align="left"><?php echo $no; ?></td>
								<td style="border: 1px solid #efefef;font-size: 13px;padding: 10px;" align="left"><?php echo $item['serviceNm']; ?></td>
								<td style="border: 1px solid #efefef;font-size: 13px;padding: 10px;" align="left"><?php echo $time; ?></td>
								<td style="border: 1px solid #efefef;font-size: 13px;padding: 10px;" align="left"><?php echo $item['duration']; ?> Min.</td>
								<td style="border: 1px solid #efefef;font-size: 13px;padding: 10px;" align="right"><?php echo $currency." ".number_format($item['amount'],2); ?></td>
							</tr>
				<?php
							$grand_total = $grand_total + $item['amount'];
						}
					}
				?>
						<tr>
							<td style="border: 1px solid #efefef;font-size: 13px;padding: 10px;" colspan="4" align="right"><b>TOTAL</b></td>
							<td style="border: 1px solid #efefef;font-size: 13px;padding: 10px;" align="right"><?php echo $currency." ".number_format($grand_total,2); ?></td>
						</tr>
				<?php
				?>
			</tbody>
		</table>
	</body>
</html>