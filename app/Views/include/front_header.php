<!DOCTYPE html>
<html lang="en">
	<?php
		$company = company(); 
		if(isset($company["status"]) && $company["status"] == RESPONSE_FLAG_SUCCESS)
		{
			$company = $company["data"];
	?>
			<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="keywords" content="" />
			<meta name="author" content="" />
			<meta name="robots" content="" />
			<meta name="description" content="" />
			<meta property="og:title" content="" />
			<meta property="og:description" content="" />
			<meta property="og:image" content="" />
			<meta name="format-detection" content="telephone=no" />
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<head>
				<link rel="icon" href="<?php echo base_url('public/frontend/images/favicon.ico'); ?>" type="image/x-icon" />
				<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('public/frontend/images/favicon.png'); ?>" />
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/bootstrap/css/bootstrap.min.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/fontawesome/css/font-awesome.min.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/themify/themify-icons.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/flaticon/flaticon.min.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/owl-carousel/owl.carousel.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/bootstrap-select/bootstrap-select.min.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/magnific-popup/magnific-popup.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/animate/animate.min.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/scroll/scrollbar.min.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/css/style.min.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/css/skin/skin-1.min.css'); ?>" class="skin">
				<link  rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/css/templete.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/revolution/v5.4.3/css/settings.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/revolution/v5.4.3/css/navigation.min.css'); ?>">
				<link href="https://fonts.googleapis.com/css?family=Ropa+Sans&display=swap" rel="stylesheet">
				<link rel="stylesheet" href="<?php echo base_url('public/frontend/css/priority-nav-scroller.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/revolution/v5.4.3/css/settings.css'); ?>">
				<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/frontend/plugins/revolution/v5.4.3/css/navigation.css'); ?>">
				<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
				<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
				<link rel="preconnect" href="https://fonts.googleapis.com">
        		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        		<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
				<title></title>
				<style>
				    body, b, h5 {
        				font-family: "Nunito", serif !important;
        				font-optical-sizing: auto;
        				font-weight: 400;
        				font-style: normal;
        			}
				    .nicescroll-cursors {
				        background-color: #736cc7 !important;
				    }
				    .error {
						color: red;
						font-size: 12px;
					}
					.header-nav .nav>li>a:hover {
				        color: <?php echo $company['code']; ?>;
				    }
				    .home-footer-2 .dez-social-icon li a {
				    	color: <?php echo $company['code']; ?>;
				    }
				    .theme-btn.bt-buy-now {
				    	background: linear-gradient(to bottom,<?php echo $company['code']; ?> 0,<?php echo $company['code']; ?> 100%);
				    }
				    .dez-separator {
				    	background-color: <?php echo $company['code']; ?> !important;
				    }
				    h4 a:hover {
				    	color: <?php echo $company['code']; ?>;
				    }
				    button.scroltop {
				    	border-color: <?php echo $company['code']; ?>;
				    	color: <?php echo $company['code']; ?>;
				    }
				    .nicescroll-cursors {
				    	background-color: <?php echo $company['code']; ?> !important;
				    }
				    .btn-success {
				    	background-color: <?php echo $company['code']; ?> !important;
    					border-color: <?php echo $company['code']; ?> !important;
				    }
				    .btn-info {
                        background-color: <?php echo $company['code']; ?> !important;
				    }
				    .abouts-2:hover {
				        border: 2px solid <?php echo $company['code']; ?> !important;
				    }
				    a {
				        color: <?php echo $company['code']; ?> !important;
				    }
				    .treatment-active, .theme-btn {
				        color: #FFF !important;
				    }
				    [class*=icon-bx-][class*=bg-] a, .dez-social-icon.dez-social-icon-lg li a {
				        color: #FFF !important;
				    }
				    .contact-style-1 .dez-social-icon-lg li a:hover {
				        color: <?php echo $company['code']; ?> !important;
				    }
				    .btn-primary {
                        background-color: <?php echo $company['code']; ?> !important;
                        border-color:<?php echo $company['code']; ?> !important;
				    }
				    #header_menu a {
				        color: #000 !important;
				    }
				    .gradient {
				        color: #fff !important;
				    }
				    .select2-selection--single {
				        border-color: #e1e6eb !important;
                        box-shadow: none !important;
                        height: 40px !important;
                        font-size: 13px !important;
                        line-height: 20px !important;
                        padding: 9px 12px !important;   
				    }
				    span.select2-selection__arrow b {
				        margin-top: 5px !important;
				    }
				    .select2-container--default .select2-selection--single .select2-selection__placeholder, .salon-label, input, input::placeholder, textarea::placeholder {
				        color: #000 !important;
				    }
				    .salon-label {
				        font-size: 13px !important;
				    }
				    span.select2-selection__rendered {
				        margin-top: -3px important;
                        margin-left: -6px !important;
				    }
				    .select2-dropdown {
                        z-index: 9999 !important;
                    }
                    #cart_list h5, #cart_list table thead th {
                        font-size: 15px !important;
                        font-weight: bold !important;
                    }
                    .select2-container--default .select2-selection--single .select2-selection__rendered {
                        margin-top: -3px !important;
                    }
                    #cart_list label {
                        margin-bottom: 5px !important;
                    }
                    .ui-datepicker-month, .ui-datepicker-year, .ui-datepicker-week-end, .ui-state-default, .ui-datepicker-calendar span {
                        font-size: 14px !important;
                        font-family: "Nunito", serif !important;
                    }
				</style>
			</head>
			<body>
				<div class="page-wraper">
					<header class="site-header header fullwidth">
					    <div class="top-bar">
		        			<div class="container-fluid">
		        				<div class="row">
		        				    <div class="dez-topbar-left">
		        						<a href="<?php echo base_url(); ?>"><img src="<?php echo $company['company_logo']; ?>" alt="<?php echo $company['company_name']; ?>"></a>
		        					</div>
		        				    <div class="dez-topbar-right topbar-social">
		        						<ul>
		        						    <li>
		        						    	<a href="tel: +<?php echo $company['company_phone']; ?>" title="+<?php echo $company['company_phone']; ?>"><i class="fa fa-phone"></i></a>
		        						    </li>
		        							<li><a href="<?php echo $company['facebook_link']; ?>" target="_blank" class="site-button-link facebook hover"><i class="fa fa-facebook"></i></a></li>
		        							<li><a href="<?php echo $company['google_link']; ?>" target="_blank" class="site-button-link google hover"><i class="fa fa-google-plus"></i></a></li>
		        							<li><a href="<?php echo $company['instagram_link']; ?>" target="_blank" class="site-button-link instagram hover"><i class="fa fa-instagram"></i></a></li>
		        						    <li><a href="<?php echo base_url('privacy-policy'); ?>" title="Privacy Policy" class="site-button-link instagram hover"><i class="fa fa-lock"></i></a></li>
		        						    <li><a href="<?php echo base_url('parking-instructions'); ?>" title="Parking Instructions" class="site-button-link instagram hover"><i class="fa fa-car"></i></a></li>
		        						</ul>
		        					</div>
		        				</div>
		        			</div>
		        		</div>
				        <div class="sticky-header main-bar-wraper">
				            <div class="main-bar clearfix ">
				                <div class="container-fluid clearfix">
				                    <!-- website logo -->
				     <!--               <div class="logo-header mostion">-->
									<!--	<a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>/uploads/<?php echo $company['company_logo']; ?>" alt="<?php echo ucwords($company['company_name']); ?>" style="width:100%;height: 50px;"></a>-->
									<!--</div>-->
				                    <!-- nav toggle button -->
				                    <button data-target=".header-nav" data-toggle="collapse" type="button" class="navbar-toggle collapsed" aria-expanded="false" > 
										<i class="flaticon-menu"></i>
									</button>
									<div class="header-nav navbar-collapse collapse">
										<ul class="nav navbar-nav" id="header_menu">
											<li><a href="<?php echo base_url(); ?>">Home</a></li>
											<li><a href="<?php echo base_url('about-us'); ?>">About</a></li>
											<li><a href="<?php echo base_url('treatments'); ?>">Treatments</a></li>
											<!-- <li><a href="< ?php echo base_url('offers'); ?>">Offers</a></li> -->
											<li><a href="<?php echo base_url('gallery'); ?>">Gallery</a></li>
											<li><a href="<?php echo base_url('contact-us'); ?>">Contact Us</a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</header>
					<?= $this->renderSection("content"); ?>
					<footer class="site-footer bg-img-fix home-footer-2">
				        <div class="footer-top">
				            <div class="container">
				                <div class="row">
				                    <div class="col-md-4 col-sm-12 col-xs-12 m-b30">
										<h2 class="font-weight-700">Location</h2>
										<ul class="add-list">
											<li><?php echo $company['company_address']; ?></li>
											<li><a href="tel: +<?php echo $company['company_phone']; ?>">+<?php echo $company['company_phone']; ?></a></li>
											<li><a href="#" class="site-button-link underline" style="padding-left: 0px;"><?php echo $company['smtp_email']; ?></a></li>
										</ul>
									</div>
				                    <div class="col-md-4 col-sm-12 col-xs-12 m-b30">
										<h2 class="font-weight-700">Connect With Us</h2>
										<ul class="dez-social-icon dez-social-icon-lg">
											<li><a href="<?php echo $company['facebook_link']; ?>" target="_blank"  class="fa fa-facebook border-2 radius-xl text-primary"></a></li>
											<li><a href="<?php echo $company['google_link']; ?>" target="_blank" class="fa fa-google-plus border-2 radius-xl text-primary"></a></li>
											<li><a href="<?php echo $company['instagram_link']; ?>" target="_blank" class="fa fa-instagram border-2 radius-xl text-primary"></a></li>
										</ul>
									</div>
				                </div>
				            </div>
				        </div>
				        <!-- footer bottom part -->
				        <div class="footer-bottom">
				            <div class="container">
								<div class="text-center">
									<!--<span>Designed & Developed By</span>-->
									<!--<a href="javascript:;" class="text-primary"></a>-->
								</div>
				            </div>
				        </div>
				    </footer>
				    <button class="scroltop fa fa-chevron-up" ></button>
				</div>
				<div class="modal fade" id="bookAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		            <div class="modal-dialog modal-lg" role="document">
		                <div class="modal-content">
		                    <form class="needs-validation" action="" method="post" id="bookAppointmentForm" autocomplete="off">
		                        <div class="modal-header">
		                        	<div class="row">
		                        		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
		                        			<h5 class="modal-title" style="color: #000;">Book Appointment</h5>
		                        		</div>
		                        		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
		                        			<a href="#" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal();">
					                            <span aria-hidden="true">&times;</span>
					                        </a>
		                        		</div>
		                        	</div>
		                        </div>
		                        <div class="modal-body">
		                            <div id="cart_msg" style="display: none;" class="alert alert-success"></div>
		                            <div class="row">
		                                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12">
    		                                <?php
        		                                $groups = company_treatments();
        		                                if(!empty($groups)) {
        		                            ?>
        		                                    <div class="nav-scroller">
        		                                        <nav class="nav-scroller-nav">
        		                                            <div class="nav-scroller-content">        
        		                                                <?php
        		                                                    foreach($groups as $group)
        		                                                    {
        		                                                    	$group_name = $group['name'];
        		                                                ?>
        		                                                        <a href="javascript:;" class="nav-scroller-item" onclick="get_sub_services('<?php echo $group['id']; ?>','<?php echo $group['name']; ?>')"><?php echo $group_name; ?></a>
        		                                                <?php
        		                                                    } 
        		                                                ?>
        		                                            </div>
        		                                        </nav>
        		                                        <button class="nav-scroller-btn nav-scroller-btn--left" aria-label="Scroll left" type="button"><</button>
        		                                        <button class="nav-scroller-btn nav-scroller-btn--right" aria-label="Scroll right" type="button">></button>
        		                                    </div>
        		                            <?php
        		                                }
        		                            ?>
        		                        </div>
    		                            <div class="col-xl-7 col-lg-7 col-md-7 col-sm-7 col-7">
		                                    <div id="sub_service_list" style="height: 500px;overflow-y: auto;"></div>
		                                </div>
		                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-5 col-5">
		                                	<div id="cart_list" style="display: none;margin-top: 15px;">
		                                        <h5><b>Total Amount : <?php echo $company['currency']; ?> <span>0</span></b></h5>
		                                        <table class="table table-bordered" style="width:100%">
		                                            <thead>
		                                                <tr>
		                                                    <th style="width: 70%;">Service</th>
		                                                    <th style="width: 20%;">Time</th>
		                                                    <th style="width: 10%;">Amount</th>
		                                                </tr>
		                                            </thead>
		                                            <tbody>
		                                            </tbody>
		                                        </table>
		                                        <div class="row">
		                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
					                                    <label for="validationCustom01" class="salon-label">Appointment Date*</label>
					                                    <input type="hidden" name="staff_ids" id="staff_ids" />
					                                    <input type="text" class="form-control" name="appointment_date" id="appointment_date" placeholder="Select Date" />   
					                                </div>
					                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6" id="booking_time">
					                                    
					                                </div>
					                            </div><br>
					                            <div class="row">
			                                		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
					                                    <label for="validationCustom01" class="salon-label">Customer Name*</label>
					                                    <input type="text" class="form-control" name="customer_name" id="customer_name" placeholder="Name" />   
					                                </div>
					                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6">
					                                    <label for="validationCustom01" class="salon-label">Customer Phone*</label>
					                                    <input type="text" class="form-control" name="customer_phone" id="customer_phone" placeholder="Phone" />   
					                                </div>
					                            </div><br>
					                            <div class="row">
					                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
					                                    <label for="validationCustom01" class="salon-label">Customer Email*</label>
					                                    <input type="text" class="form-control" name="customer_email" id="customer_email" placeholder="Email" />   
					                                </div>
					                           	</div>
					                           	<br>
					                           	<div class="row">
			                                		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
					                                    <label for="validationCustom01" class="salon-label">Note</label>
					                                    <textarea class="form-control" name="customer_note" id="customer_note" placeholder="Note"></textarea>
					                                </div>
					                           	</div>
		                                    </div>
		                                </div>
        		                    </div>
		                        </div>
		                        <div class="modal-footer">
		                            <button class="btn btn-primary" type="submit">Book</button>
		                        </div>
		                    </form>
		                </div>
		            </div>
		        </div>
		        <script type="text/javascript">
		        	var base_url = "<?php echo base_url(); ?>";
		        	var parlour_stime = "<?php echo $company['company_stime']; ?>";
		        </script>
				<script src="<?php echo base_url('public/frontend/js/combine.js'); ?>/"></script>
				<script src="<?php echo base_url('public/frontend/plugins/tilt/tilt.jquery.js'); ?>/"></script>
				<script src="<?php echo base_url('public/frontend/js/moment.js'); ?>"></script>
				<script src="<?php echo base_url('public/admin/js/bootstrap-datetimepicker.js'); ?>"></script>
				<script src="<?php echo base_url('public/frontend/plugins/particles/particles.js'); ?>/"></script>
				<script src="<?php echo base_url('public/frontend/plugins/revolution/v5.4.3/js/jquery.themepunch.tools.min.js'); ?>/"></script>
				<script src="<?php echo base_url('public/frontend/plugins/revolution/v5.4.3/js/jquery.themepunch.revolution.min.js'); ?>/"></script>
				<script src="<?php echo base_url('public/frontend/js/rev.slider.js'); ?>/"></script>
				<script src="<?php echo base_url('public/admin/js/service_scroll/priority-nav-scroller.js'); ?>"></script>
				<script src="<?php echo base_url('public/admin/js/service_scroll/main.js'); ?>"></script>
				<script src="https://rawgit.com/intoro/Lazy_Load_JQuery/master/js/1_9_7_jquery.lazyload.js"></script>
				<script src="<?php echo base_url('public/admin/js/nicescroll.js'); ?>"></script>
				<script src="<?php echo base_url('public/admin/js/sweetalert2/dist/sweetalert2.min.js'); ?>"></script>
				<script src="<?php echo base_url('public/admin/js/jquery.validate.js'); ?>"></script>
				<script src="<?php echo base_url('public/admin/js/additional_methods.js'); ?>"></script>
				<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
				<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
				<script src="<?php echo base_url('public/frontend/js/custom.js'); ?>"></script>
				<script type="text/javascript">
					$(document).ready(function(){
						setTimeout(function(){
							$("#debug-icon").remove();
						},1000);
				    	$("body").niceScroll();

				        var current_page = document.title.split("-");
				        $("#header_menu li").each(function(){
				            if($.trim($(this).text()) == $.trim(current_page[1]))
				                $(this).addClass("active");
				            else
				                $(this).removeClass("active");
				        });
				        if($(".badge-success").length)
				        {
				        	setTimeout(function(){
								$(".badge-success").hide(1000);
							},3000);
				        }
				  	});
				</script>
			</body>
	<?php
		} else if(isset($company["status"]) && $company["status"] == RESPONSE_CLOSE) {
	?>
			<body>
				<center><img src="<?php echo $company["data"]["banner"]; ?>" /></center>
			</body>
	<?php
		}
	?>
</html>