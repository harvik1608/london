$(document).ready(function(){
	$(document).on("click","#consent-form li",function(){
		$("#consent-form li").find("a").removeClass("active");
		$(this).find("a").addClass("active");
		$("#myTabContent .tab-pane").removeClass("active");
		$("#myTabContent .tab-pane").removeClass("show");			
		$($(this).find("a").attr("href")).addClass("active");
		$($(this).find("a").attr("href")).addClass("show");
	}); 
});
function get_customer_consent_form(flag = 0)
{
	var phone;
	if(flag == 0) {
		phone = $("#customer_phone").val();
	} else {
		phone = $("#walkin_phone").val();
	}
	$.ajax({
        url: base_url+"/get-customer-consent-forms",
        type: 'POST',
        dataType: 'JSON',
        data: {
        	phone:phone
        },
        success:function(response){
            $("#customerConsentFormModal .modal-body").html(response.html);
            $("#customerConsentFormModal").modal({backdrop: 'static',keyboard: false});
        }
    });     
}
function close_customer_consent_form_modal()
{
	var modal = document.getElementById('walkinModal');
	if(modal.style.display === 'block') {
	  	$("#walkinModal").modal({backdrop: 'static',keyboard: false});
	} else {
	  	$("#appointmentModal").modal({backdrop: 'static',keyboard: false});
	}
    $("#customerConsentFormModal").modal("hide");
}