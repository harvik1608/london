<?=$this->extend("include/front_header")?>
<?=$this->section("content")?>
<style>
	.lazy {
		width: 266px !important;
		height: 238px !important;
	}
    .gradient {
        background: linear-gradient(to bottom, <?php echo $company['code']; ?> 0%,<?php echo $company['code']; ?> 100%);
    }
    .circle-sap, .circle-sap:before, .circle-sap:after {
        background-color: <?php echo $company['code']; ?> !important;
    }
    .overlay-primary-middle:after {
        background: linear-gradient(to bottom, <?php echo $company['code']; ?> 0%,<?php echo $company['code']; ?> 100%);
    }
    .counter {
        color: <?php echo $company['code']; ?>;
    }
</style>
<div class="page-content">
    <div class="dez-bnr-inr bnr-center dez-bnr-inr-md overlay-black-middle banner-content" style="background-image:url(<?php echo $company['banner']; ?>);">
        <div class="container">
            <div class="dez-bnr-inr-entry text-white">
                <h1 class="text-white text-uppercase">Defining Beauty with style</h1>
				<p class="m-b0 font-weight-600"></p>
				<div class="m-t20">
					<a href="javascript:book_appointment();" class="site-button p-full radius-xl gradient fley button-lg">Book Your Appointment</a>
				</div>
            </div>
        </div>
    </div>
    <div class="section-full bg-white content-inner">
    	<div class="container">
            <div class="section-head text-center">
                <h2 class="heading-top">Welcome To <?php echo $company['company_name']; ?></h2>
                <div class="circle-sap bg-primary"></div>
                <h2 class="heading-bottom"></h2>
            </div>
            <div class="section-content">
                <div class="row">
                    <?php
                        if($company["groups"])
                        {
                            foreach($company["groups"] as $key => $val)
                            {
                    ?>
                                <div class="col-md-3 col-sm-6 m-b30">
                                    <div class="abouts-2">
                                        <div class="dez-media"> 
                                            <a href="<?php echo base_url(); ?>service/<?php echo $val['id']; ?>"><img src="<?php echo $val['avatar']; ?>" alt="<?php echo $val['name']; ?>" class="lazy"></a> 
                                        </div>
                                        <div class="about-info p-a25 text-center">
                                            <h4 class="dez-title m-t0 m-b10">
                                                <a href="<?php echo base_url(); ?>service/<?php echo $val['id']; ?>" class="font-weight-600">
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
    <div class="section-full overlay-primary-middle bg-img-fix content-inner" style="background-image:url(<?php echo base_url('public/frontend/images/background/new_bg.jpg'); ?>);">
        <div class="container">
            <div class="section-content">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="m-b30 text-white text-center">
                            <div class="icon-bx-lg radius m-b20 bg-white">
                                <div class="counter font-26 font-weight-800 text-primary m-b5"><?php echo $company['total_customer']; ?></div>
                            </div>
                            <span class="font-26 text-uppercase clearfix">Customers</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="m-b30 text-white text-center">
                            <div class="icon-bx-lg radius m-b20 bg-white">
                                <div class="counter font-26 font-weight-800 text-primary border-1 radius-xl m-b5"><?php echo $company['total_staff']; ?></div>
                            </div>
                            <span class="font-26 text-uppercase clearfix">Staffs</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="m-b30 text-white text-center">
                            <div class="icon-bx-lg radius m-b20 bg-white">
                                <div class="counter font-26 font-weight-800 text-primary m-b5"><?php echo $company['total_treatment']; ?></div>
                            </div>
                            <span class="font-26 text-uppercase clearfix">Treatments</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="m-b10 text-white text-center">
                            <div class="icon-bx-lg radius m-b20 bg-white">
                                <div class="counter font-26 font-weight-800 text-primary m-b5"><?php echo $company['total_sub_treatment']; ?></div>
                            </div>
                            <span class="font-26 text-uppercase clearfix">Sub Treatments</span>         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection()?>