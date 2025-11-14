<tr id="question-row-<?php echo $no; ?>">
	<td>
		<span><b>TITLE : </b></span>
		<a class="btn btn-sm btn-danger text-white" style="float: right;" href="javascript:;" onclick="remove_question_row(<?php echo $no; ?>)"><i class="fa fa-times"></i></a><br><br>
		<input type="text" name="question_title[]" class="form-control" /><br>
		<input type="hidden" name="no[]" value="<?php echo $no; ?>" class="form-control" />
		<table class="table table-default table-bordered" id="tbl-<?php echo $no; ?>">
			<thead>
				<tr>
					<th width="55%">Question</th>
					<th width="10%">Answer Type</th>
					<th width="25%">Options</th>
					<th width="5%">Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="text" name="question[<?php echo $no; ?>][]" class="form-control" /></td>
					<td>
						<select name="answer_type[<?php echo $no; ?>][]" class="form-control">
							<option value="1">Manual</option>
							<option value="2">Single Selection</option>
							<option value="3">Multiple Selection</option>
						</select>
					</td>
					<td><input type="text" name="option[<?php echo $no; ?>][]" class="form-control" /></td>
					<td>
						<a class="btn btn-sm btn-info text-white" href="javascript:;" onclick="add_more_option(<?php echo $no; ?>)">
							<i class="fa fa-plus"></i>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>