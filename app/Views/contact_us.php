<?=$this->extend("include/front_header")?>
<?=$this->section("content")?>
<style type="text/css">
	.icon-content {
		text-align: justify;
	}
	.breadcrumb-row ul li:after {
		content: unset;
	}
	.badge-success {
		background-color: #736cc7;
		font-size: 14px;
	}
	.badge-danger {
		background-color: #736cc7;
		font-size: 14px;
	}
    .icon-bx-xs, .contact-area .dez-social-icon li a, .site-button {
        background-color: <?php echo $company['code']; ?>;
    }
</style>
<div class="page-content bg-white">
    <div class="dez-bnr-inr overlay-white-middle tb" style="background-image:url(<?php echo base_url('public/frontend/images/banner/bnr1.jpg'); ?>);">
        <div class="container">
            <div class="dez-bnr-inr-entry">
                <h1 class="text-white">Contact Us</h1>
				<!-- Breadcrumb row -->
				<div class="breadcrumb-row">
					<ul class="list-inline">
						<li><a href="#">Home</a></li>
						<li><b>></b> Contact Us</li>
					</ul>
				</div>
            </div>
        </div>
    </div>
    
    <div class="section-full content-inner bg-white contact-style-1 bgeffect" style="background-image:url(<?php echo base_url('public/frontend/images/background/bg12.jpg'); ?>);" data-0="background-position:0px 0px;" data-end="background-position:0px 2000px;">
		<div class="container">
            <div class="row">
            	<div class="col-md-4">
                    <div class="p-a30 m-b30 border-1 contact-area">
						<h2 class="m-b10">Quick Contact</h2>
						<p>If you have any questions simply use the following contact details.</p>
                        <ul class="no-margin">
                            <li class="icon-bx-wraper left m-b30">
                                <div class="icon-bx-xs bg-primary"> <a href="#" class="icon-cell"><i class="fa fa-map-marker"></i></a> </div>
                                <h6 class="text-uppercase m-tb0 dez-tilte">Address:</h6>
                                <div class="icon-content">
                                    <p><small><?php echo $company['company_address']; ?></small></p>
                                </div>
                            </li>
                            <li class="icon-bx-wraper left  m-b30">
                                <div class="icon-bx-xs bg-primary"> <a href="#" class="icon-cell"><i class="fa fa-envelope"></i></a> </div>
                                <div class="icon-content">
                                    <h6 class="text-uppercase m-tb0 dez-tilte">Email:</h6>
                                    <p><small><?php echo $company['company_email']; ?></small></p>
                                </div>
                            </li>
                            <li class="icon-bx-wraper left">
                                <div class="icon-bx-xs bg-primary"> <a href="#" class="icon-cell"><i class="fa fa-phone"></i></a> </div>
                                <div class="icon-content">
                                    <h6 class="text-uppercase m-tb0 dez-tilte">PHONE</h6>
                                    <p><small>+44 <?php echo $company['company_phone']; ?></small></p>
                                </div>
                            </li>
                        </ul>
						<div class="m-t20">
							<ul class="dez-social-icon border dez-social-icon-lg">
								<li><a href="<?php echo $company['facebook_link']; ?>" target="_blank" class="fa fa-facebook bg-primary"></a></li>
								<li><a href="<?php echo $company['google_link']; ?>" target="_blank" class="fa fa-google-plus bg-primary"></a></li>
								<li><a href="<?php echo $company['instagram_link']; ?>" target="_blank" class="fa fa-instagram bg-primary"></a></li>
							</ul>
						</div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="p-a30 bg-gray clearfix m-b30 ">
						<h2>Send Message Us</h2>
						<form method="post" role="form" class="dzForm" action="<?php echo base_url('send_inquiry'); ?>" id="contactForm">
							<?php
								$session = session();
								if($session->getFlashData('success'))
									echo '<div class="row"><div class="col-md-12"><center><div class="badge badge-success">'.$session->getFlashData('success').'</div></center></div></div><br>';

								if($session->getFlashData('error'))
									echo '<div class="row"><div class="col-md-12"><center><div class="badge badge-success">'.$session->getFlashData('success').'</div></center></div></div><br>';
							?>
							<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" name="name" required class="form-control" placeholder="Your Name" required autofocus />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group"> 
										    <input type="text" name="email" class="form-control" placeholder="Your Email" required />
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" name="phone" class="form-control" placeholder="Your Phone" required />
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input name="subject" type="text" class="form-control" placeholder="Your Subject" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <textarea name="message" rows="4" class="form-control" placeholder="Your Message" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" value="Submit" class="site-button"> <span>SUBMIT</span> </button>
                                </div>
                            </div>
                        </form>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<?=$this->endSection()?>