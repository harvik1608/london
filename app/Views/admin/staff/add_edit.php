<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<?php
	$session = session();
	$udata = $session->get("userdata");
	$checked_services = $service_arr = $roles = $designations = array();
	$staff_services_arr = array();
	if($staff) {
		$page_title = "Edit Staff";
		$action = base_url('staffs/'.$staff["id"]);

		$id = $staff['id'];
        $fname = $staff['fname'];
        $lname = $staff['lname'];
        $mobile = $staff['phone'];
        $email = $staff['email'];
        $color = $staff['color'];
        $wages = $staff['wages'];
        $selected_services = $staff_services;
        $all = $staff["is_all_service"];
        $is_active = $staff["is_active"];
        $user_type = $udata["user_type"];
        $old_roles = $staff["roles"];
        $old_designations = $staff["designation"];
        if($staff["designation"] != "") {
        	$designations = explode(",",$staff["designation"]);
        }
        if($staff["roles"] != "") {
        	$roles = explode(",",$staff["roles"]);
        }
        if($staff_services != "") {
        	$staff_services_arr = explode(",",$staff_services);
        }
	} else {
		$page_title = "New Staff";
		$action = base_url('staffs');

		$id = "";
        $fname = "";
        $lname = "";
        $mobile = "";
        $email = "";
        $color = "";
        $wages = "";
        $selected_services = "";
        $all = "N";
        $is_active = "1";
        $user_type = 0;
        $old_roles = "";
        $old_designations = "";
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-users"></i> Staffs</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('staffs'); ?>">Staffs</a></li>
		<li class="breadcrumb-item"><a><?php echo $page_title; ?></a></li>
	</ul>
</div>
<form class="form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
	<input type="hidden" name="old_roles" value="<?php echo $old_roles; ?>" />
	<input type="hidden" name="old_designations" value="<?php echo $old_designations; ?>" />
	<div class="row">
		<div class="col-md-3">
			<div class="tile">
				<?php
					if($fname != "")
						echo '<input type="hidden" name="_method" value="PUT" />'; 
				?>
				<div class="tile-title">
					<h4><?php echo $page_title; ?></h4>
				</div><hr>
				<div class="tile-body">	
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">First name</label>
								<input class="form-control" type="text" placeholder="Enter first name" name="fname" id="fname" value="<?php echo $fname; ?>" autofocus />
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Last name</label>
								<input class="form-control" type="text" placeholder="Enter last name" name="lname" id="lname" value="<?php echo $lname; ?>" />
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Mobile No.</label>
								<input class="form-control" type="number" placeholder="Enter mobile no." name="phone" id="phone" value="<?php echo $mobile; ?>" />
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Color</label>
								<input class="form-control" type="color" name="color" id="color" value="<?php echo $color; ?>" />
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Email</label>
								<input class="form-control" type="text" placeholder="Enter email" name="email" id="email" value="<?php echo $email; ?>" />
							</div>
						</div>
						<?php
							if($fname == "") {
						?>
								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">Password</label>
										<input class="form-control" type="password" placeholder="Enter password" name="password" id="password" value="<?php echo $fname; ?>" />
									</div>
								</div>
								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">Confirm Password</label>
										<input class="form-control" type="password" placeholder="Enter confirm password" name="cpassword" id="cpassword" value="<?php echo $fname; ?>" />
									</div>
								</div>
						<?php
							}
						?>
						<div class="col-lg-12" hidden>
							<div class="form-group">
								<label class="control-label">Wages</label>
								<input class="form-control" type="text" placeholder="Enter wages" name="wages" id="wages" value="<?php echo $wages; ?>" />
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Status</label>
								<select class="form-control" name="is_active" id="is_active">
									<option value="1" <?php echo $is_active == '1' ? "selected" : ""; ?>>Active</option>
									<option value="0" <?php echo $is_active == '0' ? "selected" : ""; ?>>Inactive</option>
								</select>
							</div>
						</div>
					</div>
					<?php
						if($user_type == 0) {
					?>
							<div class="col-md-12">
		                        <div class="form-group row">
		                            <label for="colFormLabel" class="col-md-2 col-sm-4 col-form-label">Position:</label>
		                            <div class="col-sm-12">
		                            	<div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="designations[]" id="reception" value="reception" <?php if(in_array("reception",$designations)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="reception">Reception</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="designations[]" id="manager" value="manager" <?php if(in_array("manager",$designations)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="manager">Manager</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="designations[]" id="hair_dresser" value="hair_dresser" <?php if(in_array("hair_dresser",$designations)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="hair_dresser">Hair Dresser</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="designations[]" id="beautician" value="beautician" <?php if(in_array("beautician",$designations)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="beautician">Beautician</label>
		                                </div>
		                          	</div>
		                       	</div>
		                  	</div>
					<?php
							$is_selectall_selected = 0;
							if(in_array("appointments",$roles) && in_array("groups",$roles) && in_array("sub_services",$roles) && in_array("sub_services",$roles) && in_array("staffs",$roles) && in_array("staff_timing",$roles) && in_array("customers",$roles) && in_array("payment_types",$roles) && in_array("discount_types",$roles) && in_array("daily_reports",$roles) && in_array("gallery",$roles) && in_array("companies",$roles) && in_array("weekend_discount",$roles)) {
								$is_selectall_selected = 1;
							}

					?>
							<hr>
							<div class="col-md-12">
		                        <div class="form-group row" id="role-checkbox">
		                            <label for="colFormLabel" class="col-md-2 col-sm-4 col-form-label">Roles:</label>
		                            <div class="col-sm-12">
		                            	<div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" id="selectall" <?php echo $is_selectall_selected == 1 ? "checked" : ""; ?> />
		                                    <label class="custom-control-label" for="selectall">Select All</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role2" value="appointments" <?php if(in_array("appointments",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role2">Appointments</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role3" value="groups" <?php if(in_array("groups",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role3">Service Groups</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role4" value="sub_services" <?php if(in_array("sub_services",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role4">Services</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role5" value="staffs" <?php if(in_array("staffs",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role5">Staffs</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role6" value="staff_timing" <?php if(in_array("staff_timing",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role6">Staff Timing</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role7" value="customers" <?php if(in_array("customers",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role7">Customers</label>
		                                </div>
		                            </div>
		                            <div class="col-sm-12">
		                                <!-- <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role7" value="report_cards" < ?php if(in_array("report_cards",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role7">Report Cards</label>
		                                </div> -->
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role8" value="payment_types" <?php if(in_array("payment_types",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role8">Payment Types</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role9" value="discount_types" <?php if(in_array("discount_types",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role9">Discount Types</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="weekend_discount" value="weekend_discount" <?php if(in_array("weekend_discount",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="weekend_discount">Weekend Discounts</label>
		                                </div>
		                                <!-- <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role10" value="gallery" < ?php if(in_array("gallery",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role10">Gallery</label>
		                                </div> -->
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role11" value="daily_reports" <?php if(in_array("daily_reports",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role11">Daily Reports</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="gallery" value="gallery" <?php if(in_array("gallery",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="gallery">Gallery</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role12" value="companies" <?php if(in_array("companies",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role12">Companies</label>
		                                </div>
		                                <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="whatsapp" value="whatsapp" <?php if(in_array("whatsapp",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="whatsapp">Whatsapp</label>
		                                </div>
		                                <!-- <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role11" value="inquiries" < ?php if(in_array("inquiries",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role11">Inquiries</label>
		                                </div> -->
		                                <!-- <div class="custom-control custom-checkbox checkbox-success form-check">
		                                    <input type="checkbox" class="custom-control-input" name="roles[]" id="role12" value="settings" < ?php if(in_array("settings",$roles)){echo "checked";} ?> />
		                                    <label class="custom-control-label" for="role12">Settings</label>
		                                </div> -->
		                            </div>
		                        </div>
		                    </div>
		           	<?php
		           		}
		           	?>
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-8 col-md-offset-3">
							<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
							<a href="<?php echo $user_type == 0 ? base_url('staffs') : base_url('profile'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-9" id="service-checkbox">
			<div class="tile">
				<div class="tile-body">
					<input type="checkbox" name="all_service" id="all_service" /> All Services
				</div>
			</div>
			<?php 
				if(!empty($service_groups)) {
					foreach($service_groups as $service_group) {
						if(!empty($service_group["services"])) {
			?>
							<div class="tile">
								<div class="tile-header">
									<input type="checkbox" name="service_group[]" id="service_group_<?php echo $service_group['id']; ?>" value="<?php echo $service_group['id']; ?>" data-current="<?php echo $service_group['id']; ?>" <?php if(in_array("service_group_".$service_group['id'],$staff_services_arr)){echo "checked";} ?> /> <?php echo $service_group['name']; ?>
								</div>
								<div class="tile-body">
									<?php
										foreach($service_group["services"] as $service) {
									?>
											<input type="checkbox" name="service[]" id="service_<?php echo $service['id']; ?>" value="<?php echo $service['id']; ?>" data-parent="<?php echo $service_group['id']; ?>" <?php if(in_array($service['id'],$staff_services_arr)){echo "checked";} ?> /> 
											<small><?php echo $service['name']; ?></small><br>
									<?php
										}
										
									?>
								</div>
							</div>
			<?php
						}
					}
				}
			?>
		</div>
	</div>
</form>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/jquery.validate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/additional_methods.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Staffs";
	var is_edit_page = "<?php echo $fname == '' ? 0 : 1; ?>";
	$(document).ready(function(){
		if(is_edit_page == 1) {
			$('#password').rules("remove", "required" );
			$('#cpassword').rules("remove", "required" );
		}
		$("#form").validate({
			rules:{
				fname:{
					required: true
				},
				lname: {
					required: true
				},
				phone:{
					required: true
				},
				email:{
					required: true
				},
				password:{
					required: true
				},
				cpassword:{
					required: true,
					equalTo: "#password"
				}
			},
			messages:{
				fname:{
					required: "<small class='error'><i class='fa fa-warning'></i> First name is required.</small>"
				},
				lname: {
					required: "<small class='error'><i class='fa fa-warning'></i> Last name is required.</small>"
				},
				phone:{
					required: "<small class='error'><i class='fa fa-warning'></i> Mobile no. is required.</small>"
				},
				email:{
					required: "<small class='error'><i class='fa fa-warning'></i> Email is required.</small>"
				},
				password:{
					required: "<small class='error'><i class='fa fa-warning'></i> Password is required.</small>"
				},
				cpassword:{
					required: "<small class='error'><i class='fa fa-warning'></i> Confirm password is required.</small>",
					equalTo: "<small class='error'><i class='fa fa-warning'></i> Password & confirm password must be same.</small>"
				}
			}
		});
		$("#form").submit(function(e){
			e.preventDefault();

			if($("#form").valid()) {
				$.ajax({
					url: $("#form").attr("action"),
					type: $("#form").attr("method"),
					data: new FormData(this),
					processData: false,
					contentType: false,
					dataType: "json",
					beforeSend:function(){
						// $("#form button[type=submit]").attr("disabled",true);
					},
					success:function(response) {
						if(response.status == 1) {
							window.location.href = $("#backbtn").attr("href");
						}
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
		$("#selectall").click(function(){
			if($(this).prop("checked") == true) {
				$("#role-checkbox input[type=checkbox]").prop("checked",true);
			} else {
				$("#role-checkbox input[type=checkbox]").prop("checked",false);
			}
		});
	});
</script>
<?= $this->endSection(); ?>