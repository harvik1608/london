<?php
	if($cart_data) {
		foreach($cart_data as $key => $val) {
			$no = $key+1;
			$service_nm = $val["service_name"];
			if($val["caption"] != "") {
				$service_nm .= ' <small>('.$val["caption"].')</small>';
			}
?>
			<tr id="cart_<?php echo $no; ?>" name="<?php echo $val["ntime"]; ?>" class="exist_<?php echo $val["service_id"]; ?>_<?php echo $val["service_sub_id"]; ?>">
				<td>
					<input type="hidden" name="entry_id[]" value="<?php echo $val["entry_id"]; ?>" />
					<input type="hidden" name="service_item[]" value="<?php echo $val["service_id"]; ?>" />
					<input type="hidden" name="service_sub_item[]" value="<?php echo $val["service_sub_id"]; ?>" />
					<input type="hidden" name="service_name[]" value="<?php echo $val["service_name"]; ?>" />
					<input type="hidden" name="sub_service_name[]" value="<?php echo $val["caption"]; ?>" />
					<input type="hidden" name="service_duration[]" value="<?php echo $val["duration"]; ?>" />
					<input type="hidden" name="service_nm[]" value="<?php echo $service_nm; ?>" />
					<input type="hidden" name="service_actual_amount[]" value="<?php echo isset($val["actual_price"]) ? $val["actual_price"] : 0; ?>" />
					<input type="hidden" name="service_caption[]" value="<?php echo isset($val["sub_caption"]) ? $val["sub_caption"] : ''; ?>" />

					<a href="javascript:;" onclick="remove_from_cart('<?php echo $val['uniq_id']; ?>',<?php echo $val['entry_id']; ?>,<?php echo $val['flag']; ?>);"><i class='fa fa-trash'></i></a> 
					<?php echo $val["service_name"]; ?><br>
					<?php 
						if(isset($val["sub_caption"]) && $val["sub_caption"] != "") {
							echo '<small>('.$val["sub_caption"].')</small>';
						}
					?>
				</td>
				<td>
					<select class="form-control" name="service_staff[]" id="selected_staff_id_<?php echo $no; ?>" onchange="get_selected_staff_name('<?php echo $no; ?>')">
						<option value="" data-status="0" data-color="">Staff</option>
						<?php
							foreach($val["staffs"] as $staff)
							{
								// if($staff['status'] == 1) 
								// {
						?>
									<option value="<?php echo $staff['id']; ?>" data-status="<?php echo $staff['status']; ?>" data-color="<?php echo $staff['color']; ?>" <?php if($staff["id"] == $val["staffId"]){echo "selected";} ?>><?php echo ucwords(strtolower($staff['name'])); ?></option>
						<?php 
								// }
							}
						?>
					</select>
					<small class='error' id="selected_staff_id_error_<?php echo $no; ?>"></small>
					<input type="hidden" name="selected_staff_name[]" value="<?php echo $val["staff_name"]; ?>" id="selected_staff_name_<?php echo $no; ?>" value="<?php if(isset($val["staff_name"])){ echo $val["staff_name"];} ?>" />
					<input type="hidden" name="selected_staff_color[]" value="<?php echo $val["staff_color"]; ?>" id="selected_staff_color_<?php echo $no; ?>" value="<?php if(isset($val["staff_color"])){ echo $val["staff_color"];} ?>" />
					<input type="hidden" name="is_busy_staff[]" id="is_busy_staff_<?php echo $no; ?>" value="<?php echo $val["is_busy_staff"]; ?>" />
				</td>
				<td class="appointment_cart_time">
					<input type="hidden" name="service_stime[]" value="<?php echo date('H:i:s',strtotime($val["stime"])); ?>" />
					<input type="hidden" name="service_etime[]" value="<?php echo format_date(10,$val["etime"]); ?>" />
					<?php
						if($val["flag"] == 0) 
							echo "<span class='".$val["stime"]."' name='".format_date(10,$val["stime"])."'>".format_date(11,$val["stime"])."</span><br><small>".$val["duration"]." Min.</small>"; 
						else 
							echo "<strike><span class='".$val["stime"]."' name='".format_date(10,$val["stime"])."'>".format_date(11,$val["stime"])."</span><br><small>".$val["duration"]." Min.</small></strike>"; 
					?>
					<br>
				</td>
				<td align="right" class="appointment_cart_price">
			        <?php echo static_company_currency(); ?> <span><?php echo $val["price"]; ?></span>
					<input type="hidden" name="service_amount[]" value="<?php echo $val["price"]; ?>" />
				</td>
			</tr>
<?php
		}
	}
?>