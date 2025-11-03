<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-photo"></i> Gallery</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Gallery</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>Photo List <a class="btn btn-sm btn-success" href="<?php echo base_url('photos/new'); ?>" style="float: right;color: #fff;"><i class="fa fa-plus"></i> New Photo</a></h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="tbl">
						<thead>
							<tr>
								<th width="5%">No</th>
                                <th width="55%">Photo</th>                                
                                <th width="20%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($photos) {
									$no = 0;
									foreach($photos as $photo) {
										$no++;
							?>
										<tr>
											<td><?php echo $no; ?></td>
											<td><img src="<?php echo $img_path."/".$photo['name']; ?>" class="img img-responsive img-thumbnail" style="width: 125px;height: 125px;" /></td>
											<td>
												<a class="btn btn-sm btn-danger" href="javascript:;" onclick="remove_row('<?php echo base_url('photos/'.$photo['id']); ?>')"><i class="fa fa-trash text-white"></i></a>
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
	var page_title = "Gallery";
	$('#tbl').DataTable();
</script>
<?= $this->endSection(); ?>