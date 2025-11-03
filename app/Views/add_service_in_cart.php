<tr id="cart_<?php echo $no; ?>" name="<?php echo $ntime; ?>" class="exist_<?php echo $service_id; ?>_<?php echo $service_sub_id; ?>">
	<td>
		<input type="hidden" name="service_item[]" value="<?php echo $service_id; ?>" />
		<input type="hidden" name="service_sub_item[]" value="<?php echo $service_sub_id; ?>" />
		<input type="hidden" name="service_name[]" value="<?php echo $service_name; ?>" />
		<input type="hidden" name="sub_service_name[]" value="<?php echo $caption; ?>" />
		<input type="hidden" name="service_duration[]" value="<?php echo $duration; ?>" />

		<a href="javascript:;" onclick="remove_from_cart('<?php echo $no; ?>','<?php echo $flag; ?>');"><i class='la la-trash'></i></a> 
		<?php echo $service_name; ?><br>
		<?php 
			if($caption != "")
			{
		?>
				<small>(<?php echo $caption; ?>)</small>
		<?php
			}
		?>
	</td>
	<td>
		<input type="hidden" name="service_stime[]" value="<?php echo date('H:i:s',strtotime($stime)); ?>" />
		<input type="hidden" name="service_etime[]" value="<?php echo $etime; ?>" />
		<?php
			if($flag == 0) 
				echo "<span class='".$stime."' hidden>".format_date(16,$stime)."</span><small>".$duration." Min.</small>"; 
			else 
				echo "<strike><span class='".$stime."' hidden>".format_date(16,$stime)."</span><small>".$duration." Min.</small></strike>"; 
		?>
		<br>
	</td>
	<td>
		<?php echo $currency; ?> <span><?php echo $price; ?></span>
		<input type="hidden" name="service_amount[]" value="<?php echo $price; ?>" />
	</td>
</tr>