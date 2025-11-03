<?=$this->extend("include/front_header")?>
<?=$this->section("content")?>
<style>
	.lazy {
		width: 266px !important;
		height: 238px !important;
	}
	.breadcrumb-row ul li:after {
		content: unset;
	}
	span.btn-info {
		background-color: <?php echo $treatments['code']; ?>;
    	border: 1px solid <?php echo $treatments['code']; ?>;
	}
	.treatment {
		border: 1px solid <?php echo $treatments['code']; ?>;
		padding: 10px;
		border-radius: 10%;
	}
	.treatment-active {
		border: 1px solid <?php echo $treatments['code']; ?>;
		padding: 10px;
		border-radius: 10%;
		background-color: <?php echo $treatments['code']; ?>;
		color: #FFFFFF;
	}
	a {
	    font-size: 12px !important;
	    font-weight: bold !important;
	}
	.abouts-2 {
	    box-shadow: 0 0 4px 2px #efefef;
        border: 2px solid #efefef;
	}
</style>
<div class="page-content bg-white">    
	<div class="dez-bnr-inr overlay-white-middle tb" style="background-image:url(<?php echo base_url('public/frontend/images/banner/bnr1.jpg'); ?>);">
        <div class="container">
            <div class="dez-bnr-inr-entry">
                <h1 class="text-white"><?php echo $treatments['treatment_name']; ?></h1>
				<!-- Breadcrumb row -->
				<div class="breadcrumb-row">
					<ul class="list-inline">
						<li><a href="<?php echo base_url(); ?>">Home</a></li>
						<li><b>></b> <a href="<?php echo base_url('treatments'); ?>">Treatments</a></li>
						<li><b>></b> <?php echo $treatments['treatment_name']; ?></li>
					</ul>
				</div>
            </div>
        </div>
    </div>
    <div class="section-full bg-white content-inner">
    	<div class="container">
            <div class="section-content">
                <div class="row">
                    <?php
                        if($treatments["sub_treatments"]) {
                            foreach($treatments["sub_treatments"] as $key => $val) {
                    ?>
                                <div class="col-md-3 col-sm-6 m-b30">
                                    <div class="abouts-2">
                                        <div class="about-info p-a25 text-center">
                                            <h4 class="dez-title m-t0 m-b10">
                                                <a href="javascript:;" onclick="open_booking_modal()" class="font-weight-600">
                                                    <?php echo strtoupper(strtolower($val['name'])); ?>
                                                </a>
                                            </h4>
                                            <div class="dez-separator bg-primary"></div>
                                        </div>
                                    </div>
                                </div> 
                    <?php
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection()?>