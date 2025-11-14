<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-default table-bordered mt-3">
				<tbody>
					<tr>
						<td width="15%" align="right">Customer Name :</td>
						<td><b><?php echo $customer['name']; ?></b></td>
						<td width="15%" align="right">Customer Phone :</td>
						<td><b><?php echo $customer['phone']; ?></b></td>
						<td width="15%" align="right">Customer Email :</td>
						<td><b><?php echo $customer['email']; ?></b></td>
					</tr>
				</tbody>
			</table>
		</div>
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
				<?php 
					if($forms) {
						$no = 0;
						foreach($forms as $form) {
							$no++;
				?>
							<div class="tab-pane fade <?php echo $no == 1 ? 'active show' : ''; ?>" id="form<?php echo $no; ?>">
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
							</div>
				<?php 
						}
					}
				?>
			</div>
		</div>			
	</div>
</div>