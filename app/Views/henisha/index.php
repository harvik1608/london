<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<link rel="stylesheet" href="<?php echo base_url('public/admin/css/priority-nav-scroller.css'); ?>">
<style>
	.tile {
		margin-bottom: 10px;
	}
	.listing-item-container.list-layout {
    background: transparent;
    background-color: #f9f9f9;
    margin-bottom: 25px;
   }
   .listing-item-container.list-layout .listing-item {
    display: flex;
    background-color: transparent;
    height: 220px;
}
.listing-item {
    overflow: hidden;
}
.listing-item {
    background: #ccc;
    border-radius: 4px 4px 0 0;
    height: 100%;
    display: block;
    position: relative;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: 50%;
    height: 265px;
    z-index: 100;
    cursor: pointer;
}
.listing-item-container.list-layout .listing-item-image {
    min-height: 200px;
    flex: 2;
    height: 100%;
    overflow: hidden;
    border-radius: 4px 0 0 4px;
    position: relative;
}
.listing-item-container.list-layout .listing-item-content {
    flex: 5;
    position: relative;
    bottom: 0;
    left: 0;
    padding: 0;
    width: 100%;
    z-index: 50;
    box-sizing: border-box;
}
</style>
<div class="row">
	<div class="col-lg-12">
		<div class="nav-scroller" style="padding-bottom: 10px;">
            <nav class="nav-scroller-nav">
                <div class="nav-scroller-content">        
                    <?php
                        foreach($states as $state){
                    ?>
                            <a href="#" class="nav-scroller-item"><?php echo $state['name']; ?></a>
                    <?php
                        } 
                    ?>
                </div>
            </nav>
            <button class="nav-scroller-btn nav-scroller-btn--left" aria-label="Scroll left" type="button"><</button>
            <button class="nav-scroller-btn nav-scroller-btn--right" aria-label="Scroll right" type="button">></button>
        </div>
	</div><br><br><br>
	<?php
		if(!empty($hospitals)) {
			foreach($hospitals as $hospital) {
	?>
				<div class="col-md-12">
					<div class="tile">
						<div class="tile-body">
							<h6><i class="fa fa-hospital-o"></i> <?php echo $hospital['name']; ?></h6>
							<p><i class="fa fa-phone"></i> <?php echo $hospital['phone']; ?></p>
							<p><i class="fa fa-map-marker"></i> <?php echo $hospital['address']; ?></p>
							<p><i class="fa fa-map-marker"></i> <?php echo $hospital['hospital_city'].", ".$hospital['hospital_state']; ?></p>
						</div>
					</div>
				</div>
	<?php
			}
		} 
	?>
</div>
<script src="<?php echo base_url('public/admin/js/service_scroll/priority-nav-scroller.js'); ?>"></script>
<?= $this->endSection(); ?>