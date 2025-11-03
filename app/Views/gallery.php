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
                <h1 class="text-white">Gallery</h1>
				<!-- Breadcrumb row -->
				<div class="breadcrumb-row">
					<ul class="list-inline">
						<li><a href="#">Home</a></li>
						<li><b>></b> Gallery</li>
					</ul>
				</div>
            </div>
        </div>
    </div>
    
    <div class="content-area bgeffect" style="padding-top: 25px;">
        <div class="container">
            <div class="section-content">
                <div class="row">
					<?php
	                    if($photos)
	                    {
	                    	echo '<ul id="masonry" class="dez-gallery-listing gallery-grid-4 mfp-gallery">';
	                    	foreach($photos as $photo)
	                    	{
	                ?>

		                		<li class="home card-container col-lg-4 col-md-4 col-sm-6 col-xs-6">
	                                <div class="dez-box dez-gallery-box">
	                                    <div class="dez-thum dez-img-overlay1 dez-img-effect zoom-slow">
	                                        <a href="javascript:void(0);"><img src="<?php echo $photo['name']; ?>" alt="<?php echo $photo['name']; ?>" class="lazy" /></a>
	                                        <div class="overlay-bx">
	                                            <div class="overlay-icon"> 
	                                                <a href="<?php echo $photo['name']; ?>" class="mfp-link" title=""><i class="fa fa-picture-o icon-bx-xs"></i></a>
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </li>
	                <?php
	                		}
	                		echo '</ul>';
	                    } else {
	                    	echo "<div class='alert alert-warning'>There is no photo available.</div>";       
	                    }
	               	?>	                
				</div>
			</div>
        </div>
    </div>
</div>
<?=$this->endSection()?>