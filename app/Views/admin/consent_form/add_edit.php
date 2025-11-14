<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<?php 
	if($consent_form) {
		$page_title = "Edit Consent Form";
		$action = base_url('consent_forms/'.$consent_form["id"]);

		$title = rawurldecode($consent_form["title"]);
		$duration = $consent_form["duration"];
		$description = $consent_form["description"];
		$is_active = $consent_form["is_active"];
	} else {
		$page_title = "New Consent Form";
		$action = base_url('consent_forms');

		$title = "";
		$duration = "";
		$description = "";
		$is_active = "1";
	}
?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-file"></i> Consent Forms</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('consent_forms'); ?>">Consent Forms</a></li>
		<li class="breadcrumb-item"><a><?php echo $page_title; ?></a></li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<form class="form-horizontal" id="form" method="post" action="<?php echo $action; ?>">
				<?php
					if($title != "") {
						echo '<input type="hidden" name="_method" value="PUT" />'; 
					}
				?>
				<div class="tile-title">
					<h4><?php echo $page_title; ?></h4>
				</div><hr>
				<div class="tile-body">	
					<div class="row">
						<div class="col-lg-9">
							<div class="form-group">
								<label class="control-label">Form Title</label>
								<input class="form-control" type="text" placeholder="Enter form title" name="title" id="title" value="<?php echo $title; ?>" />
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group">
								<label class="control-label">Duration <small>(in month)</small></label>
								<input class="form-control" type="number" placeholder="Enter duration" name="duration" id="duration" value="<?php echo $duration; ?>" />
							</div>
						</div>
						<div class="col-lg-12">
							<a class="btn btn-sm btn-success text-white" href="javascript:;" onclick="add_more_question()" style="float: right;">
								<i class="fa fa-plus"></i>
							</a><br><br>
							<?php $time = time(); ?>
							<div class="table-responsive">
								<table class="table table-default table-bordered" id="consertFormTbl">
									<tbody>
										<?php
											if(isset($consent_form_questions) && !empty($consent_form_questions)) {
												$sr_no = 0;
												foreach($consent_form_questions as $row) {
													$sr_no++;
										?>
													<tr id="question-row-<?php echo $sr_no; ?>">
														<td>
															<span><b>TITLE : </b></span>
															<?php if($sr_no > 1) { ?>
																<a class="btn btn-sm btn-danger text-white" style="float: right;" href="javascript:;" onclick="remove_question_row(<?php echo $sr_no; ?>,<?php echo $row['id']; ?>)"><i class="fa fa-times"></i></a><br><br>
															<?php } ?>
															<input type="text" name="question_title[]" class="form-control" value="<?php echo rawurldecode($row['title']); ?>" /><br>
															<input type="hidden" name="no[]" value="<?php echo $sr_no; ?>" class="form-control" />
															<input type="hidden" name="consert_form_question_id[]" value="<?php echo $row['id']; ?>" class="form-control" />
															<table class="table table-default table-bordered" id="tbl-<?php echo $sr_no; ?>">
																<thead>
																	<tr>
																		<th width="55%">Question</th>
																		<th width="10%">Answer Type</th>
																		<th width="25%">Options</th>
																		<th width="5%">Action</th>
																	</tr>
																</thead>
																<tbody>
																	<?php
																		if($row["items"] && !empty($row["items"])) {
																			foreach($row["items"] as $itemKey => $itemVal) {
																	?>
																				<tr id="<?php echo $itemVal['id']; ?>">
																					<td>
																						<input type="hidden" name="consent_form_question_answer_id[<?php echo $sr_no; ?>][]" class="form-control" value="<?php echo rawurldecode($itemVal['id']); ?>" />
																						<input type="text" name="question[<?php echo $sr_no; ?>][]" class="form-control" value="<?php echo rawurldecode($itemVal['question']); ?>" />
																					</td>
																					<td>
																						<select name="answer_type[<?php echo $sr_no; ?>][]" class="form-control">
																							<option value="1" <?php echo rawurldecode($itemVal['answer_type']) == 1 ? 'selected' : ''; ?>>Manual</option>
																							<option value="2" <?php echo rawurldecode($itemVal['answer_type']) == 2 ? 'selected' : ''; ?>>Single Selection</option>
																							<option value="3" <?php echo rawurldecode($itemVal['answer_type']) == 3 ? 'selected' : ''; ?>>Multiple Selection</option>
																						</select>
																					</td>
																					<td><input type="text" name="option[<?php echo $sr_no; ?>][]" class="form-control" value="<?php echo rawurldecode($itemVal['option']); ?>" /></td>
																					<td>
																						<?php if($itemKey == 0) { ?>
																							<a class="btn btn-sm btn-info text-white" href="javascript:;" onclick="add_more_option(<?php echo $sr_no; ?>)">
																								<i class="fa fa-plus"></i>
																							</a>
																						<?php } else { ?>
																							<a class="btn btn-sm btn-danger text-white" href="javascript:;" onclick="remove_more_option(<?php echo $itemVal['id']; ?>,<?php echo $itemVal['id']; ?>)"><i class="fa fa-times"></i></a>
																						<?php } ?>
																					</td>
																				</tr>
																	<?php
																			}
																		} 
																	?>
																</tbody>
															</table>
														</td>
													</tr>
										<?php
												}
											} else {
										?>
												<tr>
													<td>
														<span><b>TITLE : </b></span>
														<input type="text" name="question_title[]" class="form-control" /><br>
														<input type="hidden" name="no[]" value="<?php echo $time; ?>" class="form-control" />
														<table class="table table-default table-bordered" id="tbl-<?php echo $time; ?>">
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
																	<td><input type="text" name="question[<?php echo $time; ?>][]" class="form-control" /></td>
																	<td>
																		<select name="answer_type[<?php echo $time; ?>][]" class="form-control">
																			<option value="1">Manual</option>
																			<option value="2">Single Selection</option>
																			<option value="3">Multiple Selection</option>
																		</select>
																	</td>
																	<td><input type="text" name="option[<?php echo $time; ?>][]" class="form-control" /></td>
																	<td>
																		<a class="btn btn-sm btn-info text-white" href="javascript:;" onclick="add_more_option(<?php echo $time; ?>)">
																			<i class="fa fa-plus"></i>
																		</a>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
										<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Note</label>
								<textarea class="form-control summernote" name="description" id="description"><?php echo $description; ?></textarea>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">Status</label>
								<select class="form-control" name="is_active" id="is_active">
									<option value="1" <?php echo $is_active == '1' ? "selected" : ""; ?>>Active</option>
									<option value="0" <?php echo $is_active == '0' ? "selected" : ""; ?>>Inactive</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="tile-footer">
					<div class="row">
						<div class="col-md-8 col-md-offset-3">
							<button class="btn btn-sm btn-success" type="submit">SUBMIT</button>
							<a href="<?php echo base_url('consent_forms'); ?>" class="btn btn-sm btn-danger" id="backbtn">Back</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/jquery.validate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/additional_methods.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Consent Forms";
	$(document).ready(function(){
		$('.summernote').summernote({
			height: "auto"
		});
		$("#form").validate({
			rules:{
				title: {
					required: true
				},
				duration:{
					required: true
				}
			},
			messages:{
				title:{
					required: "<small class='error'><i class='fa fa-warning'></i> Title is required</small>"
				},
				duration:{
					required: "<small class='error'><i class='fa fa-warning'></i> Duration is required</small>"
				}
			}
		});
		$("#form").submit(function(e){
			e.preventDefault();

			if($("#form").valid()) {
				$.ajax({
					url: $("#form").attr("action"),
					type: $("#form").attr("method"),
					dataType: "json",
					data: new FormData(this),
					processData: false,
					contentType: false,
					beforeSend:function(){
						$("#form button[type=submit]").attr("disabled",true);
					},
					success:function(response) {
						if(response.status == 1) {
							window.location.href = $("#backbtn").attr("href");
						}
					},
					complete:function(){
						// $("#form button[type=submit]").attr("disabled",false);
					}
				});
			}
		});
	});
	function add_more_question()
	{
		$.ajax({
			url: "<?php echo base_url('add-more-consent-questions'); ?>",
			type: "GET",
			dataType: "json",
			success:function(response) {
				$("#consertFormTbl tbody:eq(0)").append(response.html);	
			}
		});
	}
	function remove_more(no)
	{
		$("#"+no).remove();
	}
	function add_more_option(no)
	{
		$.ajax({
			url: "<?php echo base_url('add-more-option'); ?>",
			type: "GET",
			data:{
				row_no: no
			},
			dataType: "json",
			success:function(response) {
				$("#tbl-"+no+" tbody").append(response.html);	
			}
		});
	}
	function remove_more_option(element,id = 0)
	{
		if(confirm("Are you sure?")) {
			if(id > 0) {
				$.ajax({
					url: "<?php echo base_url('delete-more-option'); ?>",
					type: "GET",
					data:{
						id: id
					},
					success:function(response) {
						$("#"+element).remove();			
					}
				});
			} else {
				$("#"+element).remove();
			}
		}
	}
	function remove_question_row(row,id = 0)
	{
		if(confirm("Are you sure?")) {
			if(id > 0) {
				$.ajax({
					url: "<?php echo base_url('delete-more-question'); ?>",
					type: "GET",
					data:{
						id: id
					},
					success:function(response) {
						$("#question-row-"+row).remove();			
					}
				});
			} else {
				$("#question-row-"+row).remove();
			}
		}
	}
</script>
<?= $this->endSection(); ?>