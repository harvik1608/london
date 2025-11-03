<div class="row">
    <div class="col-xl-7 col-lg-12 col-md-12 col-sm-12 col-12">
        <?php
            if(empty($appointments))
            {
                echo "<div class='alert alert-danger'>There is no any item in cart.</div>";        
            } else {
        ?>
                <table class="table table-bordered" style="width:100%" id="checkoutItemTbl">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Time</th>
                            <th style="width: 65%;">Service</th>
                            <th style="width: 20%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($appointments as $key => $val) {
                        ?>
                                <tr data-no="<?php echo $val['id']; ?>">
                                    <td><b><?php echo format_date(11,$val['stime'],4); ?></b><br><small><?php echo $val['duration']; ?> Min.</small></td>
                                    <td>
                                        <b><?php echo $val['serviceNm']; ?></b>
                                        <?php echo trim($val['caption']) != "" ? " <small>(".$val['caption'].")</small></b>" : ''; ?>
                                        <br><small>with <?php echo format_text(1,$val['staff_name']); ?></small></td>
                                    <!-- <td align="right"><b> < ?php echo $currency." ".$val['amount']; ?></b></td> -->
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
                            <td align="right"><h5><?php echo $currency; ?> <span id="subAmt"><?php echo $moredatainfo['subTotal']; ?></span></h5></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <small>Discount Type</small>
                                        <select class="form-control" name="discount_type" id="discount_type" onchange="get_discount_type(this.value)">
                                            <option value="">Option</option>
                                        <?php
                                            if(!empty($discount_types))
                                            {
                                                foreach($discount_types as $dtype)
                                                {
                                        ?>
                                                    <option value="<?php echo $dtype['id']."_".$dtype['discount_type']."_".$dtype['discount_value']; ?>"><?php echo $dtype['name']; ?></option>
                                        <?php
                                                }
                                            } 
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <small>Discount Amount</small>
                                        <input type="text" readonly="" placeholder="Amount" class="form-control" id="discounted_amt" />
                                    </div>
                                    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <small>Extra Discount</small>
                                        <input type="number" placeholder="Extra Discount" class="form-control" id="extra_discount" value="0" min="0" max="<?php echo $moredatainfo['finalAmt']; ?>" />
                                    </div>
                                    <!-- <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="text-right">
                                            Discount Amount (-) :
                                        </div>
                                    </div> -->
                                </div>
                            </td>
                            <td align="right"><h5><?php echo $currency; ?> <span id="disAmt"><?php echo $moredatainfo['discountAmt']; ?></span></h5></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right">Final Amount :</td>
                            <td align="right"><h5><?php echo $currency; ?> <span id="totAmt"><?php echo $moredatainfo['finalAmt']; ?></span></h5></td>
                        </tr>
                    </tbody>
                </table>
        <?php
            }
            // date_default_timezone_set($timezone);
            // $show_timezone = timezone("UTC",$timezone,$moredatainfo['addedDate']);
        ?>
    </div>
    <div class="col-xl-5 col-lg-12 col-md-12 col-sm-12 col-12">
        <table class="table table-bordered" style="width:100%" id="paymentHistory">
            <tbody>
                <tr>
                    <td colspan="3" style="padding-bottom: 0px;"><h4 class="remove-margin-bottom" style="font-size: 15px;">Booking Date : <?php echo format_datetime($moredatainfo['addedDate'],3); ?></h4></td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-bottom: 0px;"><h4 class="remove-margin-bottom">Customer Info</h4></td>
                </tr>
                <tr>
                    <td>Name</td> 
                    <td colspan="2"><?php echo $moredatainfo['customer_name']; ?></td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td colspan="2"><?php echo $moredatainfo['customer_phone']; ?></td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-bottom: 0px;"><h4 class="remove-margin-bottom">Payment Method</h4></td>
                </tr>
                <?php
                    if(!empty($payment_types))
                    {
                        foreach($payment_types as $ptype)
                        {
                ?>
                            <tr>
                                <td align="right"><a href="javascript:;" class="btn btn-success btn-sm" onclick="fill_total('<?php echo $moredatainfo['subTotal']; ?>',<?php echo $ptype['id']; ?>)" style="padding: 2px 5px;"><?php echo format_text(1,$ptype['name']); ?></a></td>
                                <td><input type="text" name="payment_type_amt[]" class="form-control" data-payment-id="<?php echo $ptype['id']; ?>" placeholder="Enter amount" onkeyup="check_digit(this.value);" style="height: 25px;" /></td>
                            </tr>
                <?php
                        }
                    } 
                ?>
                <tr>
                    <td>Remaining Amount (<?php echo $currency; ?>)</td>
                    <td><h5><b><span id="remainAmt"><?php echo $moredatainfo['subTotal']; ?></span></b></h5></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="alert alert-danger" id='checkout_appointment_error' style="display: none;"></div>
    </div>
</div>