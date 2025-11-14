<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<style>
	.consent_form_history {
		font-size: 16px;
		float: right;
	}
</style>
<div class="app-title">
	<div>
		<h1><i class="fa fa-users"></i> Customers</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="<?php echo base_url('customers'); ?>">Customers</a></li>
		<li class="breadcrumb-item"><a>View Customer</a></li>
	</ul>
</div>
<div class="tile mb-4">
	<div class="row">
		<div class="col-lg-12">
			<div class="page-header">
				<h2 class="mb-3 line-head" id="navs">
					Consent Form History
					<a style="float: right;" class="btn btn-sm btn-warning" href="<?php echo base_url('customers/'.$customer_id); ?>">Back</a>
				</h2>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="table-responsive">
				<?php
					if(isset($forms) && $forms) {
						$no = 0;
						foreach($forms as $form) {
							$no++;
				?>
							<table class="table table-default table-bordered">
								<tbody>
									<tr>
										<td>
											<p>Form No: <?php echo $no; ?> - <b><u> <?php echo rawurldecode($form['title']); ?> Consent Form</u></b></p>
										</td>
									</tr>
									<?php
										if(isset($form["questions"]) && $form["questions"]) {
											foreach($form["questions"] as $question) {
									?>
												<tr>
													<td>
														<h5>
															<?php 
																echo rawurldecode($question["title"]); 
															?>
														</h5>
													</td>
												</tr>
												<?php
													if (isset($question["options"]) && isset($question["options"])) {
														$qno = 0;
														foreach($question["options"] as $index => $optVal) {
															$qno++;
												?>
															<tr>
																<td><?php echo rawurldecode($optVal["question"]); ?></td>
															</tr>
															<tr>
																<?php
																	if($optVal["answer_type"] == 1) {
																?>
																		<td><input type="text" value="<?php echo isset($optVal['customer_answer']) ? $optVal['customer_answer'] : ''; ?>" disabled class="form-control" /><p style="float: right;"><b><small>Submiited On : <?php echo $optVal['customer_answer_date']; ?></small></b></p></td>
																<?php 
																	} else if($optVal["answer_type"] == 2) {
																		$opts = explode("%2C",$optVal["option"]);
																?>
																		<td>
																			<?php
																				if(!empty($opts)) {
																					foreach($opts as $opt) { 
																			?>
																						<input type="radio" name="radio_<?php echo $optVal["id"]; ?>" value="<?php echo rawurldecode($opt); ?>" <?php echo isset($optVal['customer_answer']) && $optVal['customer_answer'] == rawurldecode(trim($opt)) ? "checked" : "" ?> disabled /> <?php echo rawurldecode(trim($opt)); ?>
																			<?php
																					}
																				} 
																			?>
																			<p style="float: right;"><b><small>Submiited On : <?php echo $optVal['customer_answer_date']; ?></small></b></p>
																		</td>
																<?php
																	} else if($optVal["answer_type"] == 3) {
																		$opts = explode("%2C",$optVal["option"]);
																?>
																		<td>
																			<?php
																				if(!empty($opts)) {
																					$customer_answers = [];
																					if(isset($optVal["customer_answer"]) && $optVal["customer_answer"] != "") {
																						$customer_answers = explode(",",rawurldecode($optVal["customer_answer"]));
																					}
																					foreach($opts as $opt) {
																		?>
																						<input type="checkbox" name="answer_<?php echo $optVal["id"]; ?>[]" value="<?php echo rawurldecode($opt); ?>" <?php echo in_array(rawurldecode($opt),$customer_answers) ? "checked" : ""; ?> disabled />&nbsp;&nbsp;<?php echo rawurldecode(trim($opt)); ?>
																		<?php
																					}
																				} 
																			?>
																			<p style="float: right;"><b><small>Submiited On : <?php echo $optVal['customer_answer_date']; ?></small></b></p>
																		</td>
																<?php
																	}
																?>
															</tr>
												<?php
														}
													}
												?>
									<?php 
											}
										}
									?>
								</tbody>
							</table>
				<?php
						}
					} else {
					    echo "<div class='alert alert-danger'><span>No any consent form added.</span></div>";
					} 
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/jquery.validate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/additional_methods.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Customers";
	$(document).ready(function(){
	});
</script>
<?= $this->endSection(); ?>