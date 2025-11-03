<?=$this->extend("include/front_header")?>
<?=$this->section("content")?>
<style>
	.lazy {
		width: 266px !important;
		height: 238px !important;
	}
	.breadcrumb-row ul li:after {
		content: unset;
	}
	span.btn-info {
		background-color: <?php echo $treatments['code']; ?>;
    	border: 1px solid <?php echo $treatments['code']; ?>;
	}
	.treatment {
		border: 1px solid <?php echo $treatments['code']; ?>;
		padding: 10px;
		border-radius: 10%;
	}
	.treatment-active {
		border: 1px solid <?php echo $treatments['code']; ?>;
		padding: 10px;
		border-radius: 10%;
		background-color: <?php echo $treatments['code']; ?>;
		color: #FFFFFF;
	}
	a {
	    font-size: 12px !important;
	    font-weight: bold !important;
	}
</style>
<div class="page-content bg-white">    
	<div class="dez-bnr-inr overlay-white-middle tb" style="background-image:url(<?php echo base_url('public/frontend/images/banner/bnr1.jpg'); ?>);">
        <div class="container">
            <div class="dez-bnr-inr-entry">
                <h1 class="text-white"><?php echo $treatments['treatment_name']; ?></h1>
				<!-- Breadcrumb row -->
				<div class="breadcrumb-row">
					<ul class="list-inline">
						<li><a href="<?php echo base_url(); ?>">Home</a></li>
						<li><b>></b> <a href="<?php echo base_url('treatments'); ?>">Treatments</a></li>
						<li><b>></b> <?php echo $treatments['treatment_name']; ?></li>
					</ul>
				</div>
            </div>
        </div>
    </div>
    <div class="content-area bgeffect">
    	<div class="container">
            <div class="section-content">
                <div class="row">
                	<div class="col-md-12 col-sm-6 m-b30">
                		<?php
                            if($other_treatments)
                            {
                                foreach($other_treatments as $g)
                                {
                                    $gname = strtolower($g["name"]);
                                    $gname = ucwords($gname);
                                    $href = base_url("treatment/".$g["id"]);
                                    if($g["id"] == $treatments['treatment_id'])
                                    {
                        ?>
                                        <a href="<?php echo $href; ?>" class="treatment-active"><?php echo $gname; ?></a>
                        <?php
                                    } else {
                        ?>
                                        <a href="<?php echo $href; ?>" class="treatment"><?php echo $gname; ?></a>    
                        <?php
                                    }
                                }
                            }
                        ?>
                	</div>
                	<div class="col-md-3 col-sm-6 m-b30">
						<div class="dez-media dez-img-overlay6 gradient"> 
							<img src="<?php echo $treatments['avatar'] ?>" alt="<?php echo $treatments['treatment_name']; ?>" /> 
						</div>
					</div>
					<div class="col-md-9 col-sm-6">
						<div class="icon-bx-wraper">
							<div class="icon-md text-black m-b20"> 
								<a href="#" class="icon-cell text-black"></a> 
							</div>
							<div class="icon-content m-b30">
								<p style="text-align:justify;"><?php echo $treatments['treatment_note']; ?></p>
							</div>
						</div>
						<?php
							if($treatments["sub_treatments"])
							{
						?>
								<table class="table table-default table-bordered">
									<tbody>
										<?php
											foreach($treatments["sub_treatments"] as $key => $val)
											{
										?>
												<tr>
													<td><?php echo $val["name"]; ?></td>
													<?php
														if($val['json'] != "")
														{
															$fields = json_decode($val["json"],true);
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
																		$serviceId 	= $val['id'];
																		$fieldId 	= $field['id'];
																		$serviceNm 	= $val['name'];
																		$caption 	= $field['caption'];
																		$duration 	= $field['duration'];
																		$mainSerId	= $val["id"];
													?>
																		<td align="center" style="font-size: 11px;cursor: pointer;">
																			<?php
																				if($field['caption'] == "")
																				{
																			?>
																					<span style="font-size: 14px;" class="btn btn-info btn-sm" onclick="open_modal('<?php echo $serviceId; ?>','<?php echo $fieldId; ?>','<?php echo $serviceNm; ?>','<?php echo $caption; ?>','<?php echo $service_price; ?>','<?php echo $duration; ?>',1,'<?php echo $mainSerId; ?>');">
																						<?php echo $treatments['currency_sign']." ".$price; ?>
																					</span>
																			<?php
																				} else {
																			?>
																					<small><?php echo $field['caption']; ?></small>
																					<br>
																					<span style="font-size: 14px;" class="btn btn-info btn-sm" onclick="open_modal('<?php echo $serviceId; ?>','<?php echo $fieldId; ?>','<?php echo $serviceNm; ?>','<?php echo $caption; ?>','<?php echo $service_price; ?>','<?php echo $duration; ?>',1,'<?php echo $mainSerId; ?>');">£ <?php echo $price; ?></span>
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
					</div>
                </div>
           	</div>
       	</div>
    </div>
</div>
<script>
    /* document.addEventListener("DOMContentLoaded", function() {
        const allowedDates = ["2025-02-01", "2025-02-05", "2025-02-10"]; // Example list of allowed dates
    
        const datepicker = document.getElementById("appointment_date");
    
        datepicker.addEventListener("input", function(event) {
            const selectedDate = event.target.value;
    
            // If the selected date is not in the allowedDates array, reset the value
            if (!allowedDates.includes(selectedDate)) {
                alert("This date is not allowed!");
                datepicker.value = "";  // Clear the date input
            }
        });
        
        datepicker.addEventListener("focus", function() {
            const today = new Date().toISOString().split("T")[0];  // Get today's date in YYYY-MM-DD format
            const validDates = allowedDates.join("|"); // Create a regex of allowed dates
            datepicker.setAttribute('list', 'validDates');

            // Set a custom validation pattern to match only allowed dates
            datepicker.setAttribute('pattern', `(${validDates})`);
        });
    }); */
</script>
<?=$this->endSection()?>