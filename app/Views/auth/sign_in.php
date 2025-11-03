<!DOCTYPE html>
<html lang="en"> 
	<head>   
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Beauty Parlour</title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/auth/css/bootstrap.min.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/auth/releases/v6.1.1/css/all.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/auth/css/style.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/auth/css/responsive.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/auth/css/animation.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('public/admin/css/js-snackbar.css?v=1.3'); ?>" />
		<style>
			.form-buttons button i {
				margin-left: 0px !important;
				margin-right: 0px !important;
			}
			a {
				text-decoration: none;
			}
		</style>
	</head>
	<body class="show-section">
		<form class="show-section" id="steps" method="post" action="<?php echo base_url('check-sign-in'); ?>">
			<section class="steps step-1" id="step-1">
				<?php 
					$session = session();
					if($session->getFlashData('success')) {
						echo '<span class="alert alert-success" hidden>'.$session->getFlashData('success').'</span>';
					}
					if($session->getFlashData('error')) {
						echo '<span class="alert alert-danger" hidden>'.$session->getFlashData('error').'</span>';
					}
				?>
				<div class="container">
					<div class="ms-auto col-md-12 col-lg-7">
						<div class="steps-inner pop-slide">
							<div class="wrapper">
								<div class="step-heading">
									<h2>Beauty Parlour</h2>
								</div>
								<div>
									<div class="form-heading">Email</div>
									<div class="form-inner">
										<input type="text" class="form-control" name="email" id="email" placeholder="Your Email" required />
									</div>
									<div class="form-heading">Password</div>
									<div class="form-inner">
										<input type="password" class="form-control" name="password" id="password" placeholder="Your Password" required />
									</div>
		
									<!-- next-prev-btn -->
									<div class="form-buttons">
										<button type="submit" class="next">Sign In<i class="fa-solid fa-arrow-right"></i></button>
									</div><br>
									<!-- <p>Don't have an account? <a href="<?php echo base_url('sign-up'); ?>">Click here</a></p> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</form>
		<script src="<?php echo base_url('public/auth/js/bootstrap.min.js'); ?>"></script>
		<script src="<?php echo base_url('public/auth/jquery-3.6.0.min.js'); ?>"></script>
		<script src="<?php echo base_url('public/admin/js/js-snackbar.js?v=1.3'); ?>"></script>
		<script src="<?php echo base_url('public/auth/js/custom.js'); ?>"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				if($("span.alert-success").length) {
	                toast($("span.alert-success").html(),"success");
	            }
	            if($("span.alert-danger").length) {
	                toast($("span.alert-danger").html(),"danger");
	            }

	            $(document).on("submit","#steps",function(e){
					e.preventDefault();

					$.ajax({
						url: $("#steps").attr("action"),
						type: "post",
						data: new FormData(this),
						processData: false,
						contentType: false,
						dataType: "json",
						beforeSend:function(){
							$("button[type=submit]").attr("disabled",true).css("opacity","0.6");
						},
						success:function(response){
							if(response.status == 0) {
								toast(response.message,"danger");
							} else {
								window.location.href = response.href;
							}
						},
						complete:function(){
							$("button[type=submit]").attr("disabled",false).css("opacity","1");
						}
					});
				});
	      	});

			function toast(message,status)
            {
                SnackBar({
                    message: message,
                    status: status,
                    position: "bc"
                });
            }
		</script>
	</body>
</html>