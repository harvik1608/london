<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-users"></i> Staffs</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Staffs</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>Staff List <a class="btn btn-sm btn-success" href="<?php echo base_url('staffs/new'); ?>" style="float: right;color: #fff;"><i class="fa fa-plus"></i> New Staff</a></h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="tbl">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="20%">First Name</th>
								<th width="20%">Last Name</th>
								<th width="25%">Email</th>
								<th width="10%">Mobile No.</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($staffs) {
									$no = 0;
									foreach($staffs as $staff) {
										$no++;
							?>
										<tr>
											<td><?php echo $no; ?></td>
											<td><?php echo $staff['fname']; ?></td>
											<td><?php echo $staff['lname']; ?></td>
											<td><?php echo $staff['email']; ?></td>
											<td><?php echo $staff['phone']; ?></td>
											<td>
												<a class="btn btn-sm btn-success" href="<?php echo base_url('staffs/'.$staff['id'].'/edit'); ?>"><i class="fa fa-edit text-white"></i></a>
												<!-- <a class="btn btn-sm btn-danger" href="javascript:;" onclick="remove_row('< ?php echo base_url('staffs/'.$staff['id']); ?>',0,'Are you sure to remove this staff?')"><i class="fa fa-trash text-white"></i></a> -->
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
	var page_title = "Staffs";
	$('#tbl').DataTable();
</script>
<?= $this->endSection(); ?>