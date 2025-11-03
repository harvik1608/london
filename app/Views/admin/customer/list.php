<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<div class="app-title">
	<div>
		<h1><i class="fa fa-users"></i> Customers</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item">Customers</li>
	</ul>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tile">
			<div class="tile-title">
				<h4>Customer List <a class="btn btn-sm btn-success" href="<?php echo base_url('customers/new'); ?>" style="float: right;color: #fff;"><i class="fa fa-plus"></i> New Customer</a></h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered" id="tbl">
						<thead>
							<tr>
								<th width="5%">No</th>
                                <th width="30%">Name</th>
                                <th width="30%">Email</th>
                                <th width="25%">Mobile No.</th>
                                <th width="10%">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								// if($customers) {
								// 	$no = 0;
								// 	foreach($customers as $customer) {
										// $no++;
							?>
										<!--<tr>-->
										<!--	<td>< ?php echo $no; ?></td>-->
										<!--	<td>< ?php echo $customer['name']; ?></td>-->
										<!--	<td>< ?php echo $customer['email']; ?></td>-->
										<!--	<td>< ?php echo $customer['phone']; ?></td>-->
										<!--	<td>-->
										<!--		<a class="btn btn-sm btn-success" href="< ?php echo base_url('customers/'.$customer['id'].'/edit'); ?>"><i class="fa fa-edit text-white"></i></a>-->
										<!--		<a class="btn btn-sm btn-danger" href="javascript:;" onclick="remove_row('< ?php echo base_url('customers/'.$customer['id']); ?>',0,'Are you sure to remove this customer?')"><i class="fa fa-trash text-white"></i></a>-->
										<!--	</td>-->
										<!--</tr>-->
							<?php
								// 	}
								// } 
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
	var page_title = "Customers";
	// $('#tbl').DataTable();
	$(document).ready(function(){
	    load_data(); 
	});
	function load_data()
    {
    	$('#tbl').DataTable().destroy();
    	$('#tbl').DataTable({
			"serverSide": true, // Enable server-side processing
	    	"processing": true,
	    	"pageLength": 10,
			"ajax":{
	            url: "<?php echo base_url('load-customers'); ?>",
	            type: "post",
	        },
	        "searching": true,
	        "columns": [
		        { "data": 0 },
		        { "data": 1 },
		        { "data": 2 },
		        { "data": 3 },
		        { "data": 4 }
		    ],
		    "order": [[0, "desc"]]
		});
    }
</script>
<?= $this->endSection(); ?>