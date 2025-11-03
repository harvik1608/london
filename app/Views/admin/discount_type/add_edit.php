<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<?php 
	if($discount_type) {
		$page_title = "Edit Discount Type";
		$action = base_url('discount_types/'.$discount_type["id"]);

		$name = $discount_type['name'];
        $discount_ty = $discount_type['discount_type'];
        $discount_value = $discount_type['discount_value'];
		$is_active = $discount_type['is_active'];
	} else {
		$page_title = "New Discount Type";
		$action = base_url('discount_types');

		$name = "";
        $discount_ty = "";
        $discount_value = "";
		$is_active = "1";
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-users"></i> Discount Types</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('discount_types'); ?>">Discount Types</a></li>
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
						<div class="col-lg-4">
							<div class="form-group">
								<label class="control-label">Discount Name</label>
								<input class="form-control" type="text" placeholder="Enter discount name" name="name" id="name" value="<?php echo $name; ?>" autofocus />
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label class="control-label">Discount Type</label>
								<select class="form-control" name="discount_type" id="discount_type">
									<option value="">Option</option>
									<option value="0" <?php echo $discount_ty == '0' ? "selected" : ""; ?>>Fixed</option>
									<option value="1" <?php echo $discount_ty == '1' ? "selected" : ""; ?>>Percentage</option>
								</select>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label class="control-label">Discount Value</label>
								<input class="form-control" type="text" placeholder="Enter discount value" name="discount_value" id="discount_value" value="<?php echo $discount_value; ?>" />
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
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-8 col-md-offset-3">
							<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
							<a href="<?php echo base_url('discount_types'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
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
	var page_title = "Discount Types";
	$(document).ready(function(){
		$("#form").validate({
			rules:{
				name: {
					required: true
				},
				discount_type:{
					required: true
				},
				discount_value: {
					required: true
				}
			},
			messages:{
				name:{
					required: "<small class='error'><i class='fa fa-warning'></i> Discount Name is required</small>"
				},
				discount_type:{
					required: "<small class='error'><i class='fa fa-warning'></i> Discount Type is required</small>"
				},
				discount_value:{
					required: "<small class='error'><i class='fa fa-warning'></i> Discount Value is required</small>"
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