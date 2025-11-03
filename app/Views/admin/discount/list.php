<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-dashboard"></i> Weekend Discounts</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Weekend Discounts</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>Weekend Discount List <a class="btn btn-sm btn-success" href="<?php echo base_url('discounts/new'); ?>" style="float: right;color: #fff;"><i class="fa fa-plus"></i> New Weekend Discount</a></h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="tbl">
						<thead>
							<tr>
								<th width="5%">No</th>
                                <th width="25%">Name</th>
                                <th width="15%">Start Date</th>
                                <th width="15%">End Date</th>
                                <th width="15%">Percentage (%)</th>
                                <th width="10%">Status</th>
                                <th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($discounts) {
									$no = 0;
									foreach($discounts as $discount) {
										$no++;
							?>
										<tr>
											<td><?php echo $no; ?></td>
											<td><?php echo $discount['name']; ?></td>
											<td><?php echo $discount['sdate']; ?></td>
											<td><?php echo $discount['edate']; ?></td>
											<td><?php echo $discount['percentage']; ?></td>
                                            <td>
                        						<?php
                        							if($discount['is_active'] == 1)
                        								echo '<span class="text-white badge badge-success">Active</span>';
                        							else
                        								echo '<span class="text-white badge badge-danger">Inactive</span>';
                        						?>
                        					</td>
											<td>
												<a class="btn btn-sm btn-success" href="<?php echo base_url('discounts/'.$discount['id'].'/edit'); ?>"><i class="fa fa-edit text-white"></i></a>
												<!-- <a class="btn btn-sm btn-danger" href="javascript:;" onclick="remove_row('<?php echo base_url('discounts/'.$discount['id']); ?>',0,'Are you sure to remove this weekend discount?')"><i class="fa fa-trash text-white"></i></a> -->
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
	var page_title = "Weekday Discounts";
	$('#tbl').DataTable();
</script>
<?= $this->endSection(); ?>