<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-file"></i> Consent Forms</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Consent Forms</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>Consent Form List <a class="btn btn-sm btn-success" href="<?php echo base_url('consent_forms/new'); ?>" style="float: right;color: #fff;"><i class="fa fa-plus"></i> New Consent Form</a></h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="tbl">
						<thead>
							<tr>
								<th width="5%">No</th>
                                <th width="75%">Title</th>
                                <th width="10%">Status</th>
                                <th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($consent_forms) {
									$no = 0;
									foreach($consent_forms as $payment_type) {
										$no++;
							?>
										<tr>
											<td><?php echo $no; ?></td>
											<td><?php echo rawurldecode($payment_type['title']); ?></td>
                                            <td>
                        						<?php
                        							if($payment_type['is_active'] == 1)
                        								echo '<span class="text-white badge badge-success p-2">Active</span>';
                        							else
                        								echo '<span class="text-white badge badge-danger">Inactive</span>';
                        						?>
                        					</td>
											<td>
												<a class="btn btn-sm btn-success" href="<?php echo base_url('consent_forms/'.$payment_type['id'].'/edit'); ?>"><i class="fa fa-edit text-white"></i></a>
												<!-- <a class="btn btn-sm btn-danger" href="javascript:;" onclick="remove_row('<?php echo base_url('payment_types/'.$payment_type['id']); ?>',0,'Are you sure to remove this payment type?')"><i class="fa fa-trash text-white"></i></a> -->
											</td>
										</tr>
							<?php
									}
								} 
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/plugins/jquery.dataTables.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/admin/js/plugins/dataTables.bootstrap.min.js'); ?>"></script>
<script type="text/javascript">
	var page_title = "Consent Forms";
	$('#tbl').DataTable();
</script>
<?= $this->endSection(); ?>