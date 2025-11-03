<?= $this->extend('include/header'); ?>
<?= $this->section('main_content'); ?>
<style>
	.bg-primary {
		color: #fff;
	}
	.send-chat {
		float: right;
	}
	.send-chat p {
	    margin-top: 5px;
	    background-color: #009688;
	    padding: 5px;
	    border-radius: 10px;
	    color: #fff;
	}
	.receive-chat span {
		margin-top: 5px;
	    background-color: #009688;
	    padding: 5px;
	    border-radius: 10px;
	    color: #fff;
	}
	.mailbox-messages {
		height: 500px;
		background-image: url(<?php echo base_url('public/uploads/whatsapp_bg.jpg'); ?>);
	}
</style>
<div class="app-title">
	<div>
		<h1><i class="fa fa-comments"></i> Whatsapp</h1>
		<p></p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-comments fs-6"></i></li>
		<li class="breadcrumb-item"><a href="#">Whatsapp</a></li>
	</ul>
</div>
<div class="row">
	<div class="col-md-4">
		<div class="d-grid">
			<input type="text" id="searchCustomer" class="form-control" placeholder="Search customer here..." /><br>
		</div>
		<div class="tile p-0">
			<h4 class="tile-title folder-head">Customers</h4>
			<div class="tile-body">
				<ul class="nav nav-pills flex-column mail-nav">
					<?php
						if($customers) {
							foreach($customers as $customer) {
								$customer_name = "";
								if(!is_null($customer["received_from"])) {
									$customer_name = $customer["received_from"];
								} else {
									if($customer["isManuallyMsg"] == 0) {
										$message = json_decode($customer['message'],true);
										$customer_name = isset($message[0]) ? ucwords(strtolower($message[0])) : "";
									} else {
										$customer_name = $customer["message"];
									}
								}
					?>
								<li class="nav-item" id="customer-<?php echo $customer['id']; ?>">
									<a class="nav-link d-flex justify-content-between align-items-start" href="javascript:;" onclick="open_chat(<?php echo $customer['id']; ?>,'<?php echo $customer['sent_to']; ?>')">
										<span>
											<i class="bi bi-inbox me-1 fs-5"></i> 
											<b><?php echo $customer_name; ?></b> <small>(<?php echo $customer['sent_to']; ?>)</small>
										</span>
										<span class="badge bg-primary"><?php echo $customer['count']; ?></span>
									</a>
								</li>
					<?php
							}
						} 
					?>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-8">
		<div class="tile">
			<div class="mailbox-controls">
				<label></label>
				<input type="hidden" id="sent_to" />
			</div>
			<div class="table-responsive mailbox-messages">
				<table class="table table-hover">
					<tbody>
					</tbody>
				</table>
			</div>
			<textarea placeholder="Type your message here...." class="form-control" id="msg"></textarea><br>
			<button type="button" class="btn btn-sm btn-primary" onclick="send_chat()" id="sendBtn">Send</button>
		</div>
	</div>
</div>
<script type="text/javascript">
	var page_title = "Dashboard";
	var chatInterval;
	$(document).ready(function() {
		chatInterval = setInterval(function(){
			load_chat_history();
		},5000);
	    $("#searchCustomer").on("keyup", function() {
		    search_text(0);
		});
		search_text();
	});
	window.addEventListener('beforeunload', function() {
	    alert(chatInterval);
	    clearInterval(chatInterval);
	});
	function open_chat(id,phone)
	{
		$(".mailbox-controls label").html("<b>"+$("#customer-"+id+" b").text()+" <small>("+phone+")</small></b>");
		$("#sent_to").val(phone);
		load_chat_history(1);
		
	}
	function load_chat_history(isCustomCall)
	{
		var phone = $("#sent_to").val();
		if(phone != "") {
			if(isCustomCall != 1) {
				var $div = $(".mailbox-messages");
				$div.animate({ scrollTop: $div[0].scrollHeight });
			}
			$.ajax({
				url: "<?php echo base_url('get-whatsapp-history'); ?>",
				type: 'GET',
				data:{
					phone: phone
				},
				dataType: "json",
				success:function(response){
					$("html, body").animate({ scrollTop: 0 }, "slow");
					$(".mailbox-messages table tbody").html(response.html);
					// if(isCustomCall == 1) {
						
					// }
				}
			});
		}	
	}
	function search_text()
	{
		var value = $("#searchCustomer").val().toLowerCase();
		$(".mail-nav li").each(function() {
	        var text = $(this).text().trim().toLowerCase();
	        if(text.includes(value)) {
	            $(this).show();
	        } else {
	            $(this).hide();
	        }
	    });
	}
	function send_chat()
	{
		if($.trim($("#msg").val()) != "") {
			$.ajax({
				url: "<?php echo base_url('send-whatsapp-message'); ?>",
				type: 'POST',
				data:{
					phone: $("#sent_to").val(),
					msg: $("#msg").val()
				},
				dataType:"json",
				beforeSend:function(){
					$("#sendBtn").attr("disabled",true).html("Sending...");
				},
				success:function(response){
					if(response.status == 1) {
						$("#sendBtn").attr("disabled",false).html("Send");
						open_chat(response.id,$("#sent_to").val());
						$("#msg").val("");
					}
				}
			});
		}
	}
</script>
<?= $this->endSection(); ?>