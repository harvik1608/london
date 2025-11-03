<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<?php 
	if($gallery) {
		$page_title = "Edit Payment Type";
		$action = base_url('payment_types/'.$payment_type["id"]);

		$position = $payment_type["position"];
		$is_active = $payment_type["is_active"];
	} else {
		$page_title = "New Photo";
		$action = base_url('photos');

		$position = "";
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-photo"></i> Gallery</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('photos'); ?>">Gallery</a></li>
		<li class="breadcrumb-item"><a><?php echo $page_title; ?></a></li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<form class="form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
				<?php
					if($position != "")
						echo '<input type="hidden" name="_method" value="PUT" />'; 
				?>
				<div class="tile-title">
					<h4><?php echo $page_title; ?></h4>
				</div><hr>
				<div class="tile-body">	
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Photo <small>(480x430)</small></label>
								<input class="form-control" type="file" name="avatar" id="avatar" required />
							</div>
						</div>
						<?php
							if($position != "") {
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
					</div>
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-8 col-md-offset-3">
							<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
							<a href="<?php echo base_url('photos'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
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
	var page_title = "Gallery";
	$(document).ready(function(){
		$("#form").submit(function(e){
			e.preventDefault();

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
		});
	});
</script>
<?= $this->endSection(); ?>