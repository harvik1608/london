<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<?php
	if($service_group) {
		$page_title = "Edit Service Group";
		$action = base_url('service_groups/'.$service_group["id"]);

		$name = $service_group["name"];
		$color = $service_group["color"];
		$note = $service_group["note"];
		$position = $service_group["position"];
		$is_active = $service_group["is_active"];
		$old_avatar = $service_group["avatar"];
	} else {
		$page_title = "New Service Group";
		$action = base_url('service_groups');

		$name = "";
		$color = "";
		$note = "";
		$position = "";
		$is_active = "1";
		$old_avatar = "";
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-dashboard"></i> Service Groups</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('service_groups'); ?>">Service Groups</a></li>
		<li class="breadcrumb-item"><a><?php echo $page_title; ?></a></li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<form class="form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
				<input type="hidden" name="old_avatar" value="<?php echo $old_avatar; ?>" />
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
								<label class="control-label">Service Group Name*</label>
								<input class="form-control" type="text" placeholder="Enter service group name" name="name" id="name" value="<?php echo $name; ?>" />
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">Color</label>
								<input class="form-control" type="color" name="color" id="color" value="<?php echo $color; ?>" />
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Description</label>
								<textarea class="form-control" name="note" id="note" placeholder="Enter description"><?php echo $note; ?></textarea>
							</div>
						</div>
						<?php
							if($name != "") {
						?>
								<div class="col-lg-6">
									<div class="form-group">
										<label class="control-label">Position</label>
										<input class="form-control" min="1" type="number" name="position" id="position" value="<?php echo $position; ?>" />
									</div>
								</div>
						<?php
							}
						?>
						<div class="col-lg-<?php echo $name == '' ? '12' : '6'; ?>">
							<div class="form-group">
								<label class="control-label">Status</label>
								<select class="form-control" name="is_active" id="is_active">
									<option value="1" <?php echo $is_active == '1' ? "selected" : ""; ?>>Active</option>
									<option value="0" <?php echo $is_active == '0' ? "selected" : ""; ?>>Inactive</option>
								</select>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Photo*</label>
								<input type="file" class="form-control" name="avatar" id="avatar" />
								<?php
									if($old_avatar != "") {
										echo '<br><img src="'.base_url('public/uploads/service_group/'.$old_avatar).'" class="img img-responsive img-thumbnail" style="width: 125px;height: 125px;" />';
									} 
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-8 col-md-offset-3">
							<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
							<a href="<?php echo base_url('service_groups'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/jquery.validate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/additional_methods.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Service Groups";
	var name = "<?php echo $name; ?>";
	$(document).ready(function(){
		$("#form").validate({
			rules:{
				name:{
					required: true
				},
				color: {
					required: true
				},
				avatar:{
					required: function () {
		                return $.trim($("#name").val()) === ""; // Only required if name is blank
		            }
				}
			},
			messages:{
				name:{
					required: "<small class='error'><i class='fa fa-warning'></i> Service group name is required</small>"
				},
				color:{
					required: "<small class='error'><i class='fa fa-warning'></i> Color is required</small>"
				},
				avatar:{
					required: "<small class='error'><i class='fa fa-warning'></i> Photo is required</small>"
				}
			}
		});
		if($.trim(name) != "") {
			
		}
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
	});
</script>
<?= $this->endSection(); ?>