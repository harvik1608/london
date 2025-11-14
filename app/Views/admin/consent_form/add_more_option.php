<tr id="<?php echo $no; ?>">
	<td><input type="text" name="question[<?php echo $row_no; ?>][]" class="form-control" /></td>
	<td>
		<select name="answer_type[<?php echo $row_no; ?>][]" class="form-control">
			<option value="1">Manual</option>
			<option value="2">Single Selection</option>
			<option value="3">Multiple Selection</option>
		</select>
	</td>
	<td><input type="text" name="option[<?php echo $row_no; ?>][]" class="form-control" /></td>
	<td>
		<a class="btn btn-sm btn-danger text-white" href="javascript:;" onclick="remove_more_option('<?php echo $no; ?>')">
			<i class="fa fa-times"></i>
		</a>
	</td>
</tr>