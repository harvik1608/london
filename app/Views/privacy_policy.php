<?=$this->extend("include/front_header")?>
<?=$this->section("content")?>
<style type="text/css">
	.icon-content {
		text-align: justify;
	}
	.breadcrumb-row ul li:after {
		content: unset;
	}
</style>
<div class="page-content bg-white">
    <div class="dez-bnr-inr overlay-white-middle tb" style="background-image:url(<?php echo base_url('public/frontend/images/banner/bnr1.jpg'); ?>);">
        <div class="container">
            <div class="dez-bnr-inr-entry">
                <h1 class="text-white"><?php echo $title; ?></h1>
				<!-- Breadcrumb row -->
				<div class="breadcrumb-row">
					<ul class="list-inline">
						<li><a href="#">Home</a></li>
						<li><b>></b> <?php echo $title; ?></li>
					</ul>
				</div>
            </div>
        </div>
    </div>
    
    <div class="content-area bgeffect" style="padding-top: 25px;">
        <div class="container">
            <div class="section-content">
                <div class="row">
					<div class="col-md-12 col-sm-6">
						<div class="icon-bx-wraper">
							<div class="icon-md text-black m-b20"> 
								<a href="#" class="icon-cell text-black"></a> 
							</div>
							<div class="icon-content m-b30">
								<?php
									echo $privacy_policy; 
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
<?=$this->endSection()?>