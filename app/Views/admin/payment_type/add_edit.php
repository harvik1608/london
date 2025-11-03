<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<?php 
	if($payment_type) {
		$page_title = "Edit Payment Type";
		$action = base_url('payment_types/'.$payment_type["id"]);

		$name = $payment_type["name"];
		$position = $payment_type["position"];
		$is_active = $payment_type["is_active"];
	} else {
		$page_title = "New Payment Type";
		$action = base_url('payment_types');

		$name = "";
		$position = "";
		$is_active = "1";
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-users"></i> Payment Types</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('payment_types'); ?>">Payment Types</a></li>
		<li class="breadcrumb-item"><a><?php echo $page_title; ?></a></li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<form class="form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
				<?php
					if($name != "")
						echo '<input type="hidden" name="_method" value="PUT" />'; 
				?>
				<div class="tile-title">
					<h4><?php echo $page_title; ?></h4>
				</div><hr>
				<div class="tile-body">	
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Payment Name</label>
								<input class="form-control" type="text" placeholder="Enter payment name" name="name" id="name" value="<?php echo $name; ?>" autofocus />
							</div>
						</div>
						<?php
							if($name != "") {
						?>
								<div class="col-lg-12">
									<div class="form-group">
										<label class="control-label">Position</label>
										<input class="form-control" type="number" placeholder="Enter position" name="position" id="position" value="<?php echo $position; ?>" />
									</div>
								</div>	
						<?php 
							}
						?>					
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
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-8 col-md-offset-3">
							<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
							<a href="<?php echo base_url('payment_types'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
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
	var page_title = "Payment Types";
	$(document).ready(function(){
		$("#form").validate({
			rules:{
				name: {
					required: true
				}
			},
			messages:{
				name:{
					required: "<small class='error'><i class='fa fa-warning'></i> Payment Name is required</small>"
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
	});
</script>
<?= $this->endSection(); ?>