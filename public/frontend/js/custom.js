$(document).ready(function(){
    $('img.lazy').lazyload({
        effect: "fadeIn"
    });

    $("#bookAppointmentForm").validate({
        rules:{
            appointment_date:{
                required: true
            },
            appointment_time:{
                required: true
            },
            customer_name: {
                required: true
            },
            customer_phone:{
                required: true,
            },
            customer_email:{
                required: true,
                email: true
            }
        },
        messages:{
            appointment_date:{
                required: "<small class='error'><i class='fa fa-warning'></i> Select appointment date</small>"
            },
            appointment_time:{
                required: "<small class='error'><i class='fa fa-warning'></i> Select appointment time</small>"
            },
            customer_name: {
                required: "<small class='error'><i class='fa fa-warning'></i> Enter customer name</small>"
            },
            customer_phone: {
                required: "<small class='error'><i class='fa fa-warning'></i> Enter customer phone</small>"
            },
            customer_email:{
                required: "<small class='error'><i class='fa fa-warning'></i> Enter customer email</small>",
                email: "<small class='error'><i class='fa fa-warning'></i> Enter valid customer email</small>"
            }
        }
    });

    $("#bookAppointmentForm").submit(function(e){
        e.preventDefault();

        if($("#bookAppointmentForm").valid())
        {
            if($("#appointment_time").val() == "" && $("#cart_list table tbody tr").length > 0)
            {
                alert("Select appointment time");
                return false;
            }
            if($("#cart_list table tbody tr").length === 0)
            {
                alert("Your cart is empty. Add service in your cart first.");
                return false;   
            }
            $.ajax({
                url: base_url+"/book_appointment_from_website",
                type: 'post',
                dataType: 'json',
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                beforeSend:function(){
                    $("#bookAppointmentForm button[type=submit]").html("Please wait...").prop("disabled",true);
                },
                success:function(response){
                    alert(response.message);
                    
                    if(response.status == 1)
                        location.reload();
                    else 
                        $("#bookAppointmentForm button[type=submit]").html("Book").prop("disabled",false);
                }
            });
        }
    });
    
    $("#appointment_date").change(function(){
        fetch_slots(); 
    });
});
function fetch_slots()
{
    $.ajax({
        url: base_url+"fetch-slots",
        type: 'post',
        data: {
            date: $("#appointment_date").val(),
            service_id: 89,
            duration: 40 
        },
        dataType: "json",
        success:function(response){
            $("#staff_ids").val(response.staff_ids);
            var content = "";
            content += '<label for="validationCustom01" class="salon-label">Appointment Time*</label>';
            content += '<select class="form-control select2" name="appointment_time" id="appointment_time">';
                content += "<option value=''>Select time</option>";
                if(response.status == 200) {
                    if(response.slots.length > 0) {
                        for(var i = 0; i < response.slots.length; i ++) {
                            content += '<option value="'+response.slots[i].stime+'">'+response.slots[i].stime+'</option>';
                        }
                    }
                }
            content += '</select>';
            content += '<label id="appointment_time-error" class="error" for="appointment_time" style="display: none;"></label>';
            $('#booking_time').html(content);
            $('.select2').select2({
                placeholder: "Select Time", // Optional
                allowClear: true                 // Optional
            });
        }
    });
}
function book_appointment()
{
    $(".modal-header").css("background","#FFF");
    $("#bookAppointmentModal").modal({backdrop: 'static',keyboard: false});
}
function get_sub_services(serviceId,serviceNm,flag = 0)
{
    $.ajax({
        url: base_url+"all_sub_services",
        type: 'post',
        dataType: 'json',
        data: {
            serviceId: serviceId,
            serviceNm: serviceNm,
            flag: flag
        },
        success:function(response){
            $("#sub_service_list").html(response.content);
        }
    });
}
function add_to_cart(serviceId,serviceSubId,serviceNm,caption,price,duration,flag)
{
    var element = "cart_list";
    var stime = "";
    if(parseInt($("#"+element+" table tbody tr[class=exist_"+serviceId+"_"+serviceSubId+"]").length) === 0)
    {
        if(parseInt(flag) === 0)
            stime = parlour_stime;
        else 
            stime = parlour_stime; 

        var no = 0;            
        if($("#"+element+" table tbody tr").length !== 0)
        {
              let time = $("#"+element+" table tbody tr:last td:eq(1) span").text().split(" ");
              stime = add_minutes(time[0],$("#"+element+" table tbody tr:last td:eq(1) small").text()); 
              ntime = add_minutes(stime,duration); 
              no = $("#"+element+" table tbody tr").length; 
        } else {
              ntime = add_minutes(stime,duration); 
        }
        $.ajax({
              url: base_url+"add_service_in_cart",
              type: 'post',
              dataType: 'json',
              data: {
                    serviceId: serviceId,
                    serviceSubId: serviceSubId,
                    serviceNm: serviceNm,
                    caption: caption,
                    price: price,
                    stime: stime,
                    duration: duration,
                    no:no,
                    ntime:ntime,
                    appointmentDate: $("#appointment_date").val(),
                    flag: flag
              },
              success:function(response){
                    if(response.status == 1)
                    {
                        $("#cart_msg").html(serviceNm+ " added in your cart").show(200);
                        setTimeout(function(){
                            $("#cart_msg").html("").hide(200);
                        },3000);
                        $("#"+element).show(500);
                        // Datepicker
				        apply_datepicker(response.available_dates);
                        $("#"+element+" table tbody").append(response.content);
                        get_cart_total(element);
                    } else {
                        alert(response.message);
                    }
              }
        });
    } else {
        alert("Service already added in your cart.");
    }
}
function apply_datepicker(available_dates)
{
    var enabledDates = [];
    if(available_dates.length > 0) {
        for(var i = 0; i < available_dates.length; i ++) {
            enabledDates.push(available_dates[i]);   
        }
    }
    function enableSpecificDates(date) {
        var formattedDate = $.datepicker.formatDate('yy-mm-dd', date);
        return [enabledDates.includes(formattedDate)];
    }
    $("#appointment_date").datepicker({
        beforeShowDay: enableSpecificDates,
        minDate: new Date()
    });
}
function get_cart_total(element = "cart_list")
{
    if($("#"+element+" table tbody tr").length > 0)
    {
        var total = 0;
        $("#"+element+" table tbody tr").each(function(){
            total += parseFloat($(this).find("td:eq(2) span").text());
        });
        $("#"+element+" h5 span").text(total);
    }
}
function add_minutes(time,minutes)
{
      let added_minute = moment.utc(time,'hh:mm').add(parseInt(minutes),'minutes').format('hh:mm A');
      return added_minute;
}
function closeModal()
{
    $("#appointment_time").val($("#appointment_time option:first").val());
    $("#appointment_date,#customer_name,#customer_phone,#customer_email,customer_note").val('');
    $("#cart_list table tbody,#sub_service_list").html("");    
    $("#cart_list h5 span").text(0);
    $("#cart_list").hide();    
}
function open_modal(serviceId,serviceSubId,serviceNm,caption,price,duration,flag,mainServiceId)
{
    $("#bookAppointmentModal").modal({backdrop: 'static',keyboard: false});
    // get_sub_services(mainServiceId,serviceNm);
    // add_to_cart(serviceId,serviceSubId,serviceNm,caption,price,duration,flag);
}
function open_booking_modal()
{
    $("#bookAppointmentModal").modal({backdrop: 'static',keyboard: false});
}