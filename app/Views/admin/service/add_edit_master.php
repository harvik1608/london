<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<?php
	$json = array(); 
	if($service) {
		$page_title = "Edit Service";
		$action = base_url('services/'.$service["id"]);

		$id = $service['id'];
		$serviceId = $service['service_group_id'];
		$name = $service['name'];
		$note = $service['note'];
        $price_type = $service['price_type'];
        $duration = $service['duration'];
        $extra_time_type = $service['extra_time_type'];
		$bookedFrom = $service['bookedFrom'];
        $isActive = $service['is_active'];
        $display = $price_type == "1" ? "block" : "none";
        $price_structure = "block";
        $position = $service["position"];
        if($service['json'] != "")
            $json = json_decode($service['json'],true);
	} else {
		$page_title = "New Service";
		$action = base_url('services');

		$id = 0;
		$serviceId = "";
		$name = "";
		$note = "";
        $price_type = "";
        $duration = "";
        $extra_time_type = "0";
        $bookedFrom = "Y";
        $position = "";
        $note = "";
		$isActive = "1";
        $display = "none";
        $price_structure = "none";
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-dashboard"></i> Services</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('services'); ?>">Services</a></li>
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
								<label for="colFormLabel" class="control-label">Service Group</label>
                                <select class="form-control" name="service_group_id" id="service_group_id" autofocus>
                                    <option value="">Option</option>
                                    <?php
                                        if($service_groups)
                                        {
                                            foreach($service_groups as $key => $val)
                                            {
                                    ?>
                                                <option value="<?php echo $val['id']; ?>" <?php echo $serviceId == $val['id'] ? "selected" : "";?>><?php echo $val['name']; ?></option>
                                    <?php
                                            }
                                        } 
                                    ?>
                                </select>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Service Name</label>
								<input class="form-control" type="text" placeholder="Enter service name" name="name" id="name" value="<?php echo $name; ?>" />
							</div>
						</div>
					</div>
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-8 col-md-offset-3">
							<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
							<a href="<?php echo base_url('services'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
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
	var page_title = "Services";
	var service_group_id = "<?php echo $serviceId; ?>";
	$(document).ready(function(){
		$("#form").validate({
			rules:{
				service_group_id:{
					required: true
				},
				name: {
					required: true
				}
			},
			messages:{
				service_group_id:{
					required: "<small class='error'><i class='fa fa-warning'></i> Service group is required</small>"
				},
				name:{
					required: "<small class='error'><i class='fa fa-warning'></i> Service name is required</small>"
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
						if(response.status == 1 && service_group_id == "")
							window.location.href = $("#backbtn").attr("href");
						else 
							window.location.href = "<?php echo base_url("services"); ?>/"+service_group_id;
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