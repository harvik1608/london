<div class="row">
    <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
        <?php
            if(empty($appointments))
            {
                echo "<div class='alert alert-danger'>There is no any item in cart.</div>";        
            } else {
        ?>
                <table class="table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Time</th>
                            <th style="width: 65%;">Service</th>
                            <th style="width: 20%;" align="right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($appointments as $key => $val) {
                        ?>
                                <tr>
                                    <td>
                                        <b><?php echo format_date(11,$val['stime']); ?></b><br>
                                        <small><?php echo $val['duration']; ?> Min.</small>
                                    </td>
                                    <td><b><?php echo $val['serviceNm']; ?>
                                       <?php echo trim($val['caption']) != "" ? " <small>(".$val['caption'].")</small></b>" : ''; ?>
                                        <br><small>with <?php echo format_text(1,$val['staff_name']); ?></small></td>
                                    <td align="right">
                                        <b><?php echo $currency." ".number_format($val['amount'],2); ?></b>
                                        <?php
                                            if($val['actual_amount'] > 0 && $val['actual_amount'] != $val['amount']) {
                                                echo '<strike><small>'.$val['actual_amount'].'</small></strike>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                        <?php       
                            } 
                        ?>
                        <tr>
                            <td colspan="2" align="right">Sub Total :</td>
                            <td align="right"><h5><?php echo $currency." ".$moredatainfo['subTotal']; ?></h5></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right">Discount Amount (-) :</td>
                            <td align="right"><h5><?php echo $currency." ".$moredatainfo['discountAmt']+$moredatainfo['extra_discount']; ?></h5></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right">Final Amount :</td>
                            <td align="right"><h5><?php echo $currency." ".$moredatainfo['subTotal'] - ($moredatainfo['discountAmt']+$moredatainfo['extra_discount']); ?></h5></td>
                        </tr>
                    </tbody>
                </table>
        <?php
            }
            // date_default_timezone_set($timezone);
            // $show_timezone = timezone("UTC",$timezone,$moredatainfo['addedDate']);
        ?>
    </div>
    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
        <table class="table table-bordered" style="width:100%">
            <tbody>
                <tr>
                    <td colspan="3">
                        <p class="remove-margin-bottom" style="margin-bottom: 0px;">Booked Date : <b><?php echo format_datetime($moredatainfo['addedDate'],3); ?></b></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"><h4 class="remove-margin-bottom" style="margin-bottom: 0px;">Customer Info</h4></td>
                </tr>
                <tr>
                    <td><span class="pull-right">Name :</span></td> 
                    <td colspan="2"><?php echo $moredatainfo['customer_name']; ?></td>
                </tr>
                <tr>
                    <td><span class="pull-right">Phone :</span></td>
                    <td colspan="2"><?php echo $moredatainfo['customer_phone']; ?> <a href="javascript:get_customer_history(0,'<?php echo $moredatainfo['customer_phone']; ?>');"><i class="fa fa-eye"></i></a></td>
                </tr>
                <tr>
                    <td><span class="pull-right">Note :</span></td>
                    <td colspan="2"><b style="color: #FF0000;"><?php echo $moredatainfo['note']; ?></b></td>
                </tr>
                <!--<tr>-->
                <!--    <td><span class="pull-right">Booked By :</span></td>-->
                <!--    <td colspan="2"><b>< ?php echo isset($moredatainfo['staff_name']) && !is_null($moredatainfo['staff_name']) && $moredatainfo['staff_name'] != 0 ? $moredatainfo['staff_name'] : "Customer"; ?></b></td>-->
                <!--</tr>-->
                <!-- <tr>
                    <td colspan="3"><h4 class="remove-margin-bottom">Appointment Status</h4></td>
                </tr>
                <tr>
                    <td>
                        <div class="custom-control custom-radio form-check">
                            <input type="radio" class="custom-control-input" id="astatus1" name="appointmentStatus" value="1" < ?php echo $moredatainfo['status'] == 1 ? "checked" : ""; ?> />
                            <label class="custom-control-label" for="astatus1">New</label>
                        </div>
                    </td>
                    <td>
                        <div class="custom-control custom-radio form-check">
                            <input type="radio" class="custom-control-input" id="astatus2" name="appointmentStatus" value="1" < ?php echo $moredatainfo['status'] == 2 ? "checked" : ""; ?> />
                            <label class="custom-control-label" for="astatus2">Completed</label>
                        </div>
                    </td>
                    <td>
                        <div class="custom-control custom-radio form-check">
                            <input type="radio" class="custom-control-input" id="astatus3" name="appointmentStatus" value="1" < ?php echo $moredatainfo['status'] == 3 ? "checked" : ""; ?> />
                            <label class="custom-control-label" for="astatus3">No Show</label>
                        </div>
                    </td>
                </tr> -->
                <tr>
                    <td colspan="3"><h4 class="remove-margin-bottom" style="margin-bottom: 0px;">Booked From</h4></td>
                </tr>
                <tr>
                    <td>
                        <div class="custom-control custom-radio form-check">
                            <input type="radio" class="custom-control-input" id="bookFrom1" name="bookedFrom" value="1" <?php echo $moredatainfo['bookedFrom'] == 1 ? "checked" : ""; ?> />
                            <label class="custom-control-label" for="bookFrom1">Online</label>
                        </div>
                    </td>
                    <td>
                        <div class="custom-control custom-radio form-check">
                            <input type="radio" class="custom-control-input" id="bookFrom2" name="bookedFrom" value="1" <?php echo $moredatainfo['bookedFrom'] == 2 ? "checked" : ""; ?> />
                            <label class="custom-control-label" for="bookFrom2">Treatwell</label>
                        </div>
                    </td>
                    <td>
                        <div class="custom-control custom-radio form-check">
                            <input type="radio" class="custom-control-input" id="bookFrom3" name="bookedFrom" value="1" <?php echo $moredatainfo['bookedFrom'] == 3 ? "checked" : ""; ?> />
                            <label class="custom-control-label" for="bookFrom3">Salon</label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="alert alert-danger" id='view_appointment_error' style="display: none;"></div>
    </div>
</div>