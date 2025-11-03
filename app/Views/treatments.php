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
</style>
<div class="page-content bg-white">    
	<div class="dez-bnr-inr overlay-white-middle tb" style="background-image:url(<?php echo base_url('public/frontend/images/banner/bnr1.jpg'); ?>);">
        <div class="container">
            <div class="dez-bnr-inr-entry">
                <h1 class="text-white">Treatments</h1>
				<!-- Breadcrumb row -->
				<div class="breadcrumb-row">
					<ul class="list-inline">
						<li><a href="#">Home</a></li>
						<li><b>></b> Treatments</li>
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
                        if($treatments)
                        {
                            foreach($treatments as $key => $val)
                            {
                    ?>
                                <div class="col-md-3 col-sm-6 m-b30">
                                    <div class="abouts-2">
                                        <div class="dez-media"> 
                                            <a href="<?php echo base_url('treatment/'.$val['id']); ?>"><img src="<?php echo $val['avatar']; ?>" alt="<?php echo $val['name']; ?>" class="lazy"></a> 
                                        </div>
                                        <div class="about-info p-a25 text-center">
                                            <h4 class="dez-title m-t0 m-b10">
                                                <a href="<?php echo base_url('treatment/'.$val['id']); ?>" class="font-weight-600">
                                                    <?php echo $val['name']; ?>
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