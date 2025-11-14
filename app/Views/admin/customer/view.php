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
				<h2 class="mb-3 line-head" id="navs">Consent Forms <a class="consent_form_history" href="<?php echo base_url('customer-consent-form-history/'.$customer_id); ?>"><small>Consent Form History</small></a></h2>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="bs-component">
				<ul class="nav nav-tabs" id="consent-form">
					<?php 
						if($forms) {
							$no = 0;
							foreach($forms as $form) {
								$no++;
					?>
								<li class="nav-item">
									<a class="nav-link <?php echo $no == 1 ? 'active' : ''; ?>" data-bs-toggle="tab" href="#form<?php echo $no; ?>">
										<b><?php echo rawurldecode($form['title']); ?></b>
									</a>
								</li>
					<?php
							}
						}
					?>
				</ul>
				<div class="tab-content" id="myTabContent">
					<div class="table-responsive">
						<table class="table table-default table-bordered mt-3">
							<tbody>
								<tr>
									<td width="15%" align="right">Customer Name :</td>
									<td><input type="text" class="form-control" name="customer_name" value="<?php echo $customer['name']; ?>"readonly /></td>
									<td width="15%" align="right">Customer Phone :</td>
									<td><input type="text" class="form-control" name="customer_phone" value="<?php echo $customer['phone']; ?>"readonly /></td>
								</tr>
								<tr>
									<td width="15%" align="right">Customer Email :</td>
									<td><input type="text" class="form-control" name="customer_email" id="customer_email" value="<?php echo $customer['email']; ?>" /></td>
									<td width="15%" align="right">Date :</td>
									<td><input type="date" class="form-control" name="customer_date" value="<?php echo date('Y-m-d'); ?>" readonly /></td>
								</tr>
							</tbody>
						</table>
					</div>
					<?php 
						if($forms) {
							$no = 0;
							foreach($forms as $form) {
								$no++;
					?>
								<div class="tab-pane fade <?php echo $no == 1 ? 'active show' : ''; ?>" id="form<?php echo $no; ?>">
									<form action="<?php echo base_url('customer-consent-form/'.$customer_id); ?>" method="POST" class="customer_consent_form">
										<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
										<input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
										<input type="hidden" name="consent_form_id" value="<?php echo $form['id']; ?>" />
										<input type="hidden" class="cmail" name="cmail" value="<?php echo $customer['email']; ?>" />
										<input type="hidden" class="form-control" name="customer_full_name" value="<?php echo $customer['name']; ?>" />
										<?php
											if(isset($form["questions"]) && $form["questions"]) {
												foreach($form["questions"] as $question) {
										?>
													<?php 
														if($question["title"] != ".") { 
													?> 
															<h3 class="mt-3"><?php echo rawurldecode($question["title"]); ?></h3>
															<input type="hidden" name="consent_form_question_id" value="<?php echo $question['id']; ?>" />
													<?php 
														} 
													?>
													<ul>
														<?php
															if (isset($question["options"]) && isset($question["options"])) {
																$qno = 0;
																foreach($question["options"] as $index => $optVal) {
																	$qno++;
														?>
																	<li class='mt-3'>
																		<h6>
																			<?php echo rawurldecode($optVal["question"]); ?>
																		</h6>
																		<?php
																			if($optVal["answer_type"] == 1) {
																		?>
																				<textarea name="input_<?php echo $optVal["id"]; ?>" class="form-control"><?php echo isset($optVal['customer_answer']) ? $optVal['customer_answer'] : ''; ?></textarea>
																		<?php
																			} else if($optVal["answer_type"] == 2) {
																				$opts = explode("%2C",$optVal["option"]);
																				if(!empty($opts)) {
																					foreach($opts as $opt) {
																		?>
																						<input type="radio" name="radio_<?php echo $optVal["id"]; ?>" value="<?php echo rawurldecode($opt); ?>" <?php echo isset($optVal['customer_answer']) && $optVal['customer_answer'] == rawurldecode(trim($opt)) ? "checked" : "" ?> /> <?php echo rawurldecode(trim($opt)); ?>
																		<?php
																					}
																		        }
																			} else if($optVal["answer_type"] == 3) {
																				$opts = explode("%2C",$optVal["option"]);
																				if(!empty($opts)) {
																					$customer_answers = [];
																					if(isset($optVal["customer_answer"]) && $optVal["customer_answer"] != "") {
																						$customer_answers = explode(",",rawurldecode($optVal["customer_answer"]));
																					}
																					foreach($opts as $opt) {
																		?>
																						<input type="checkbox" name="answer_<?php echo $optVal["id"]; ?>[]" value="<?php echo rawurldecode($opt); ?>" <?php echo in_array(rawurldecode($opt),$customer_answers) ? "checked" : ""; ?> />&nbsp;&nbsp;<?php echo rawurldecode(trim($opt)); ?>
																		<?php
																					}
																		        }
																			}
																		?>
																	</li>
														<?php
																}
															} 
														?>
													</ul>
										<?php
												}
											} 
										?>
										<button class="btn btn-sm btn-success" type="submit" style="float: right;">SUBMIT</button>
									</form>
								</div>
					<?php 
							}
						}
					?>
				</div>
			</div>			
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/jquery.validate.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/additional_methods.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Customers";
	$(document).ready(function(){
		$("#customer_email").keyup(function(){
			$(".cmail").val($(this).val());
		});
		$(".customer_consent_form").submit(function(e){
			e.preventDefault();

			if($("input[name=cmail]").val() == "") {
				alert("Please enter email");
				$("#customer_email").focus();
			    $(".cmail").val();
			} else {
				this.submit();
			}
		});
		$("#consent-form li").click(function(){
			$("#consent-form li").find("a").removeClass("active");
			$(this).find("a").addClass("active");
			$("#myTabContent .tab-pane").removeClass("active");
			$("#myTabContent .tab-pane").removeClass("show");			
			$($(this).find("a").attr("href")).addClass("active");
			$($(this).find("a").attr("href")).addClass("show");
		});
	});
</script>
<?= $this->endSection(); ?>