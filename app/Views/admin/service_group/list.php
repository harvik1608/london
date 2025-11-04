<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-dashboard"></i> Service Groups</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Service Groups</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>Service Group List <a class="btn btn-sm btn-success" href="<?php echo base_url('service_groups/new'); ?>" style="float: right;color: #fff;"><i class="fa fa-plus"></i> New Service Group</a></h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="tbl">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="10%">Photo</th>
								<th width="35%">Name</th>
								<th width="10%">Color</th>
								<th width="10%">Position</th>
								<th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($service_groups) {
									$no = 0;
									foreach($service_groups as $service_group) {
										$no++;
										$avatar = base_url("public/uploads/service_group/default.webp");
										if($service_group["avatar"] != "") {
											$avatar = base_url("public/uploads/service_group/".$service_group["avatar"]);
										}
							?>
										<tr>
											<td><?php echo $no; ?></td>
											<td><img src="<?php echo $avatar; ?>" class="img img-responsive img-thumbnail" style="width: 125px;height: 100px;" /></td>
											<td><?php echo $service_group['name']; ?></td>
											<td><?php echo $service_group['color']; ?></td>
											<td><?php echo $service_group['position']; ?></td>
											<td>
												<a class="btn btn-sm btn-success" href="<?php echo base_url('service_groups/'.$service_group['id'].'/edit'); ?>"><i class="fa fa-edit text-white"></i></a>
												<a class="btn btn-sm btn-info" href="<?php echo base_url('service_groups/'.$service_group['id']); ?>"><i class="fa fa-sort text-white"></i></a>
												<!-- <a class="btn btn-sm btn-danger" href="javascript:;" onclick="remove_row('<?php echo base_url('service_groups/'.$service_group['id']); ?>',0,'Are you sure to remove this service group?')"><i class="fa fa-trash text-white"></i></a> -->
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
	var page_title = "Service Groups";
	$('#tbl').DataTable();
</script>
<?= $this->endSection(); ?>