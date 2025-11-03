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
		$name = $service_name;
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
								<label class="control-label">Service Name</label>
								<input class="form-control" type="text" placeholder="Enter service name" value="<?php echo $name; ?>" disabled />
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label for="colFormLabel" class="control-label">Price Type</label>
                                <select class="form-control" name="price_type" id="price_type">
                                    <option value="">Option</option>
                                    <option value="0" <?php echo $price_type == 0 ? "selected" : ""; ?>>Single</option>
                                    <option value="1" <?php echo $price_type == 1 ? "selected" : ""; ?>>Multiple</option>
                                </select>
							</div>
						</div>
						<div class="col-lg-12">
							<div id="price_structure" style="display: <?php echo $price_structure; ?>">
								<?php
                                if(!empty($json))
                                {
                                    for($i = 0; $i < count($json); $i ++)
                                    {
                            ?>
                                        <div class='price_type_<?php echo $price_type; ?>' name='p_type' id="priceType_<?php echo $json[$i]['id']; ?>">
                                            <div class='row'>
                                                <div class='col-sm-3'>
                                                    <label class='col-md-10 col-sm-4 col-form-label'>
                                                        <?php
                                                            if($price_type == "1") { 
                                                        ?>   
                                                                <a class="" href="javascript:;" onclick="remove_service_price('<?php echo $id; ?>','<?php echo $json[$i]['id']; ?>'); "><i class="la la-trash la-lg" style="padding-bottom: 0px;"></i></a>
                                                        <?php 
                                                            }
                                                        ?>
                                                    Duration (in minute)</label>
                                                    <div class='col-sm-12'>
                                                        <input type='text' class='form-control duration' name='price_duration[]' placeholder='Duration (in minute)' value="<?php echo $json[$i]['duration']; ?>" />
                                                    </div>
                                                </div>

                                                <div class='col-sm-3'>
                                                    <label class='col-md-10 col-sm-4 col-form-label'>Retail Price</label>
                                                    <div class='col-sm-12'>
                                                        <input type='text' class='form-control rprice' name='rprice[]' placeholder='Retail Price' value="<?php echo $json[$i]['retail_price']; ?>" />
                                                    </div>
                                                </div>

                                                <div class='col-sm-3'>
                                                    <label class='col-md-10 col-sm-4 col-form-label'>Special Price</label>
                                                    <div class='col-sm-12'>
                                                        <input type='text' class='form-control sprice' name='sprice[]' placeholder='Special Price' value="<?php echo $json[$i]['special_price']; ?>" />
                                                    </div>
                                                </div>

                                                <div class='col-sm-3'>
                                                    <label class='col-md-10 col-sm-4 col-form-label'>Caption</label>
                                                    <div class='col-sm-12'>
                                                        <input type='text' class='form-control caption' name='caption[]' placeholder='Caption' value="<?php echo $json[$i]['caption']; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                            <?php
                                    }
                                }
                            ?>
							</div><br>
							<a class="btn btn-sm btn-info" style="display: <?php echo $display; ?>" id="add_new_price_type">Add New</a>
						</div><br><br>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="colFormLabel" class="control-label">Extra Time Type</label>
                                <select class="form-control" name="extra_time_type" id="extra_time_type">
                                    <option value="0" <?php echo $extra_time_type == 0 ? "selected" : ""; ?>>No Extra Time</option>
                                    <option value="1" <?php echo $extra_time_type == 1 ? "selected" : ""; ?>>Proccessing Time After</option>
                                    <option value="2" <?php echo $extra_time_type == 2 ? "selected" : ""; ?>>Blocked Time After</option>
                                </select>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">Duration <small>(in minute)</small></label>
								<input class="form-control" type="text" placeholder="Enter duration" name="duration" id="duration" value="<?php echo $duration; ?>" readonly />
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="colFormLabel" class="control-label">Appointment Booked from website</label>
                                <select class="form-control" name="bookedFrom" id="bookedFrom">
                                    <option value="Y" <?php echo $bookedFrom == "Y" ? "selected" : ""; ?>>Yes</option>
                                   	<option value="N" <?php echo $bookedFrom == "N" ? "selected" : ""; ?>>No</option>
                                </select>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">Position</label>
								<input class="form-control" type="number" placeholder="Enter position" name="position" id="position" value="<?php echo $position; ?>" />
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Note</label>
								<textarea class="form-control" placeholder="Enter note" name="note" id="note"><?php echo $note; ?></textarea>
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
				},
				price_type: {
					required: true
				}
			},
			messages:{
				service_group_id:{
					required: "<small class='error'><i class='fa fa-warning'></i> Service group is required</small>"
				},
				name:{
					required: "<small class='error'><i class='fa fa-warning'></i> Service name is required</small>"
				},
				price_type:{
					required: "<small class='error'><i class='fa fa-warning'></i> Price type is required</small>"
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
		$("#price_type").change(function(){
            $("#price_structure").show();
            if($(this).val() === "")
            {
                $("#add_new_row,#add_new_price_type").hide();
                $("div[name^=p_type]").remove();
            } else {
                if(parseInt($(this).val()) == 1)
                {
                    $("#add_new_row,#add_new_price_type").show();
                    $(".price_type_0").remove();
                } else {
                    $("#add_new_row,#add_new_price_type").hide();
                    $(".price_type_1").remove();
                }
                create_divs(parseInt($(this).val()));
            }
        });
        $("#add_new_price_type").click(function(){
        	create_divs(1);
        });
	});
	function create_divs(val)
    {
        var rowIndex = $("#price_structure div[name=p_type]").length;

        var content = "";
        content += "<div class='price_type_"+val+"' name='p_type' id='priceType_"+rowIndex+"'>";
        content += "<div class='row'>";
        content += "<div class='col-sm-3'>";
        content += "<label for='colFormLabel' class='col-md-10 col-sm-4 col-form-label'>";
        if(val == 1)
        {
            content += "<a class='' href='javascript:;' onclick='remove_service_price(0,"+rowIndex+");'><i class='la la-trash la-lg' style='padding-bottom: 0px;'></i></a>&nbsp;";
        }
        content += "Duration (in minute)</label>";
        content += "<div class='col-sm-12'>";
        content += "<input type='text' class='form-control duration' name='price_duration[]' placeholder='Duration (in minute)' /></div>";
        content += "</div>";
        content += "<div class='col-sm-3'>";
        content += "<label class='col-md-10 col-sm-10 col-form-label'>Retail Price</label>";
        content += "<div class='col-sm-12'><input type='text' class='form-control rprice' name='rprice[]' placeholder='Retail Price' /></div>";
        content += "</div>";
        content += "<div class='col-sm-3'>";
        content += "<label class='col-md-12 col-sm-12 col-form-label'>Special Price</label>";
        content += "<div class='col-sm-12'><input type='text' class='form-control sprice' name='sprice[]' placeholder='Special Price' /></div>";
        content += "</div>";
        content += "<div class='col-sm-3'>";
        content += "<label class='col-md-10 col-sm-10 col-form-label'>Caption</label>";
        if(val == 1) { 
            content += "<div class='col-sm-12'><input type='text' class='form-control caption' name='caption[]' placeholder='Caption' /></div>";
        } else {
            content += "<div class='col-sm-12'><input type='text' class='form-control caption' name='caption[]' placeholder='Caption' /></div>";
        }
        content += "</div>";
        content += "</div></div>";
        $("#price_structure").prepend(content);
    }
</script>
<?= $this->endSection(); ?>