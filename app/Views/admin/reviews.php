<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-users"></i> Reviews</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Reviews</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>Review List</h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="tbl">
						<thead>
							<tr>
								<th width="5%">No</th>
                                <th width="5%">Rate</th>
                                <th width="25%">Comment</th>
                                <th width="15%">Given By</th>
                                <th width="15%">Given On</th>
                                <th width="10%">Status</th>
                                <th width="20%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($reviews) {
									$no = 0;
									foreach($reviews as $review) {
										$no++;
							?>
										<tr>
											<td><?php echo $no; ?></td>
											<td><?php echo $review['star']; ?></td>
											<td><?php echo $review['comment']; ?></td>
											<td><?php echo $review['given_by']; ?></td>
											<td><?php echo date("d M, Y",strtotime($review['created_at'])); ?></td>
											<td><?php echo $review['is_approved'] == 1 ? "Approved" : "Pending"; ?></td>
											<td>
												<?php
													if($review["is_approved"] == 0) {
												?>
														<a class="btn btn-sm btn-success" href="<?php echo base_url('approve-review/'.$review['id']); ?>" onclick="return confirm('Are you sure to approve this review?')">Approve</a>
												<?php
													} 
												?>
												<a class="btn btn-sm btn-danger" href="<?php echo base_url('remove-review/'.$review['id']); ?>" onclick="return confirm('Are you sure to remove this review?')"><i class="fa fa-trash text-white"></i></a>
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
	var page_title = "Reviews";
	$('#tbl').DataTable();
</script>
<?= $this->endSection(); ?>