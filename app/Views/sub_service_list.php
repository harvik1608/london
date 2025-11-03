<?php 
	if(!empty($services))
	{
		echo "<h5>Service Name : <b>".$service_name."</b></h5>";
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
										$single_fields = json_decode($service["json"],true);	
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
												<a href="javascript:;" onclick="add_to_cart('<?php echo $service['id']; ?>','<?php echo $sfield['id']; ?>','<?php echo $service['name']; ?>','<?php echo $sfield['caption']; ?>','<?php echo $service_price; ?>','<?php echo $sfield['duration']; ?>','<?php echo $flag; ?>');">
												<small><?php echo $service['name']; ?></small>
											</a>
								<?php

											}
										}
									} else {
								?>
										<a><small><?php echo $service['name']; ?></small></a>
								<?php	
									} 
								?>
							</td>
							<?php
								if($service['json'] != "")
								{
									$fields = json_decode($service["json"],true);
									if(!empty($fields))
									{
										foreach($fields as $field)
										{
											if($field['retail_price'] != "") 
											{
												$sprice = $field['special_price'];
												$rprice = $field['retail_price'];
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
							?>
												<td align="center" style="font-size: 11px;cursor: pointer;" onclick="add_to_cart('<?php echo $service['id']; ?>','<?php echo $field['id']; ?>','<?php echo $service['name']; ?>','<?php echo $field['caption']; ?>','<?php echo $service_price; ?>','<?php echo $field['duration']; ?>','<?php echo $flag; ?>');">
													<?php
														if($field['caption'] == "")
														{
													?>
															<span style="font-size: 12px;padding-top: 9px;" class="btn btn-success btn-sm">
																<?php echo $currency." ".$price; ?>
															</span>
													<?php
														} else {
													?>
															<small><?php echo $field['caption']; ?></small>
															<br>
															<span style="font-size: 12px;padding-top: 9px;" class="btn btn-success btn-sm"><?php echo $currency; ?> <?php echo $price; ?></span>
													<?php
														}
													?>
												</td>
							<?php
											}
										}
									}
								} 
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