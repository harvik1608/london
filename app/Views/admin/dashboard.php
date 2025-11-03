<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<link rel="stylesheet" href="<?php echo base_url('public/admin/css/priority-nav-scroller.css'); ?>">
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
	.modal-content {
		width: 150%;
		left: -190px;
	}
    .modal { 
        overflow: auto !important; 
    }
    .appointment_cart_price,.appointment_cart_time {
        font-size: 11px !important;
    }
    .fc-event {
        font-size: 15px !important; /* Change to your preferred size */
        font-weight: bold;          /* Optional: makes text bolder */
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 37px !important;
        border: 2px solid #ced4da !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-top: 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        margin-top: 5px !important;
    }
    .customer_history_info_parent {
        height: 500px !important;
        overflow-y: auto;
        display: block;
    }
    #customer_hints, #walkin_customer_hints {
        max-height: 75px;
        overflow-y: auto;
        display: none;
    }
    #customer_hints.show, #walkin_customer_hints.show {
        display: block;
    }
    #customer_name_hints, #walkin_customer_name_hints {
        max-height: 75px;
        overflow-y: auto;
        display: none;
    }
    #customer_name_hints.show, #walkin_customer_name_hints.show {
        display: block;
    }
    .fc-bgevent.unavailable-slot {
        background-color: #000 !important;   
    }
    .fc-bgevent.available-slot-line {
        background: #fff !important;  
        border-top: 2px solid #28a745 !important; 
        border-bottom: none !important;
    }

    .fc-bgevent.available-slot {
        background-color: #fff !important; 
        border: none !important;
        color: #fff !important; 
    }
    .fc-time-grid-event.fc-short .fc-time:before {
        display: none;
    }
    #save_checkout_btn {
        display: none;
    }
</style>
<!--<div class="app-title">-->
<!--	<div>-->
		
<!--	</div>-->
<!--	<ul class="app-breadcrumb breadcrumb">-->
<!--		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>-->
<!--		<li class="breadcrumb-item"><a href="#">Dashboard</a></li>-->
<!--	</ul>-->
<!--</div>-->
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="tile">
			<div class="tile-body">
			    <div class="row">
                    <div class="col-lg-3">
                        <p><input type="date" id="go_to_date" value="<?php echo date('Y-m-d'); ?>" /></p>
                    </div>
                    <div class="col-lg-3">
                        <div class="custom-control custom-checkbox checkbox-success form-check">
                            <input type="checkbox" class="custom-control-input" id="checkout-appointment" <?php echo $checkout_appointment == 1 ? "checked" : ""; ?> />
                            <label class="custom-control-label" for="checkout-appointment"><b>Checkout Appointments</b></label>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="custom-control custom-checkbox checkbox-success form-check">
                            <input type="checkbox" class="custom-control-input" id="pending-appointment" <?php echo $pending_appointment == 1 ? "checked" : ""; ?> />
                            <label class="custom-control-label" for="pending-appointment"><b>Pending Appointments</b></label>
                        </div>  
                    </div>
                    <div class="col-lg-3">
                        <p style="float: right;"><a class="btn btn-sm btn-success text-white" href="javascript:;" onclick="open_walkin()">Walkin</a></p>
                    </div>
                </div>
				<div id="calendar"></div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="needs-validation" action="<?php echo base_url('new_timing'); ?>" method="post" id="appointmentForm" autocomplete="off">
                <input type="hidden" name="uniq_id" id="uniq_id" />
                <input type="hidden" name="appointmentID" id="appointmentID" />
                <input type="hidden" name="resourceID" id="resourceID" />
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalLabel">New Appointment</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close" onclick="clear_appointment();">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                	<div class="row">
                		<div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-2">
                            <label for="validationCustom01">Date</label>
                            <input type="date" class="form-control" name="appointment_date" id="appointment_date" placeholder="Appointment Date" onchange="change_appointment_date(1,this.value);" />               
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-2">
                            <label for="validationCustom01">Time</label>
                            <select class="form-control" name="appointment_time" id="appointment_time" onchange="change_appointment_date(0,this.value);">
                                <option value="">Time</option>
                                <?php
                                    echo timepicker(format_date(9,$company['company_stime']),format_date(9,$company['company_etime'])); 
                                ?>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-2">
                            <label for="validationCustom01">Phone<span class="error">*</span>&nbsp;&nbsp;<a style="float: right;" href="javascript:;" onclick="get_customer_history()"><small style="font-size: 10px;">View Customer History</small></a></label><br>
                            <input type="text" class="form-control select2-ajax" name="customer_phone" id="customer_phone" placeholder="Customer phone" onkeyup="get_customer_info(this.value,0,'phone');" />
                            <!--<select class="form-control select2-ajax" name="customer_phone" id="customer_phone">-->
                            <!--    <option value="">Search phone</option>-->
                            <!--</select>-->
                            <div id="customer_hints"></div>               
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-3">
                            <label for="validationCustom01">
                                <!-- <a title="Report Card" onclick="get_report_card();" href="javascript:;"><i class="zmdi zmdi-assignment-account zmdi-hc-fw"></i></a>
                                <a title="Customer History" id="customer_history" onclick="get_customer_history()" href="javascript:;"><i class="la la-history"></i></a> -->
                                Name<span class="error">*</span>
                            </label>
                            <input type="text" class="form-control" name="customer_name" id="customer_name" placeholder="Customer name" onkeyup="get_customer_info(this.value,0,'name');" />
                            <!--<select class="form-control select2-ajax" name="customer_name" id="customer_name">-->
                            <!--    <option value="">Search phone</option>-->
                            <!--</select>-->
                            <div id="customer_name_hints"></div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-3">
                            <label for="validationCustom01">Email</label>
                            <input type="text" class="form-control" name="customer_email" id="customer_email" placeholder="Customer email" />
                        </div>
                    </div>
                	<div class="row mt-3">
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-2">
                            <label for="validationCustom01">Booked From</label>
                            <select class="form-control" name="bookedFrom" id="bookedFrom">
                                <option value="3">Salon</option>
                                <option value="1">Online</option>
                                <option value="2">Treatwell</option>
                            </select>               
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-3">
                            <label for="validationCustom01"><small>Total Appointments</small></label>
                            <input class="form-control" name="total_app" id="total_app" disabled />
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-3">
                            <label for="validationCustom01"><small>Total No Show Appointments</small></label>
                            <input class="form-control" name="no_show_app" id="no_show_app" disabled />
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
                            <label for="validationCustom01">Note</label>
                            <input class="form-control" name="customer_note" id="customer_note" placeholder="Note" />
                        </div>
                	</div><br>
                    <?php
                        $services = get_service_groups(static_company_id());
                        if(!empty($services)) {
                    ?>
                            <div class="nav-scroller" style="padding-bottom: 10px;">
                                <nav class="nav-scroller-nav">
                                    <div class="nav-scroller-content">        
                                        <?php
                                            foreach($services as $service)
                                            {
                                        ?>
                                                <a href="#" class="nav-scroller-item" onclick="get_sub_services('<?php echo $service['id']; ?>','<?php echo $service['name']; ?>')"><?php echo $service['name']; ?></a>
                                        <?php
                                            } 
                                        ?>
                                    </div>
                                </nav>
                                <button class="nav-scroller-btn nav-scroller-btn--left" aria-label="Scroll left" type="button"><</button>
                                <button class="nav-scroller-btn nav-scroller-btn--right" aria-label="Scroll right" type="button">></button>
                            </div>
                    <?php
                        }
                    ?>
                    <hr>
                    <div class="row">
                        <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div id="sub_service_list" style="height: 500px;overflow: auto;">
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div id="cart_list" style="display: none;height: 500px;overflow: auto;">
                                <h5>
                                    <b>Total Amount : <?php echo $currency; ?> <span>0</span></b>
                                    <div class="custom-control custom-checkbox checkbox-success form-check" style="display: none;">
                                        <input type="checkbox" class="custom-control-input" name="showbusystaff" id="showbusystaff" value="1" onclick="change_appointment_date(1,this.value);" />
                                        <label class="custom-control-label" for="showbusystaff">Show Busy Staff</label>
                                    </div>
                                </h5>
                                <table class="table table-bordered" style="width:100%" id="cart-tbl">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Service</th>
                                            <th style="width: 45%;">Staff</th>
                                            <th style="width: 20%;">Time</th>
                                            <th style="width: 5%;">Amt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <div class="pull-right">
                                    <button class="btn btn-primary" type="submit">Add</button>
                                    <button class="btn btn-warning" type="button" id="save_checkout_btn">Save & Checkout</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="alert alert-warning" id='appointment_error' style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="viewAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <input type="hidden" id="is_checkout_summary_open" value="0" />
            <form class="needs-validation" action="" method="post" id="viewAppointmentForm">
                <div class="modal-header">
                    <h5 class="modal-title">View Appointment</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body" id="view_appointment_info">
                </div>
                <div class="modal-footer">
                    <textarea name="salon_note_view" id="salon_note_view" style="display: none;" class="form-control" placeholder="Enter your note..."></textarea>
                    <button class="btn btn-primary btn-sm text-white" type="button" id="closeViewAppointement">Close</button>
                    <a class="btn btn-success btn-sm text-white" id="checkoutBtn" style="display: none;">Checkout</a>
                    <a class="btn btn-warning btn-sm text-white" id="editAppointment" style="display: none;">Edit</a>
                    <a class="btn btn-danger btn-sm text-white" id="removeAppointment" style="display: none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="needs-validation" action="" method="post" id="checkoutForm">
                <div class="modal-header">
                    <h5 class="modal-title">Checkout</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body" id="checkout_appointment">
                </div>
                <div class="modal-footer">
                    <textarea name="salon_note" id="salon_note" class="form-control" placeholder="Enter your note..."></textarea>
                    <a class="btn btn-primary btn-sm text-white" id="backViewAppointment" style="padding: 9px;">< Back</a>
                    <a class="btn btn-danger btn-sm text-white add-padding-top" id="noShowAppointment" style="padding: 9px;">No Show</a>
                    <a class="btn btn-success btn-sm text-white add-padding-top" id="completeBtn" style="padding: 9px;">Complete</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="walkinModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="needs-validation" action="" method="post" id="walkinForm">
                <input type="hidden" name="walkin_uniq_id" id="walkin_uniq_id" />
                <div class="modal-header">
                    <h5 class="modal-title">Walkin</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close" onclick="clear_walkin();">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-xl-2 col-lg-12 col-md-12 col-sm-12 col-12">
                            <label for="validationCustom01">Date</label>
                            <input type="text" class="form-control" name="walkin_date" id="walkin_date" placeholder="walkin Date" readonly />               
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-2">
                            <label for="validationCustom01">Time<span class="error">*</span></label>
                            <input type="text" class="form-control" name="walkin_time" id="walkin_time" readonly />
                        </div>
                        <div class="col-xl-2 col-lg-12 col-md-12 col-sm-12 col-12">
                            <label for="validationCustom01">Phone<span class="error">*</span>&nbsp;&nbsp;<a style="float: right;" href="javascript:;" onclick="get_customer_history(1)"><small style="font-size: 10px;">View Customer History</small></a></label>
                            <input type="text" class="form-control" name="walkin_phone" id="walkin_phone" placeholder="Customer phone" onkeyup="get_customer_info(this.value,1);" autocomplete="off" />
                            <div id="walkin_customer_hints"></div>               
                        </div>
                        <div class="col-xl-2 col-lg-12 col-md-12 col-sm-12 col-12">
                            <label for="validationCustom01">Name<span class="error">*</span></label>
                            <input type="text" class="form-control" name="walkin_name" id="walkin_name" placeholder="Customer name" onkeyup="get_customer_info(this.value,1,'name');" />
                            <div id="walkin_customer_name_hints"></div>                 
                        </div>
                        <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                            <label for="validationCustom01">Email</label>
                            <input type="text" class="form-control" name="walkin_email" id="walkin_email" placeholder="Customer email" />               
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-3">
                            <label for="validationCustom01"><small>Total Appointments</small></label>
                            <input class="form-control" name="walkin_total_app" id="walkin_total_app" disabled />
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-3 col-3">
                            <label for="validationCustom01"><small>Total No Show Appointments</small></label>
                            <input class="form-control" name="walkin_no_show_app" id="walkin_no_show_app" disabled />
                        </div>
                        <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
                            <label for="validationCustom01">Note</label>
                            <input class="form-control" name="walkin_note" id="walkin_note" placeholder="Note" />
                        </div>
                    </div>
                    <?php
                        $session = session();
                        $groups = get_service_groups(static_company_id());
                        if($groups)
                        {
                    ?>
                            <div class="nav-scroller" style="padding-bottom: 10px;">
                                <nav class="nav-scroller-nav">
                                    <div class="nav-scroller-content">        
                                        <?php
                                            foreach($groups as $group)
                                            {
                                        ?>
                                                <a href="#" class="nav-scroller-item" onclick="get_sub_services('<?php echo $group['id']; ?>','<?php echo $group['name']; ?>','1')"><?php echo format_text(1,$group['name']); ?></a>
                                        <?php
                                            } 
                                        ?>
                                    </div>
                                </nav>
                                <button class="nav-scroller-btn nav-scroller-btn--left" aria-label="Scroll left" type="button"><</button>
                                <button class="nav-scroller-btn nav-scroller-btn--right" aria-label="Scroll right" type="button">></button>
                            </div>
                            <hr>
                    <?php
                        }
                        $discount_types = getDiscountList(static_company_id());
                        $payment_types  = getPaymentList(static_company_id());
                    ?>
                    <hr>
                    <div class="row">
                        <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div id="walkin_sub_service_list" style="height: 500px;overflow-y: auto;">
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div id="walkin_cart_list" style="height: 500px;overflow-y: auto;display: none;">
                                <h5><b>Total Amount : <?php echo $currency; ?> <span>0</span></b></h5>
                                <table class="table table-bordered" style="width:100%" id="walkin_cart_list">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">Service</th>
                                            <th style="width: 35%;">Staff</th>
                                            <th style="width: 20%;"><strike>Time</strike></th>
                                            <th style="width: 10%;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" align="right">Total :</td>
                                            <td align="right"><?php echo $currency; ?> <span>0</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="row">
                                                    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                                        <small>Discount Type</small>
                                                        <select class="form-control" name="walkin_discount_type" id="walkin_discount_type" onchange="get_discount_type(this.value,1)">
                                                            <option value="">Option</option>
                                                        <?php
                                                            if(!empty($discount_types)){
                                                                foreach($discount_types as $dtype){
                                                        ?>
                                                                    <option value="<?php echo $dtype['id']."_".$dtype['discount_type']."_".$dtype['discount_value']; ?>">       <?php echo $dtype['name']; ?>
                                                                    </option>
                                                        <?php
                                                                }
                                                            } 
                                                        ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                                        <small>Discount Amount</small>
                                                        <input type="number" readonly="" placeholder="Amount" class="form-control" id="walkin_discounted_amt" name="walkin_discounted_amt" />
                                                    </div>
                                                    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                                        <small>Extra Discount</small>
                                                        <input type="number" min="0" placeholder="Amount" class="form-control" id="walkin_extra_discount" name="walkin_extra_discount" onkeyup="calculate_walkin_item()" />
                                                        <!-- <div class="text-right" style="margin-top: 10px;">Discount :</div> -->
                                                    </div>
                                                </div>
                                            </td>
                                            <td align="right"><div style="margin-top: 10px;"><?php echo $currency; ?> <span>0</span></div></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" align="right">Final Amount :</td>
                                            <td align="right"><?php echo $currency; ?> <span>0</span></td>
                                        </tr>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12 col-12">
                        </div>
                        <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div id="walkin_payment_types" class="text-right">
                                <table class="table table-bordered" style="width:100%">
                                    <tbody>
                                        <?php
                                            if(!empty($payment_types))
                                            {
                                                foreach($payment_types as $ptype)
                                                {
                                        ?>
                                                    <tr>
                                                        <td><a href="javascript:;" class="btn btn-success btn-sm" onclick="fill_walkin_total(<?php echo $ptype['id']; ?>)" style="padding: 2px 5px;"><?php echo format_text(1,$ptype['name']); ?></a></td>
                                                        <td>
                                                            <input type="text" name="walkin_payment_type_amt[]" class="form-control" placeholder="Enter amount" onkeyup="check_walkin_digit(this.value);" data-walkin-id="<?php echo $ptype['id']; ?>" style="height: 25px;" />
                                                            <input type="hidden" name="walkin_payment_type_ids[]" class="form-control" value="<?php echo $ptype['id']; ?>" />
                                                        </td>
                                                    </tr>
                                        <?php
                                                }
                                            } 
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>Remaining Amount (<?php echo $currency; ?>)</td>
                                            <td><input type="text" name="remaining_amt" id="remaining_amt" readonly="" value="0" class="form-control" /></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="customerHistoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="needs-validation" action="" method="post">
                <input type="hidden" name="is_walkin_customer" id="is_walkin_customer" />
                <div class="modal-header">
                    <h5 class="modal-title">Customer History</h5>
                    <a href="javascript:;" class="close" onclick="close_customer_history_modal()">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="table-responsive customer_history_info_parent">
                        <table class="table table-default table-bordered" id="customer_history_info">
                            <thead>
                                <tr>
                                    <th width="10%">Date</th>
                                    <th width="15%">Service</th>
                                    <th width="15%">Specialist</th>
                                    <th width="30%">Note</th>
                                    <th width="10%">Source</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                     <a href="#" class="btn btn-success btn-sm" onclick="close_customer_history_modal()">Close</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
	var base_url = "<?php echo base_url(); ?>";
    var checkout_appointment = "<?php echo $checkout_appointment; ?>";
    var pending_appointment = "<?php echo $pending_appointment; ?>";
    
    var fetch_events;
    if(checkout_appointment == 1 && pending_appointment == 1) {
        fetch_events = "<?php echo base_url('appointments'); ?>?is_check=1&pending_appointment=1";
    } else if(checkout_appointment == 1 && pending_appointment == 0) {
        fetch_events = "<?php echo base_url('appointments'); ?>?is_check=1";
    } else if(checkout_appointment == 0 && pending_appointment == 1) {
        fetch_events = "<?php echo base_url('appointments'); ?>?pending_appointment=1";
    } else {
        fetch_events = "<?php echo base_url('appointments'); ?>";
    }
    var today_employees = "<?php echo base_url('today_employees'); ?>";
    var global_html;
    var company_stime = "<?php echo $company['company_stime']; ?>";
    var company_etime = "<?php echo $company['company_etime']; ?>";
    var company_currency = "<?php echo static_company_currency(); ?>";
    var default_date = "<?php echo $default_date; ?>";
	function full_screen()
    {
        var elem = document.getElementById("main_body");
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
          } else if (elem.mozRequestFullScreen) { /* Firefox */
            elem.mozRequestFullScreen();
          } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
            elem.webkitRequestFullscreen();
          } else if (elem.msRequestFullscreen) { /* IE/Edge */
            elem.msRequestFullscreen();
          }
    }
</script>
<!-- <script src="< ?php echo base_url('public/calendar/vendor/moment/min/moment.min.js'); ?>"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.34/moment-timezone-with-data.min.js"></script> -->
<script src="<?php echo base_url('public/calendar/fullcalendar.js'); ?>"></script>
<script src="<?php echo base_url('public/calendar/resource.js'); ?>"></script>
<script src="<?php echo base_url('public/calendar/appointment.js'); ?>?v=13.0"></script>
<script src="<?php echo base_url('public/admin/js/service_scroll/priority-nav-scroller.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="<?php echo base_url('public/admin/js/jquery.validate.js'); ?>"></script>
<script src="<?php echo base_url('public/admin/js/additional_methods.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Dashboard";
    $(document).ready(function(){
        $(document).on("keyup","#extra_discount",function(){
            var discounted_amt = 0;
            if($("#discounted_amt").val() != "") {
                discounted_amt = parseFloat($("#discounted_amt").val());
            }
            var amt = $(this).val();
            if($.trim(amt) != "") {
                amt = parseFloat(amt);
                var total_bill = parseFloat($.trim($("#subAmt").text()));
                if(amt > total_bill) {
                    $(this).val("0");
                } else {
                    var remaining_amt = total_bill - (amt+discounted_amt);
                    $("#totAmt").text(remaining_amt);
                    $("#remainAmt").text(remaining_amt);
                }
            }
        });
        $(document).on("change","#extra_discount",function(){
            var discounted_amt = 0;
            if($("#discounted_amt").val() != "") {
                discounted_amt = parseFloat($("#discounted_amt").val());
            }
            var amt = $(this).val();
            if($.trim(amt) != "") {
                amt = parseFloat(amt);
                var total_bill = parseFloat($.trim($("#subAmt").text()));
                if(amt > total_bill) {
                    $(this).val("0");
                } else {
                    var remaining_amt = total_bill - (amt+discounted_amt);
                    $("#totAmt").text(remaining_amt);
                    $("#remainAmt").text(remaining_amt);
                }
            }
        });
    });
</script>
<?= $this->endSection(); ?>