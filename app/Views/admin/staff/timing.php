<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<link rel="stylesheet" href="<?php echo base_url('public/admin/js/vendor/select2/select2.min.css'); ?>">
<style>
    .select2-container--default {
        width: 100% !important;
    }
    .select2-selection--single {
        height: 38px !important;
        border: 2px solid #ced4da !important;
    }
    #select2-shift_stime-container, #select2-shift_etime-container {
        position: relative;
        top: 2px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 6px !important;
        right: 2px !important;
    }
</style>
<div class="app-title">
	<div>
		<h1><i class="fa fa-clock-o"></i> Staff Timings</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Staff Timings</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>
					Staff Timing List (ROTA)
					<div class="pull-right">
						<a href="javascript:;" class="text-white btn btn-success btn-sm pt-2" onclick="get_next_days('prev');">Prev</a>
						<a href="javascript:;" class="text-white btn btn-success btn-sm pt-2" onclick="get_next_days('today');">Today</a>
						<a href="javascript:;" class="text-white btn btn-success btn-sm pt-2" onclick="get_next_days('next');">Next</a>
					</div>
                    <div class="clearfix"></div>
				</h4>
			</div><hr>
			<div class="tile-body">
				<div id="timing_op"></div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="addTimingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="needs-validation" action="<?php echo base_url('new-timing'); ?>" method="post" id="staffTimingForm" autocomplete="off">
                <input type="hidden" name="timing_uid" id="timing_uid" />
                <input type="hidden" name="staff_timing_id" id="staff_timing_id" />
                <input type="hidden" name="staff_timing_dt" id="staff_timing_dt" />
                <input type="hidden" name="staff_timing_ts" id="staff_timing_ts" />
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close" onclick="closeAddTimingModal();">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <label for="validationCustom01">Shift Start</label><br>
                            <select class="form-control" name="shift_stime" id="shift_stime">
                                <?php
                                    echo timepicker(format_date(9,$company['company_stime']),format_date(9,$company['company_etime'])); 
                                ?>
                            </select>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <label for="validationCustom01">Shift End</label>
                            <select class="form-control" name="shift_etime" id="shift_etime">
                                <?php
                                    echo timepicker(format_date(9,$company['company_stime']),format_date(9,$company['company_etime'])); 
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row repeat">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <label for="validationCustom01">Repeats</label>
                            <select class="form-control" id="shift_repeat" name="shift_repeat">
                                <option value="N">Don't Repeat</option>
                                <option value="Y">This Week</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Add</button>
                    <a class="btn btn-danger text-white" onclick="remove_staff_timing();" style="display: none;">Delete</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="show_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="needs-validation" action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo company_info("","company_name"); ?></h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">

                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url('public/calendar/vendor/moment/min/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('public/admin/js/vendor/select2/select2.min.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Staff Timings";
	$(document).ready(function(){
        $("#staffTimingForm").submit(function(e){
            e.preventDefault();

            $.ajax({
                url: $("#staffTimingForm").attr("action"),
                type: 'post',
                dataType: 'json',
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                beforeSend:function(){
                    $("#staffTimingForm button[type=submit]").prop("disabled",true).html("Loading...");
                },
                success:function(response){
                    $("#staffTimingForm button[type=submit]").prop("disabled",false).html("Add");
                    $("#staffTimingForm button[type=submit]").removeClass("btn-warning");
                    $("#staffTimingForm button[type=submit]").addClass("btn-success");
                    if(response.status == 1)
                    {
                        get_timing_grid($("#timingTbl thead tr th:eq(1)").attr("id"),1);
                        $("#shift_repeat").val($("#shift_repeat option:first").val());
                        $("#timing_uid").val("");
                        $("#addTimingModal").modal('hide');
                    } else {
                        alert('Error',response.message,'error');
                    }
                }
            });
        });
        get_timing_grid();
    });
    function get_next_days(caption)
    {
        var sdate = $("#timingTbl thead tr th:eq(7)").attr("id");
        if(caption == "prev")
            sdate = $("#timingTbl thead tr th:eq(1)").attr("id");
        else if(caption == "today") {
            sdate = "";
            caption = "next";
        }
        get_timing_grid(sdate,0,caption);
    }
    function get_timing_grid(date = "",addDay = 0,sign = "next")
    {
        $.ajax({
            url: "<?php echo base_url('get-timing-grid'); ?>",
            type: 'post',
            data:{
                sdate: date,
                addDay: addDay,
                sign: sign
            },
            success:function(response){
                $("#timing_op").html(response);
                var hours = 0;
                $("#timingTbl tbody tr").each(function(){
                    hours = hours + parseInt($(this).find("td:last").text());
                });
                $("#total_staff_hours").html("<b>TOTAL STAFF HOURS: "+hours+"</b>");
            }
        });
    }
    function remove_staff_timing()
    {
        if(confirm("Are you sure to remove this?")) {
        	$.ajax({
                url: "<?php echo base_url('remove-staff-timing'); ?>",
                type: 'post',
                dataType: 'json',
                data:{timing_id:$("#timing_uid").val()},
                success:function(response){
                    if(response.status == 1)
                    {
                    	// alert(response.message);
                        // show_toast('Success',response.message,'success');
                        $("#addTimingModal").modal("hide");
                        // get_timing_grid();
                        get_timing_grid($("#timingTbl thead tr th:eq(1)").attr("id"),1);
                    } else {
                    	alert(response.message);
                        // show_toast('Error',response.message,'error');
                    }
                }
            });
        }
    }
    function add_timing(staffId,date,title,timestamp,timingId,isRepeat,col_title)
    {
        $("#addTimingModal #exampleModalLabel").html(title);
        $("#staff_timing_id").val(staffId);
        $("#staff_timing_dt").val(date);
        $("#staff_timing_ts").val(timestamp);
        $("#timing_uid").val(timingId);
        var time = col_title.split("<br>");
        var stime_col = time[0].split(":");
        if (typeof stime_col[2] !== "undefined") {
            $("#shift_stime").val(time[0]);
            $("#shift_etime").val(time[2]);
        } else {
            $("#shift_stime").val(time[0]+":00");
            $("#shift_etime").val(time[2]+":00");
        }
        if(timingId != 0)
        {
            if(isRepeat == "N")
                $("#shift_repeat option:eq(0)").prop("selected",true);
            else
                $("#shift_repeat option:eq(1)").prop("selected",true);

            $("#staffTimingForm a.btn-danger").show();
            $("#staffTimingForm button[type=submit]").text("Update");
            $("#staffTimingForm button[type=submit]").removeClass("btn-success");
            $("#staffTimingForm button[type=submit]").addClass("btn-warning");
            $(".repeat").hide();
        } else {
            $("#staffTimingForm a.btn-danger").hide();
            $("#staffTimingForm button[type=submit]").text("Add");
            $("#staffTimingForm button[type=submit]").removeClass("btn-warning");
            $("#staffTimingForm button[type=submit]").addClass("btn-success");
            $(".repeat").show();
        }
        // $("#shift_stime").select2({
        //     tags: true,
        //     dropdownParent: $("#addTimingModal"),
        //     placeholder: "Shift Start",
        //     livesearch: true
        // });
        // $("#shift_etime").select2({
        //     tags: true,
        //     dropdownParent: $("#addTimingModal"),
        //     placeholder: "Shift End",
        //     livesearch: true
        // });
        $("#addTimingModal").modal({
            backdrop: 'static',
            keyboard: false
        });
    }
    function closeAddTimingModal()
    {
        $("#shift_stime,#shift_etime").val("");
        $("#shift_repeat").val($("#shift_repeat option:first").val());  
    }
    function show_report()
    {
        var days = [];
        $("#timingTbl thead tr th").each(function(){
            if(typeof $(this).attr("id") !== "undefined") {
                days.push({"date":$(this).attr("id")});
            }
        });
        $.ajax({
            url: "<?php echo base_url('get-weekly-time-report'); ?>",
            type: 'post',
            data:{
                dates: days
            },
            success:function(response){
                $("#show_report_modal .modal-body").html(response);
                $("#show_report_modal").modal("show");
                fill_empty_td();
            }
        });
    }
    function fill_empty_td()
    {
        let tds = [];
        $("#week-tbl tbody tr").each(function(){
            tds.push($(this).find("td").length);
        });
        var max = Math.max(...tds);
        $("#week-tbl tbody tr").each(function(){
            if($(this).find("td").length < max) {
                var k = max - $(this).find("td").length;
                for(var i = 1; i <= k; i ++) {
                    $(this).append("<td align='center' valign='middle'>-</td>");
                }
            }
        });
    }
</script>
<?= $this->endSection(); ?>