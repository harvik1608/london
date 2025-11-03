<style>
    .table thead th {
        font-weight: 300 !important;
    }
</style>
<div class="table-responsive">
    <?php
        $timing_arr = getDatesFromRange($sdate,$edate);
    ?>
    <a href="javascript:;" onclick="show_report()"><b>SHOW REPORT</b></a>
    <a id="total_staff_hours" style="float: right;"></a>
    <table class="table table-default table-bordered" id="timingTbl">
        <thead>
            <tr>
                <th>Staff/Date</th>
                <?php
                    $today = date("Y-m-d");
                    foreach($timing_arr as $arrK => $arrV)
                    {  
                        echo "<th id='".$arrV."'><center>".date("D",strtotime($arrV))."<br>".format_datetime($arrV,1)."</center></th>";
                    }
                ?>
                <th><b>TOTAL HOURS</b></th>
            </tr>
        </thead>
        <tbody id="">
            <?php
                $session = session();
                $company_time = company_timing(static_company_id());
                $default_time = $company_time["company_stime"]."<br>To<br>".$company_time["company_etime"];
                
                $timing_arr = getDatesFromRange($sdate,$edate);
                if(!empty($staffs))
                {
                    foreach($staffs as $key => $val)
                    {
                        $total_hours = 0;
                        echo "<tr id='".$val['id']."'>";
                        echo "<td>".format_text(1,$val['fname']." ".$val['lname'])."</td>";
                        $i = 0;
                        foreach($timing_arr as $arrK => $arrV)
                        {  
                            $i++;
            ?>
                            <?php
                                if(in_array($arrV, $val['date'])) {
                                    $grid_title = explode("_",$val['time'][$arrV]);
                            ?>
                                    <td style="cursor: pointer;text-align: center;" id="<?php echo $val['id']."_".$i; ?>" onclick="add_timing('<?php echo $val['id']; ?>','<?php echo $arrV; ?>','<?php echo format_text(1,$val['fname']." ".$val['lname'])." <small>(".date("D", strtotime($arrV))." ".format_date(13,$arrV).")</small>"; ?>','<?php echo $arrV; ?>','<?php echo $grid_title[0]; ?>','<?php echo $grid_title[2]; ?>','<?php echo $grid_title[1]; ?>');">
                                        <h6>
                                            <small><?php echo $grid_title[1]; ?></small>
                                            <?php 
                                                $tm = explode("To",$grid_title[1]);
                                                $tm[0] = str_replace("<br>", "", $tm[0]);
                                                $tm[1] = str_replace("<br>", "", $tm[1]);
                                                $dt1 = trim($arrV." ".trim($tm[0]));
                                                $dt2 = trim($arrV." ".trim($tm[1]));
                                                $total_hours = $total_hours + calc_hours($dt1,$dt2);
                                            ?>
                                        </h6>
                                    </td>
                            <?php
                                } else {
                            ?>
                                    <td style="cursor: pointer;text-align: center;" id="<?php echo $val['id']."_".$i; ?>" onclick="add_timing('<?php echo $val['id']; ?>','<?php echo $arrV; ?>','<?php echo format_text(1,$val['fname']." ".$val['lname'])." <small>(".date("D", strtotime($arrV))." ".format_date(13,$arrV).")</small>"; ?>','<?php echo $arrV; ?>','0','0','<?php echo $default_time; ?>');">
                                        <h4>+</h4>
                                    </td>
                            <?php
                                }
                            ?>
            <?php
                        }
                        echo '<td style="text-align: center;"><h6><b>'.$total_hours.'</b></h6></td>';
                        echo "</tr>";
                    }
                } 
            ?>       
        </tbody>
    </table>
</div>