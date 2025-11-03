<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<style>
	label {
		font-weight: bold;
	}
</style>
<?php
	$json              = array(); 
	$action            = base_url("new-service-price/".$service_id);
	$price_type        = isset($price['price_type']) ? $price['price_type'] : "";
	$duration          = isset($price['duration']) ? $price['duration'] : "";
	$display           = isset($price['price_type']) && $price['price_type'] == 1 ? "block" : "none";
	$price_structure   = "block";
	$extra_time_type   = isset($price['extra_time_type']) ? $price['extra_time_type'] : "0";
    $bookedFrom        = isset($price['bookedFrom']) ? $price['bookedFrom'] : "Y";
    $position          = isset($price['position']) ? $price['position'] : "";
    $note               = isset($price['note']) ? $price['note'] : "";
    $id = $service_id;
    if(isset($price['json']) && !empty($price['json'])) {
        $json = json_decode($price['json'],true);
    }
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-dashboard"></i> Services</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Services</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<form class="form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
			<div class="tile">
				<div class="tile-title">
					<h4><i><u><?php echo ucwords($service_name); ?></u></i></h4>
				</div>
				<div class="tile-body">
					<div class="row">
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
								<label class="control-label">Duration <small><b>(in minute)</b></small></label>
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
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	var page_title = "Services";
	$(document).ready(function(){
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