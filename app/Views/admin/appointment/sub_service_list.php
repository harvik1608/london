<?php 
	if($services)
	{
		$appointment_day = strtolower(date("D",strtotime($appointment_date)));
		echo "<h5>Service Name : <b id='".$service_group_id."'>".$service_name."</b></h5>";
?>
		<table class="table table-bordered" style="width:100%">
			<tbody>
				<?php
					foreach($services as $service)
					{
					    $multiple = addslashes($service['json']);
						$ser_name = addslashes($service['name']);
				?>
						<tr>
							<td>
								<?php
									if($service['price_type'] == 0)
									{
										$single_fields = get_service_prices($service['id'],0,$appointment_date,$uniq_id,$service_group_id,$appointment_id);	
										if(!empty($single_fields))
										{
											foreach($single_fields as $sfield)
											{
												if($sfield['retail_price'] != "") 
												{
													$sprice = $sfield['special_price'];
													$rprice = $sfield['retail_price'];
													if($sprice == $rprice) {
														$price = $sprice; 	
														$service_price = $sprice; 	
													} else if($rprice != "" && $sprice == "") {
														$price = $rprice;
														$service_price = $rprice; 	
													} else if($rprice != "" && $sprice != "") {
														$price = $sprice." <strike>".$rprice."</strike>"; 
														$service_price = $sprice; 	
													} else {
														$price = $rprice;
														$service_price = $rprice; 	
													}
												}
								?>
												<a href="javascript:;" onclick="add_to_cart('<?php echo $service['id']; ?>','<?php echo $sfield['id']; ?>','<?php echo $service['name']; ?>','<?php echo $sfield['caption']; ?>','<?php echo $service_price; ?>','<?php echo $sfield['duration']; ?>','<?php echo $flag; ?>','','<?php echo $rprice; ?>');"></a>
								<?php
											}
											echo "<a>".$service['name']."</a>";
										}
									} else {
										echo "<a>".$service['name'].".</a>";
									} 
								?>
							</td>
							<?php
								// if($service['json'] != "")
								// {
									$weekday_sprice = 0;
									$weekday_rprice = 0;
									$fields = get_service_prices($service['id'],0,$appointment_date,$uniq_id,$service_group_id,$appointment_id);
									if(!empty($fields))
									{
										foreach($fields as $field)
										{
											if($field['retail_price'] != "") 
											{
												$sprice = (float) $field['special_price'];
												$rprice = (float) $field['retail_price'];
									            if($sprice == $rprice) {
													$price = $sprice; 	
													$service_price = $sprice; 	
												} else if($rprice != 0 && $sprice == 0) {
													$price = $rprice;
													$service_price = $rprice; 	
												} else if($rprice != 0 && $sprice != 0) {
													$price = $sprice." <strike>".$rprice."</strike>"; 
													$service_price = $sprice; 	
												} else {
													$price = $rprice;
													$service_price = $rprice; 	
												}
												$uniq_element = md5($uniq_id."-".$service['id'].'-'.$field['caption']);

							?>
												<td align="center" data-cart="<?php echo isset($field['is_added_in_cart']) ? $field['is_added_in_cart'] : '007'; ?>" onclick="add_to_cart('<?php echo $service['id']; ?>','<?php echo $field['id']; ?>','<?php echo $service['name']; ?>','<?php echo $field['caption']; ?>','<?php echo $service_price; ?>','<?php echo $field['duration']; ?>','<?php echo $flag; ?>','<?php echo $uniq_element; ?>','<?php echo $rprice; ?>');" style="font-size: 11px;cursor: pointer;" data-uniq="<?php echo $uniq_element; ?>" data-isaddedcart="<?php echo isset($field['is_added_in_cart']) ? $field['is_added_in_cart'] : 0; ?>">
													<?php
														if($field['caption'] == "")
														{
													?>
															<span style="font-size: 12px;padding-top: 2px;padding-bottom: 2px;" class="btn btn-<?php echo isset($field['is_added_in_cart']) && $field['is_added_in_cart'] > 0 ? 'danger' : 'success'; ?> btn-sm">
																<?php echo static_company_currency()." ".$price; ?>
															</span>
													<?php
														} else {
													?>
															<?php echo $field['caption']; ?>
															<br>
															<span style="font-size: 12px;padding-top: 2px;padding-bottom: 2px;" class="btn btn-<?php echo isset($field['is_added_in_cart']) && $field['is_added_in_cart'] > 0 ? 'danger' : 'success'; ?> btn-sm"><?php echo static_company_currency()." ".$price; ?></span>
													<?php
														}
													?>
												</td>
							<?php
											}
										}
									}
								// } 
							?>
						</tr>
				<?php
					} 
				?>
			</tbody>
		</table>
<?php
	}
?>