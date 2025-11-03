<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<?php 
	$company_service_group_arr = $company_service_arr = array();
	$banners = [];
	if($company) {
		$page_title = "Edit Company";
		$action = base_url('companies/'.$company["id"]);

		$company_name = $company["company_name"];
		$company_email = $company["company_email"];
		$company_phone = $company["company_phone"];
		$company_whatsapp_phone = $company["company_whatsapp_phone"];
		$company_desc = $company["company_desc"];
		$company_address = $company["company_address"];
		$company_logo = $company["company_logo"];
		$banner = $company["banner"];
		$isActive = $company["isActive"];
		$currency = $company["currency"];
		$company_stime = $company["company_stime"];
		$company_etime = $company["company_etime"];
		$about_company = $company["about_company"];
		$smtp_email = $company["smtp_email"];
		$smtp_password = $company["smtp_password"];
		$smtp_host = $company["smtp_host"];
		$smtp_port = $company["smtp_port"];
		$from_email = $company["from_email"];
		$from_name = $company["from_name"];
		$website_url = $company["website_url"];
		$facebook_link = $company["facebook_link"];
		$google_link = $company["google_link"];
		$instagram_link = $company["instagram_link"];
		$company_currency = $company["company_currency"];
		$old_company_logo = $company["company_logo"];
		$old_banner = $company["banner"];
		$timezone = $company["timezone"];
		$privacy_policy = $company["privacy_policy"];
		$parking_instructions = $company["parking_instructions"];
		$color_code = $company["code"];
		$company_sunday_stime = $company["company_sunday_stime"];
		$company_sunday_etime = $company["company_sunday_etime"];
		$wa_phone_id = $company["wa_phone_id"];
		$wa_token = $company["wa_token"];
		$google_map = $company['google_map'];
		if($company["company_service_groups"] != "") {
			$company_service_group_arr = explode(",", $company["company_service_groups"]);
		}
		if($company["company_services"] != "") {
			$company_service_arr = explode(",", $company["company_services"]);
		}
		if($company["banners"] != "") {
			$banners = json_decode($company["banners"],true);
		}
	} else {
		$page_title = "New Company";
		$action = base_url('companies');

		$company_name = "";
		$company_email = "";
		$company_phone = "";
		$company_whatsapp_phone = "";
		$company_desc = "";
		$company_address = "";
		$company_logo = "";
		$banner = "";
		$isActive = "";
		$currency = "£";
		$company_stime = "";
		$company_etime = "";
		$about_company = "";
		$smtp_email = "";
		$smtp_password = "";
		$smtp_host = "";
		$smtp_port = "";
		$from_email = "";
		$from_name = "";
		$website_url = "";
		$facebook_link = "";
		$google_link = "";
		$instagram_link = "";
		$company_currency = "";
		$old_company_logo = "";
		$old_banner = "";
		$timezone = "Europe/London";
		$privacy_policy = "";
		$parking_instructions = "";
		$color_code = "#000000";
		$company_sunday_stime = "";
		$company_sunday_etime = "";
		$wa_phone_id = "";
		$wa_token = "";
		$google_map = "";
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-bank"></i> Companies</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('companies'); ?>">Companies</a></li>
		<li class="breadcrumb-item"><a><?php echo $page_title; ?></a></li>
	</ul>
</div>
<form class="form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
<div class="row">
	<div class="col-md-8">
		<div class="tile">
			<input type="hidden" name="old_company_photo" value="<?php echo $old_company_logo; ?>" />
			<input type="hidden" name="old_banner" value="<?php echo $old_banner; ?>" />
			<?php
				if($company_name != "")
					echo '<input type="hidden" name="_method" value="PUT" />'; 
			?>
			<div class="tile-title">
				<h4><?php echo $page_title; ?></h4>
			</div><hr>
			<div class="tile-body">	
				<div class="row">
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Company Name*</b></small></label>
							<input class="form-control" type="text" placeholder="" name="company_name" id="company_name" value="<?php echo $company_name; ?>" />
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Company Phone*</b></small></label>
							<input class="form-control" type="text" placeholder="" name="company_phone" id="company_phone" value="<?php echo $company_phone; ?>" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label class="control-label"><small><b>Company Email*</b></small></label>
							<input class="form-control" type="text" placeholder="" name="company_email" id="company_email" value="<?php echo $company_email; ?>" />
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Company Whatsapp Phone*</b></small></label>
							<input class="form-control" type="text" placeholder="" name="company_whatsapp_phone" id="company_whatsapp_phone" value="<?php echo $company_whatsapp_phone; ?>" />
						</div>
					</div>
					<div class="col-lg-9">
						<div class="form-group">
							<label class="control-label"><small><b>Company Address*</b></small></label>
							<input type="text" class="form-control" placeholder="" name="company_address" id="company_address" value="<?php echo $company_address; ?>" />
						</div>
					</div>
					<div class="col-lg-4" hidden>
						<div class="form-group">
							<label class="control-label"><small><b>Company Description</b></small></label>
							<textarea class="form-control" placeholder="" name="company_desc" id="company_desc"><?php echo $company_desc; ?></textarea>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Start Time*</b></small></label>
							<input class="form-control" type="time" placeholder="" name="company_stime" id="company_stime" value="<?php echo $company_stime; ?>" />
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>End Time*</b></small></label>
							<input class="form-control" type="time" placeholder="" name="company_etime" id="company_etime" value="<?php echo $company_etime; ?>" />
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Sunday Start Time*</b></small></label>
							<input class="form-control" type="time" placeholder="" name="company_sunday_stime" id="company_sunday_stime" value="<?php echo $company_sunday_stime; ?>" />
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Sunday End Time*</b></small></label>
							<input class="form-control" type="time" placeholder="" name="company_sunday_etime" id="company_sunday_etime" value="<?php echo $company_sunday_etime; ?>" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label class="control-label"><small><b>Company Website URL</b></small></label>
							<input class="form-control" type="text" placeholder="" name="website_url" id="website_url" value="<?php echo $website_url; ?>" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label class="control-label"><small><b>Company Facebook Link</b></small></label>
							<input class="form-control" type="text" placeholder="" name="facebook_link" id="facebook_link" value="<?php echo $facebook_link; ?>" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label class="control-label"><small><b>Company Google Link</b></small></label>
							<input class="form-control" type="text" placeholder="" name="google_link" id="google_link" value="<?php echo $google_link; ?>" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label class="control-label"><small><b>Company Instagram Link</b></small></label>
							<input class="form-control" type="text" placeholder="" name="instagram_link" id="instagram_link" value="<?php echo $instagram_link; ?>" />
						</div>
					</div>
					
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Currency</b></small></label>
							<input class="form-control" type="text" placeholder="" name="currency" id="currency" value="<?php echo $currency; ?>" />
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Timezone</b></small></label>
							<input class="form-control" type="text" placeholder="" name="timezone" id="timezone" value="<?php echo $timezone; ?>" />
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Theme Color</b></small></label>
							<input class="form-control" type="text" name="code" id="code" value="<?php echo $color_code; ?>" />
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<label class="control-label"><small><b>Status</b></small></label>
							<select class="form-control" name="isActive" id="isActive">
								<option value="1" <?php echo $isActive == 1 ? 'selected' : ''; ?>>Active</option>
								<option value="0" <?php echo $isActive == 0 ? 'selected' : ''; ?>>Inactive</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label"><small><b>Google Map</b></small></label>
							<textarea class="form-control" placeholder="" name="google_map" id="google_map"><?php echo $google_map; ?></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label"><small><b>About Company</b></small></label>
							<textarea class="form-control summernote" placeholder="" name="about_company" id="about_company"><?php echo $about_company; ?></textarea>
						</div>
					</div>
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label"><small><b>Privacy Policy</b></small></label>
							<textarea class="form-control summernote" id="privacy_policy" name="privacy_policy"><?php echo $privacy_policy; ?></textarea>
						</div>
					</div>
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label"><small><b>Parking Instructions</b></small></label>
							<textarea class="form-control summernote" id="parking_instructions" name="parking_instructions"><?php echo $parking_instructions; ?></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label class="control-label"><small><b>Company Logo</b></small></label>
							<input class="form-control" type="file" placeholder="" name="company_logo" id="company_logo" />
							<?php
								if($old_company_logo != "") {
									echo '<br><center><img src="'.base_url('public/uploads/company/'.$old_company_logo).'" style="width: 150px;height: 150px;" class="img img-thumbnail img-responsive" /></center>';
								}
							?>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<label class="control-label"><small><b>Banner (1920x1052)</b></small></label>
							<input class="form-control" type="file" placeholder="" name="banner" id="banner" />
							<?php
								if($old_banner != "") {
									echo '<br><center><img src="'.base_url('public/uploads/company/'.$old_banner).'" style="width: 150px;height: 150px;" class="img img-thumbnail img-responsive" /></center>';
								}
							?>
						</div>
					</div>
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label"><small><b>Banners</b></small></label>
							<input class="form-control" type="file" placeholder="" name="banners[]" id="banners" multiple />
							<?php
								if(!empty($banners)) {
									echo "<br><div class='row'>";
									foreach($banners as $bannerVal) {
							?>
										<div class="col-lg-4">
											<center><img src="<?php echo base_url('public/'.$bannerVal['avatar']); ?>" style="width: 150px;height: 150px;" class="img img-thumbnail img-responsive" /></center>
										</div>
							<?php
									}
									echo "</div>";
								}
							?>
						</div>
					</div>
				</div>
				<hr><h2>Whatsapp Settings</h2><hr>
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label">Phone ID</label>
							<input class="form-control" type="text" placeholder="" name="wa_phone_id" id="wa_phone_id" value="<?php echo $wa_phone_id; ?>" />
						</div>
					</div>
					<div class="col-lg-12">
						<div class="form-group">
							<label class="control-label">TOKEN</label>
							<textarea class="form-control" type="text" placeholder="" name="wa_token" id="wa_token" rows="3"><?php echo $wa_token; ?></textarea>
						</div>
					</div>
				</div>
				<hr><h2>SMTP Settings</h2><hr>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label class="control-label">SMTP Email</label>
							<input class="form-control" type="text" placeholder="" name="smtp_email" id="smtp_email" value="<?php echo $smtp_email; ?>" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="control-label">SMTP Password</label>
							<input class="form-control" type="text" placeholder="" name="smtp_password" id="smtp_password" value="<?php echo $smtp_password; ?>" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="control-label">SMTP Host</label>
							<input class="form-control" type="text" placeholder="" name="smtp_host" id="smtp_host" value="<?php echo $smtp_host; ?>" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="control-label">SMTP Port</label>
							<input class="form-control" type="text" placeholder="" name="smtp_port" id="smtp_port" value="<?php echo $smtp_port; ?>" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="control-label">From Email</label>
							<input class="form-control" type="text" placeholder="" name="from_email" id="from_email" value="<?php echo $from_email; ?>" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="control-label">From Name</label>
							<input class="form-control" type="text" placeholder="" name="from_name" id="from_name" value="<?php echo $from_name; ?>" />
						</div>
					</div>
				</div>
			</div>
			<div class="tile-footer">
				<div class="row">
					<div class="col-md-8 col-md-offset-3">
						<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
						<a href="<?php echo base_url('companies'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4" id="service-checkbox">
		<div class="tile">
			<div class="tile-body">
				<div class="custom-control custom-checkbox checkbox-success form-check">
                    <input type="checkbox" class="custom-control-input" name="all_service" id="all_service" />
                    <label class="custom-control-label" for="all_service">All Service</label>
                </div>
                <hr>
				<?php
					if(!empty($service_groups)) {
						foreach($service_groups as $service_group) {
				?>
							<div class="custom-control custom-checkbox checkbox-success form-check">
                                <input type="checkbox" class="custom-control-input" name="service_group[]" id="service_group_<?php echo $service_group['id']; ?>" data-current="<?php echo $service_group['id']; ?>" value="<?php echo $service_group['id']; ?>" <?php echo in_array($service_group['id'],$company_service_group_arr) ? "checked" : ""; ?> />
                                <label class="custom-control-label" for="service_group_<?php echo $service_group['id']; ?>">
                                	<u><b><?php echo strtoupper($service_group['name']); ?></b></u>
                               	</label>
                            </div>
                            <?php 
                            	if(!empty($service_group["services"])) {
                            		foreach($service_group["services"] as $service) {
                            ?>
                            			<div class="custom-control custom-checkbox checkbox-success form-check">
			                                <input type="checkbox" class="custom-control-input" name="service[]" id="service_<?php echo $service['id']; ?>" data-parent="<?php echo $service_group['id']; ?>" value="<?php echo $service['id']; ?>" <?php echo in_array($service['id'],$company_service_arr) ? "checked" : ""; ?> />
			                                <label class="custom-control-label" for="service_<?php echo $service['id']; ?>">
			                                	<small><?php echo $service['name']; ?></small>
			                               	</label>
			                            </div>
                            <?php
                            		}
                            	}
                            ?>
                            <hr>
				<?php
						}
					}
				?>
			</div>
		</div>
	</div>
</div>
</form>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/jquery.validate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/additional_methods.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Companies";
	$(document).ready(function(){
		$('.summernote').summernote({
			height: "500px"
		});
		$("#form").validate({
			rules:{
				company_name: {
					required: true
				},
				company_phone: {
					required: true
				},
				company_email: {
					required: true
				},
				company_address:{
					required: true
				},
				company_stime:{
					required: true
				},
				company_etime:{
					required: true
				},
				company_sunday_stime:{
					required: true
				},
				company_sunday_etime:{
					required: true
				}
			},
			messages:{
				company_name:{
					required: "<small class='error'><i class='fa fa-warning'></i> Company name is required</small>"
				},
				company_phone: {
					required: "<small class='error'><i class='fa fa-warning'></i> Company phone is required</small>"
				},
				company_email: {
					required: "<small class='error'><i class='fa fa-warning'></i> Company email is required</small>"
				},
				company_address:{
					required: "<small class='error'><i class='fa fa-warning'></i> Company address is required</small>"
				},
				company_stime:{
					required: "<small class='error'><i class='fa fa-warning'></i> Start time is required</small>"
				},
				company_etime:{
					required: "<small class='error'><i class='fa fa-warning'></i> End time is required</small>"
				},
				company_sunday_stime:{
					required: "<small class='error'><i class='fa fa-warning'></i> Sunday start time is required</small>"
				},
				company_sunday_etime:{
					required: "<small class='error'><i class='fa fa-warning'></i> Sunday end time is required</small>"
				}
			}
		});
		$("#form").submit(function(e){
			e.preventDefault();

			if($("#form").valid()) {
				$.ajax({
					url: $("#form").attr("action"),
					type: $("#form").attr("method"),
					dataType: "json",
					data: new FormData(this),
					processData: false,
					contentType: false,
					beforeSend:function(){
						$("#form button[type=submit]").attr("disabled",true);
					},
					success:function(response) {
						if(response.status == 1)
							window.location.href = $("#backbtn").attr("href");
					},
					complete:function(){
						// $("#form button[type=submit]").attr("disabled",false);
					}
				});
			}
		});
		$(document).on("click","#all_service",function(){
			if($(this).prop("checked") == true) {
				$("#service-checkbox input[type=checkbox]").prop("checked",true);
			} else {
				$("#service-checkbox input[type=checkbox]").prop("checked",false);
			}
		});
		$(document).on("click","input[id^=service_group_]",function(){
			var current_id = $(this).attr("data-current");
			if($(this).prop("checked") == true) {
				$("input[data-parent="+current_id+"]").prop("checked",true);
			} else {
				$("input[data-parent="+current_id+"]").prop("checked",false);
			}
		});
	});
</script>
<?= $this->endSection(); ?>