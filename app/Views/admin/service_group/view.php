<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
<style>
	.sort {
		float: right;
	}
</style>
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
				<h4><?php echo format_text(1,$service_group['name']); ?></h4>
			</div><hr>
			<div class="tile-body">
				<div class="table-responsive">
					<table class="table table-hover table-bordered">
						<tbody id="sortable">
							<?php
								if($services) {
									foreach($services as $key => $val) {
							?>
										<tr data-id="<?php echo $val['id']; ?>">
											<td>
												<?php echo format_text(1,$val['name']); ?>
												<a class="sort"><i class="fa fa-sort"></i></a>		
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
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script type="text/javascript">
	var page_title = "Service Groups";
	$(document).ready(function(){
		$("#sortable").sortable({
			update: function(event, ui) {
				let order = [];
				$("#sortable tr").each(function(index, element) {
					order.push($(element).attr('data-id'));
				});
				
				$.ajax({
					url: "<?php echo base_url('update-service-order'); ?>",
					method: 'POST',
					data: { order: order },
					dataType: "json",
					success: function(response) {
						if(response.status == 200) {
							alert(response.message);
						}
					}
				});
			}
		});
	});
</script>
<?= $this->endSection(); ?>