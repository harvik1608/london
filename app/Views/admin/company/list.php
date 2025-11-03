<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-users"></i> Companies</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Companies</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>Company List <a class="btn btn-sm btn-success" href="<?php echo base_url('companies/new'); ?>" style="float: right;color: #fff;"><i class="fa fa-plus"></i> New Company</a></h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="tbl">
						<thead>
							<tr>
								<th width="5%">No</th>
                                <th width="15%">Name</th>
                                <th width="20%">Email</th>
                                <th width="10%">Phone</th>
                                <th width="15%">Timing</th>
                                <th width="15%">Sunday Timing</th>
                                <th width="5%">Status</th>
                                <th width="5%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($companies) {
									$no = 0;
									foreach($companies as $company) {
										$no++;
							?>
										<tr>
											<td><?php echo $no; ?></td>
											<td><?php echo $company['company_name']; ?></td>
											<td><?php echo $company['company_email']; ?></td>
											<td><?php echo $company['company_phone']; ?></td>
											<td><?php echo date('h:i A',strtotime($company['company_stime']))." To ".date('h:i A',strtotime($company['company_etime'])); ?></td>
											<td><?php echo date('h:i A',strtotime($company['company_sunday_stime']))." To ".date('h:i A',strtotime($company['company_sunday_etime'])); ?></td>
											<td>
												<?php
													if($company["isActive"] == '1') {
														echo "ACTIVE";
													} else {
														echo "INACTIVE";
													}
												?>
											</td>
											<td>
												<a class="btn btn-sm btn-success" href="<?php echo base_url('companies/'.$company['id'].'/edit'); ?>"><i class="fa fa-edit text-white"></i></a>
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
	var page_title = "Companies";
	$('#tbl').DataTable();
</script>
<?= $this->endSection(); ?>