<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<?php
	$week_days = array();
	$service_group_ids = array();
	$service_ids = array();
	if($discount) {
		$page_title = "Edit Discount Type";
		$action = base_url('discounts/'.$discount["id"]);

		$name = $discount['name'];
		$sdate = $discount['sdate'];
		$edate = $discount['edate'];
		if($discount["week_days"] != "") {
			$week_days = explode(",", $discount["week_days"]);
		}
		$percentage = $discount['percentage'];
		$is_active = $discount['is_active'];
		$is_all_service_checked = $discount['is_all_service_checked'];
		if($discount["service_group_ids"] != "") {
			$service_group_ids = explode(",",$discount["service_group_ids"]);
		}
		if($discount["service_ids"] != "") {
			$service_ids = explode(",",$discount["service_ids"]);
		}
	} else {
		$page_title = "New Weekend Discount";
		$action = base_url('discounts');

		$name = "";
		$sdate = "";
		$edate = "";
		$percentage = "";
		$is_active = "1";
		$is_all_service_checked = 0;
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-dashboard"></i> Weekend Discounts</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('discounts'); ?>">Weekend Discounts</a></li>
		<li class="breadcrumb-item"><a><?php echo $page_title; ?></a></li>
	</ul>
</div>
<form class="form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
	<div class="row">
		<div class="col-md-8">
			<div class="tile">
				<?php
					if($name != "")
						echo '<input type="hidden" name="_method" value="PUT" />'; 
				?>
				<div class="tile-title">
					<h4><?php echo $page_title; ?></h4>
				</div><hr>
				<div class="tile-body">	
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">Name</label>
								<input class="form-control" type="text" placeholder="Enter name" name="name" id="name" value="<?php echo $name; ?>" autofocus />
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group">
								<label class="control-label">Start Date</label>
								<input class="form-control" type="date" name="sdate" id="sdate" value="<?php echo $sdate; ?>" />
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group">
								<label class="control-label">End Date</label>
								<input class="form-control" type="date" name="edate" id="edate" value="<?php echo $edate; ?>" />
							</div>
						</div>
						<div class="col-sm-6">
							<label class="control-label">Week Days</label><br>
							<input type="checkbox" name="week_day[]" id="mon" value="mon" <?php echo in_array("mon",$week_days) ? "checked" : ""; ?> /> Monday
							<input type="checkbox" name="week_day[]" id="tue" value="tue" <?php echo in_array("tue",$week_days) ? "checked" : ""; ?> /> Tuesday
							<input type="checkbox" name="week_day[]" id="wed" value="wed" <?php echo in_array("wed",$week_days) ? "checked" : ""; ?> /> Wednesday
							<input type="checkbox" name="week_day[]" id="thu" value="thu" <?php echo in_array("thu",$week_days) ? "checked" : ""; ?> /> Thrusday
							<input type="checkbox" name="week_day[]" id="fri" value="fri" <?php echo in_array("fri",$week_days) ? "checked" : ""; ?> /> Friday
							<input type="checkbox" name="week_day[]" id="sat" value="sat" <?php echo in_array("sat",$week_days) ? "checked" : ""; ?> /> Saturday<br>
							<input type="checkbox" name="week_day[]" id="sun" value="sun" <?php echo in_array("sun",$week_days) ? "checked" : ""; ?> /> Sunday<br>
							<label id="week_day[]-error" class="error" for="week_day[]" style="display: none;"></label>
                       	</div>
                       	<div class="col-lg-3">
							<div class="form-group">
								<label class="control-label">Percentage (%)</label>
								<input class="form-control" type="number" name="percentage" id="percentage" value="<?php echo $percentage; ?>" />
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group">
								<label class="control-label">Status</label>
								<select class="form-control" name="is_active" id="is_active">
									<option value="1" <?php echo $is_active == '1' ? "selected" : ""; ?>>Active</option>
									<option value="0" <?php echo $is_active == '0' ? "selected" : ""; ?>>Inactive</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-8 col-md-offset-3">
							<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
							<a href="<?php echo base_url('discounts'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4" id="service-checkbox">
			<div class="tile">
				<div class="tile-body">
					<input type="checkbox" name="all_service" id="all_service" <?php echo $is_all_service_checked == 1 ? "checked" : ""; ?> /> All Services
				</div>
			</div>
			<?php 
				if(!empty($service_groups)) {
					foreach($service_groups as $service_group) {
						if(!empty($service_group["services"])) {
			?>
							<div class="tile">
								<div class="tile-header">
									<input type="checkbox" name="service_group[]" id="service_group_<?php echo $service_group['id']; ?>" value="<?php echo $service_group['id']; ?>" data-current="<?php echo $service_group['id']; ?>" <?php echo in_array($service_group['id'],$service_group_ids) ? "checked" : ""; ?> /> <b><u><?php echo $service_group['name']; ?></u></b>
								</div>
								<div class="tile-body">
									<?php
										foreach($service_group["services"] as $service) {
									?>
											<input type="checkbox" name="service[]" id="service_<?php echo $service['id']; ?>" value="<?php echo $service['id']; ?>" data-parent="<?php echo $service_group['id']; ?>"  <?php echo in_array($service['id'],$service_ids) ? "checked" : ""; ?> /> 
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
	var page_title = "Weekday Discounts";
	$(document).ready(function(){
		$("#form").validate({
			rules:{
				name: {
					required: true
				},
				sdate:{
					required: true
				},
				edate: {
					required: true
				},
				"week_day[]":{
					required: true
				},
				percentage:{
					required: true
				}
			},
			messages:{
				name:{
					required: "<small class='error'><i class='fa fa-warning'></i> Name is required</small>"
				},
				sdate:{
					required: "<small class='error'><i class='fa fa-warning'></i> Start Date is required</small>"
				},
				edate:{
					required: "<small class='error'><i class='fa fa-warning'></i> End Date is required</small>"
				},
				"week_day[]":{
					required: "<small class='error'><i class='fa fa-warning'></i> Week Day is required</small>"
				},
				percentage:{
					required: "<small class='error'><i class='fa fa-warning'></i> Percentage is required</small>"
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
		$(document).on("click","input[id^=service_]",function(){
			var isAtleastOneChecked = 0;
			var current_id = $.trim($(this).parents(".tile").find(".tile-header input").attr("data-current"));
			if($("input[data-parent="+current_id+"]").length > 0) {
				$("input[data-parent="+current_id+"]").each(function(){
					if($(this).prop("checked") == true) {
						isAtleastOneChecked = 1;
					}
				});
			}
			if(isAtleastOneChecked == 1) {
				$(this).parents(".tile").find(".tile-header input").prop("checked",true);
			} else {
				$(this).parents(".tile").find(".tile-header input").prop("checked",false);
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