<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-dashboard"></i> Daily Reports</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Daily Reports</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-body">
				<form class="form-horizontal">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
							<label>Start Date</label>
							<input type="date" class="form-control" name="sdate" id="sdate" value="<?php echo date('Y-m-d'); ?>" onchange="show_report()" />
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
							<label>End Date</label>
							<input type="date" class="form-control" name="edate" id="edate" value="<?php echo date('Y-m-d'); ?>" onchange="show_report()" />
							<small id="edate-error" style="color: #FF0000;"></small>
						</div>
					</div>
				</form><br>
				<div id="op">
					
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var page_title = "Daily Reports";
	show_report();
	function show_report()
	{
		$.ajax({
			url: "<?php echo base_url('fetch-daily-report'); ?>",
			type: "post",
			data:{
				sdate: $("#sdate").val(),
				edate: $("#edate").val()
			},
			dataType: "json",
			success:function(response){
				if(response.status == 1) {
					$("#edate-error").html("");
					$("#op").html(response.html);
					hide_empty_row();
				} else {
					$("#edate-error").html(response.message);
					$("#op").html("");
				}
			}
		});
	}
	function hide_empty_row()
	{
		$(".service-group-tbl tbody tr").each(function(){
			if($.trim($(this).find("td:eq(1)").text()) == "£ 0") {
				$(this).remove();
			}
		})
	}
</script>
<?= $this->endSection(); ?>