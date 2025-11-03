var target = $('#calendar');
$(document).ready(function(){    
    if($("#calendar").length)
    {
        $("#checkout-appointment").click(function(){
            if($(this).prop("checked") == true && $("#pending-appointment").prop("checked") == true) {
                window.location.href = "dashboard?is_check=1&pending_appointment=1";
            } else if($(this).prop("checked") == true && $("#pending-appointment").prop("checked") == false) {
                window.location.href = "dashboard?is_check=1";
            } else if($(this).prop("checked") == false && $("#pending-appointment").prop("checked") == true) {
                window.location.href = "dashboard?pending_appointment=1";
            } else {
                window.location.href = "dashboard?is_check=0";
            }
        });

        $("#pending-appointment").click(function(){
            if($(this).prop("checked") == true && $("#checkout-appointment").prop("checked") == true) {
                window.location.href = "dashboard?is_check=1&pending_appointment=1";
            } else if($(this).prop("checked") == true && $("#checkout-appointment").prop("checked") == false) {
                window.location.href = "dashboard?pending_appointment=1";
            } else if($(this).prop("checked") == false && $("#checkout-appointment").prop("checked") == true) {
                window.location.href = "dashboard?is_check=1";
            } else {
                window.location.href = "dashboard?pending_appointment=0";
            }
        });

        var date = new Date();
        var m = date.getMonth();
        var y = date.getFullYear();
        setInterval(function(){
            target.fullCalendar('refetchEvents');
        },10000);
        target.fullCalendar({
            defaultView: "agendaDay",
            locale: 'en',
            buttonText: {
                day: 'Refresh' 
            },
            defaultDate: default_date,
            editable: true,
            selectable: true,
            timeZone: 'Europe/London',
            eventLimit: true,
            header: {
                left: 'prev,next,today',
                center: 'title',
                right: 'agendaDay'
            },
            allDaySlot: false,
            // esources: today_employees,
            refetchResourcesOnNavigate: true,
            events: fetch_events,
            minTime: company_stime,
            maxTime: company_etime,
            slotDuration: '00:05:00',
            nowIndicator: true,
            views: {
                agendaDay: {
                    titleFormat: 'MMMM D, YYYY dddd'
                }
            },
            resources: function(callback){
                var calendar_view = $('#calendar').fullCalendar('getView');
                if(calendar_view.name == "agendaDay") {
                    var viewDateISO = target.fullCalendar('getDate').format('YYYY-MM-DD');

                    var date = calendar_view.title === null ? "today" : calendar_view.title;
                    setTimeout(function(){
                        $.ajax({
                            url: base_url+"/today-employees",
                            type: 'post',
                            dataType: 'json',
                            data: {date:$(".fc-center h2").text()},
                            success:function(resources){
                                callback(resources);

                                $.ajax({
                                    url: base_url + "/get-available-staff-time",
                                    type: "post",
                                    dataType: "json",
                                    data: { date: $(".fc-center h2").text() },
                                    success: function(availableTimes) {
                                        var fullDayStart = viewDateISO+"T"+company_stime;
                                        var fullDayEnd = viewDateISO+"T"+company_etime;
                                    
                                        var staffIds = [...new Set(availableTimes.map(t => t.staff_id))];
                                        $.each(staffIds, function(index, staffId) {
                                            // alert(fullDayStart);
                                            target.fullCalendar('renderEvent', {
                                                start: fullDayStart,
                                                end: fullDayEnd,
                                                rendering: "background",
                                                resourceId: staffId,
                                                className: "unavailable-slot",
                                                allDay: false
                                            }, true);
                                        });
                                        $.each(availableTimes, function(index, timeSlot) {
                                            // alert(timeSlot.start_time);
                                            target.fullCalendar('renderEvent', {
                                                start: viewDateISO+"T"+timeSlot.start_time,
                                                end: viewDateISO+"T"+timeSlot.end_time,
                                                rendering: "background",
                                                resourceId: timeSlot.staff_id,
                                                className: "available-slot-line",
                                                allDay: false
                                            }, true);
                                        });
                                    }
                                });
                            }
                        });
                    },100);    
                }
            },
            dayClick: function(date, jsEvent, view,resourceObj) {
                var cal_view = $('#calendar').fullCalendar('getView');
                    
                $.ajax({
                    url: base_url+"/check-past-appointment",
                    type: 'post',
                    data: {adate: convertDate(date,"YYYY-MM-DD"),atime:convertDate(date,"HH:mm")},
                    dataType: 'json',
                    success:function(response){
                        if(response.status == 1)
                        {
                            $("#save_checkout_btn").hide();
                            if(cal_view.name == "agendaDay" && typeof resourceObj !== "undefined")
                                $("#resourceID").val(resourceObj.id);
                                
                            $("#appointment_date").val(convertDate(date,"YYYY-MM-DD"));
                            set_time(date);
                            $("#uniq_id").val(response.uniq_id);
                            $("#appointmentModal").modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                        } else {
                            alert("You can not do appointment in past time");   
                        }
                    }
                });
            },
            eventClick: function(event, element) {
                view_appointment(event.id);
            },
            eventDrop:function(event,date){
                var view = $('#calendar').fullCalendar('getView');
                let staff_id = 0;
                if(view.name == "agendaDay")
                    staff_id = event.resourceId;

                $.ajax({
                    url: base_url+"drop-appointment",
                    type: 'post',
                    dataType: 'json',
                    data:{
                        cart_id: event.id,
                        new_sdate: convertDate(event.start,"YYYY-MM-DD"),
                        new_stime: convertDate(event.start,"HH:mm"),
                        new_etime: convertDate(event.end,"HH:mm"),
                        staff_id: staff_id
                    },
                    success:function(response){
                        if(response.status === 400) {
                            alert(response.message);
                            $('#calendar').fullCalendar('refetchEvents');
                        }
                    }
                });
            },
            eventRender: function(event, element) {
                if (event.customer_name && event.customer_phone) {
                    var timeEl = element.find('.fc-time');
                    var newHtml = timeEl.text() + " : " + event.customer_name+" ("+event.customer_phone+")";
                    // timeEl.html("");
                    timeEl.html(newHtml);
                }
            },
            viewRender:function(view, element){
                var calendar_view = $('#calendar').fullCalendar('getView');
                var curTime1 = new Date(calendar_view.title);
                if(view.name == "agendaDay") 
                {
                    var dt = moment(curTime1, "YYYY-MM-DD HH:mm:ss");
                    $("#dayfromdate").text(dt.format('dddd'));
                } else {
                    $("#dayfromdate").text("");
                }
                setTimeout(function() {
                    // FullCalendar v3 now indicator selector
                    var nowLine = element.find('.fc-now-indicator-line, .fc-now-indicator');
                    if (nowLine.length) {
                        var container = element.find('.fc-scroller');
                        if (container.length) {
                            var offsetTop = nowLine.position().top - (container.height() / 2);
                            container.scrollTop(offsetTop);
                        }
                    }
                }, 100); // wait a moment for the now line to be drawn
            }
        });
    }
    $('.fc-daygrid-day').css('background-color', '#999999');
    $('.fc-timegrid-slot').css('background-color', '#999999');
    $("#go_to_date").change(function(){
        var newDate = $(this).val() == "" ? default_date : $(this).val(); // Set the new date
        target.fullCalendar('gotoDate', newDate);
    });
    // $('#customer_phone').select2({
    //     dropdownParent: $('#appointmentModal'),
    //     placeholder: 'Search phone here...',
    //     tags: true,
    //     minimumInputLength: 1, // start searching after 2 characters
    //     ajax: {
    //         url: base_url+"/get-customer-info", // change this to your actual URL
    //         type: "POST",
    //         dataType: 'json',
    //         delay: 250, // delay in ms while typing
    //         data: function (params) {
    //             return {
    //                 column: 'phone',
    //                 phone: params.term // search term
    //             };
    //         },
    //         processResults: function (data) {
    //             return {
    //                 results: data.items // must be an array of { id, text }
    //             };
    //         },
    //         cache: true
    //     }
    // });
    // $('#customer_name').select2({
    //     dropdownParent: $('#appointmentModal'),
    //     placeholder: 'Search name here...',
    //     tags: true,
    //     minimumInputLength: 1, // start searching after 2 characters
    //     ajax: {
    //         url: base_url+"/get-customer-info", // change this to your actual URL
    //         type: "POST",
    //         dataType: 'json',
    //         delay: 250, // delay in ms while typing
    //         data: function (params) {
    //             return {
    //                 column: 'name',
    //                 name: params.term,
    //             };
    //         },
    //         processResults: function (data) {
    //             return {
    //                 results: data.items // must be an array of { id, text }
    //             };
    //         },
    //         cache: true
    //     }
    // });
    // $("#customer_phone").change(function(){
    //     if($(this).val() != "") {
    //         $.ajax({
    //             url: base_url+"/get-customer-info-by-id",
    //             type: "POST",
    //             data:{
    //                 customer_id: $(this).val()
    //             },
    //             dataType: "json",
    //             success:function(response){
    //                 // $("#customer_name").val(response.customer.name).trigger("change");
    //                 if ($("#customer_name option[value='" + response.customer.name + "']").length === 0) {
    //                     // If not, append it (for Select2 to recognize it)
    //                     var newOption = new Option(response.customer.name, response.customer.name, true, true);
    //                     $("#customer_name").append(newOption).trigger('change');
    //                 } else {
    //                     // If it exists, just set the value
    //                     $("#customer_name").val(response.customer.name).trigger('change');
    //                 }
    //                 $("#customer_email").val(response.customer.email);
    //                 $("#total_app").val(response.total);
    //                 $("#no_show_app").val(response.total_no_show);
    //             }
    //         });
    //     } 
    // });
    // $("#customer_name").change(function(){
    //     // if($(this).val() != "") {
    //     //     $.ajax({
    //     //         url: base_url+"/get-customer-info-by-id",
    //     //         type: "POST",
    //     //         data:{
    //     //             customer_id: $(this).val()
    //     //         },
    //     //         dataType: "json",
    //     //         success:function(response){
    //     //             if ($("#customer_name option[value='" + response.customer.phone + "']").length === 0) {
    //     //                 var newOption = new Option(response.customer.phone, response.customer.phone, true, true);
    //     //                 $("#customer_phone").append(newOption).trigger('change');
    //     //             } else {
    //     //                 $("#customer_phone").val(response.customer.name).trigger('change');
    //     //             }
    //     //             $("#customer_email").val(response.customer.email);
    //     //             $("#total_app").val(response.total);
    //     //             $("#no_show_app").val(response.total_no_show);
    //     //         }
    //     //     });
    //     // } 
    // });
    // $("#customer_phone,#customer_name").keyup(function(){
    //     if($(this).val().length === 0) {
    //         // $("#customer_phone,#customer_name,#customer_email,#customer_note").val('');
    //     }
    // });
    $('.fc-button-prev').click(function(){
        var dt = moment($(".fc-header span.fc-header-title h2").text(),'MMMDD,YYYY').format("YYYY-MM-DD");
        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var d = new Date(dt);
        $("#dayfromdate").text(days[d.getDay()]);
    });
    $('.fc-button-next').click(function(){
        var dt = moment($(".fc-header span.fc-header-title h2").text(),'MMMDD,YYYY').format("YYYY-MM-DD");
        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var d = new Date(dt);
        $("#dayfromdate").text(days[d.getDay()]);
    });
    
    $("#appointmentForm").validate({
        rules:{
            customer_phone:{
                required: true
            },
            customer_name:{
                required: true
            },
            customer_email:{
                email: true
            }
        },
        messages:{
            customer_phone: {
                required: "<small class='error'><i class='la la-warning'></i> Enter customer phone</small>"
            },
            customer_name: {
                required: "<small class='error'><i class='la la-warning'></i> Enter customer name</small>"
            },
            customer_email:{
                email: "<small class='error'><i class='la la-warning'></i> Customer email is invalid</small>"    
            }   
        }
    });
    $("#appointmentForm").submit(function(e){
        e.preventDefault();
        var isManual = !!e.originalEvent;

        if($("#appointmentForm").valid()) {
            var status  = 1;
            if($("#customer_phone").val().length != 11)  {
                status = 0;
                alert("Customer Phone required 11 digits only.");
            }
            if($("#cart_list table tbody tr").length <= 0)  {
                status = 0;
                alert("Your cart is empty please add at least one service.");
            }
            if($("#cart_list table tbody tr").length > 0) {
                $("#cart_list table tbody tr").each(function() {
                    if($(this).find("td:eq(1) select:eq(0)").val() === "") {
                        status = 2;
                        $(this).find("td:eq(1) small.error").html("<i class='la la-warning'></i> Select staff");
                    } else {
                        $(this).find("td:eq(1) small.error").html("");
                    }
                }); 
            }
            if(status == 1) {
                var formData = new FormData(this);
                formData.append("customer_phone",$("#customer_phone").val());
                formData.append("customer_name",$("#customer_name").val());
                $.ajax({
                    url: base_url+"/add-appointment",
                    type: 'post',
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend:function(){
                        $("#appointmentForm button[type=submit]").attr("disabled",true);
                    },
                    complete:function(){
                        $("#appointmentForm button[type=submit]").attr("disabled",false);
                    },
                    success:function(response){
                        if(response.status == 1) {
                            if(isManual) {
                                location.reload();
                            } else {
                                checkout_appointment($("#appointmentID").val());
                            }
                        } else 
                            alert(response.message);
                    }
                });       
            } else if(status == 2) {
                alert("Please select staff");
            }
        }
    });
    $("#walkinForm").validate({
        rules:{
            walkin_time:{
                required: true
            },
            walkin_phone:{
                required: true
            },
            walkin_name:{
                required: true
            }
        },
        messages:{
            walkin_time: {
                required: "<small class='error'><i class='la la-warning'></i> Select walkin time</small>"
            },
            walkin_phone: {
                required: "<small class='error'><i class='la la-warning'></i> Enter customer phone</small>"
            },
            walkin_name: {
                required: "<small class='error'><i class='la la-warning'></i> Enter customer name</small>"
            }   
        }
    });
    $("#walkinForm").submit(function(e){
        e.preventDefault();

        if($("#walkinForm").valid()) {
            var status  = 1;
            var errormsg= "";
            if($("#walkin_cart_list table tbody tr").length <= 0) {
                status = 0;
                errormsg = "Your cart is empty please add at least one service.";
            }
            if($("#walkin_cart_list table tbody tr").length > 0) {
                $("#walkin_cart_list table tbody tr").each(function(){
                    if($(this).find("td:eq(1) select").val() == "") {
                        status = 2;
                        $(this).find("td:eq(1) small").html("<i class='la la-warning'></i> Select staff");
                    } else {
                        $(this).find("td:eq(1) small").html("");
                    }
                }); 
            }
            if(parseFloat($("#remaining_amt").val()) != 0) {
                status = 0;
                errormsg = "Remaining amount must be 0";
            }
            if(status == 1) {
                var formData = new FormData(this);
                $.ajax({
                    url: base_url+"add-walkin",
                    type: 'post',
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success:function(response){
                        location.reload();
                    }
                });
            } else {
                alert(errormsg);
            }
        }
    });
      $("#removeAppointment").click(function(){
            if(confirm("Are you sure to remove this appointment?")) {
                $.ajax({
                    url: base_url+"remove-appointment",
                    type: 'post',
                    dataType: 'json',
                    data:{
                        id: $(this).attr("name")
                    },
                    success:function(response){
                        if(response.status == 1) {
                            window.location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
      });
      $("#checkoutBtn").click(function(){
            var id = $(this).attr("name");
            checkout_appointment(id);
      });

        $("#backViewAppointment").click(function(){
            view_appointment($("#checkoutItemTbl tbody tr:first").attr("data-no"));
        });

      $("#completeBtn").click(function(){
            var id = $(this).attr("name");
            var discount_id = $("#discount_type").val();
            var discountAmt = $("#discounted_amt").val();
            var payments = [];
            $("#paymentHistory input").each(function(){
                  if($(this).val() != "")
                        payments.push({"payment_id":$(this).attr("data-payment-id"),"amount":$(this).val()});
            });
            if(parseInt($("#remainAmt").text()) < 0 || parseInt($("#remainAmt").text()) > 0)
            {
                  alert("Remaining amount must be 0");
            } else {
                  $.ajax({
                        url: base_url+"complete-appointment",
                        type: 'post',
                        dataType: 'json',
                        data:{
                            appointmentId: id,
                            discount_id: discount_id,
                            discountAmt: discountAmt,
                            payments: payments,
                            salon_note: $("#salon_note").val(),
                            extra_discount: $("#extra_discount").val()
                        },
                        success:function(response){
                              if(response.status == 1) {
                                    window.location.reload();
                              }
                        }
                  });
            }
      });
      $("#noShowAppointment").click(function(){
            $.ajax({
                  url: base_url+"hide-appointment",
                  type: 'post',
                  dataType: 'json',
                  data:{
                        appointmentId: $(this).attr("name")
                  },
                  success:function(response){
                        if(response.status == 1) {
                              window.location.reload();
                        }
                  }
            });
      });
    $("#editAppointment").click(function(){
        var aid = $(this).attr("name");
        $("#customer_history").show();
        $.ajax({
            url: base_url+"edit-appointment",
            type: 'post',
            dataType: 'json',
            data:{
                appointmentId: aid
            },
            success:function(response){
                if(response.status == 1) {
                    $("#uniq_id").val(response.moredatainfo.uniq_id);
                    $("#appointmentID").val(aid);
                    $("#appointment_date").val(response.moredatainfo.bookingDate);
                    if(response.appointments.length > 0) {
                        set_time(response.appointments[0].stime,1);
                    }
                    $("#customer_phone").val(response.moredatainfo.customer_phone);
                    $("#customer_name").val(response.moredatainfo.customer_name);
                    $("#customer_email").val(response.moredatainfo.customer_email);
                    $("#bookedFrom").val(response.moredatainfo.bookedFrom);
                    $("#customer_note").val(response.moredatainfo.note);
                    // $("#customer_phone,#customer_name,#customer_email,#customer_note").attr("readonly",true);
                    $(".nav-scroller .nav-scroller-item:first").trigger("click");
                    $("#cart_list table tbody").html(response.html);
                    get_cart_total();
                    $("#cart_list").show();
                    $("#appointmentForm button[type=submit]").text("Save");
                    $("#save_checkout_btn").show();
                    $("#viewAppointmentModal").modal('hide');
                    $("#appointmentModal #appointmentModalLabel").text("Edit Appointment");
                    $("#total_app").val(response.total_appointments);
                    $("#no_show_app").val(response.total_no_show);
                    $("#appointmentModal").modal({backdrop: 'static',keyboard: false});
                }
            }
        });
    });
        $("#appointment_date,#appointment_time,#customer_email,#customer_note").focus(function(){
            $("#customer_hints,#customer_name_hints").hide();
        });

      $("#customer_phone").hover(function(){
            $("#customer_hints").show();
      });  

    $("#closeViewAppointement").click(function(){
        $.ajax({
            url: base_url+"close-appointment",
            type: "post",
            data:{
                appointmentId: $("#salon_note_view").attr("data-appointment"),
                salon_note: $("#salon_note_view").val()
            },
            dataType: "json",
            success:function(response){
                if(response.status == 200)
                    $("#viewAppointmentModal").modal("hide");
            }
        });
    });
    $("#save_checkout_btn").click(function(){
        $("#appointmentForm").trigger("submit");
    });
});
function get_sub_services(serviceId,serviceNm,flag = 0)
{
    $("#customer_hints,#customer_name_hints").hide();
    var appointment_date;
    if(flag == 0) {
        appointment_date = $("#appointment_date").val();
        var uniq_id = $("#uniq_id").val();
    } else {
        appointment_date = $("#walkin_date").val();
        var uniq_id = $("#walkin_uniq_id").val();
    }
    $.ajax({
        url: base_url+"/get-sub-services",
        type: 'post',
        dataType: 'json',
        data: {
            appointment_date: appointment_date,
            serviceId: serviceId,
            serviceNm: serviceNm,
            flag: flag,
            uniq_id: uniq_id,
            appointment_id: $("#appointmentID").val()
        },
        success:function(response){
            if(flag == 1) {
                $("#walkin_sub_service_list").html(response.content);
            } else {
                $("#sub_service_list").html(response.content);
            }
            hide_empty_tr(flag);
        }
    });
}
function hide_empty_tr(flag)
{
    if(flag == 1) {
        $("#walkin_sub_service_list table tbody tr").each(function(){
            var isBlank = 0;
            $(this).find("td").each(function(){
                if($.trim($(this).text()) != "") {
                    isBlank = 1;
                } 
            });
            if(isBlank == 0) {
                $(this).remove();
            }
        });
    } else {
        $("#sub_service_list table tbody tr").each(function(){
            var isBlank = 0;
            $(this).find("td").each(function(){
                if($.trim($(this).text()) != "") {
                    isBlank = 1;
                } 
            });
            if(isBlank == 0) {
                $(this).remove();
            }
        });
    }   
}
function add_to_cart_multiple(id,json,flag,name)
{
      /* var json_obj = $.parseJSON(json);
      if(json_obj.length > 0)
      {
            for(var i = 0; i < json_obj.length; i ++)
            {
                  add_to_cart(id,json_obj[i].id,name,json_obj[i].caption,json_obj[i].special_price,json_obj[i].duration,flag);
            }
      } */
}
function add_to_cart(serviceId,serviceSubId,serviceNm,caption,price,duration,flag,uniq_ele = "",actual_price = 0)
{

    if(flag == 0) {
        var element = "#cart-tbl tbody";
        var appointmentDate = $("#appointment_date").val();
        var tblelement = "cart_list";
        var uniq_id = $("#uniq_id").val();
    } else {
        var element = "#walkin_cart_list table tbody";
        var appointmentDate = $("#walkin_date").val();
        var tblelement = "walkin_cart_list";
        var uniq_id = $("#walkin_uniq_id").val();
    }
    $("td[data-uniq="+uniq_ele+"] span").removeClass("btn-success");
    $("td[data-uniq="+uniq_ele+"] span").addClass("btn-danger");

    let showbusystaff = 0;
    var stime = flag == 0 ? $("#appointment_time").val() : $("#walkin_time").val();
    $.ajax({
        url: base_url+"add-to-cart",
        type: 'post',
        dataType: 'json',
        data: {
            uniq_id: uniq_id,
            appointment_id: $("#appointmentID").val(),
            serviceId: serviceId,
            serviceSubId: serviceSubId,
            serviceNm: serviceNm,
            caption: caption,
            price: price,
            actual_price: actual_price,
            stime: stime,
            duration: duration,
            no:0,
            appointmentDate: appointmentDate,
            flag: flag,
            showbusystaff: showbusystaff,
            resourceID: $("#resourceID").val()
        },
        success:function(response){
            // $(element).html(response.content);
            get_cart_items(flag,tblelement);
        }
    });
}
function get_cart_items(flag,tblelement,is_removed_from_cart = 0)
{
    var uniq_id;
    var element;
    if(flag == 0) {
        uniq_id = $("#uniq_id").val();
        element = "#cart-tbl tbody";
    } else {
        uniq_id = $("#walkin_uniq_id").val();
        element = "#walkin_cart_list tbody";
    }
    var appointment_date;
    if(flag == 0) {
        appointment_date = $("#appointment_date").val();
    } else {
        appointment_date = $("#walkin_date").val();
    }
    $.ajax({
        url: base_url+"get-cart-items",
        type: 'post',
        data: {
            uniq_id: uniq_id,
            appointment_id: $("#appointmentID").val(),
            appointment_date: appointment_date
        },
        dataType: "json",
        success:function(response){
            if(flag == 0) {
                $("#walkin_cart_list").hide();
                $("#cart_list").show();
            } else {
                $("#cart_list").hide();
                $("#walkin_cart_list").show();
            }
            $(element).html(response.content);
            show_error();
            get_cart_total(tblelement);
            if(parseInt(flag) == 1) {
                calculate_walkin_item();
            }
            if(is_removed_from_cart == 1) {
                check_cartin_items();
            }
        }
    });
}
function check_cartin_items()
{
    get_sub_services($("#sub_service_list h5:eq(0) b").attr("id"),$("#sub_service_list h5:eq(0) b").text());
}
function remove_from_cart(uniq_id,entry_id,flag)
{
    var element;
    if(flag == 0) {
        element = "cart-tbl";
        var tblelement = "cart_list";
    } else {
        element = "walkin_cart_list";
        var tblelement = "walkin_cart_list";
    }
    $.ajax({
        url: base_url+"remove-from-cart",
        type: "post",
        data:{
            action: "remove_cart",
            uniq_id: uniq_id,
            entry_id: entry_id,
            date: $("#appointment_date").val(),
            time: $("#appointment_time").val() 
        },
        // dataType: "json",
        success:function(response){
            get_cart_items(flag,tblelement,1);
        }
    });
}
function change_appointment_date(status,val)
{
    var element = "cart-tbl";
    var tblelement = "cart_list";
    $.ajax({
        url: base_url+"remove-from-cart",
        type: "post",
        data:{
            action: "change_date_time",
            uniq_id: $("#uniq_id").val(),
            date: $("#appointment_date").val(),
            time: $("#appointment_time").val() 
        },
        // dataType: "json",
        success:function(response){
            $("#sub_service_list").html("");
            $("#cart_list").hide();
            get_cart_items(0,tblelement);
            
            // $("#"+element+" tbody").html(response.content);
        }
    });
}
function show_error()
{
    if($("#appointmentModal #appointmentModalLabel").text() != "Edit Appointment") {
        $("#cart-tbl tbody tr").each(function(){
            let staff_name = $.trim($(this).find("td:eq(1) select option:selected").text());
            let staff_status = parseInt($(this).find("td:eq(1) select option:selected").attr("data-status"));
            console.log(staff_status);
            switch(staff_status) {
                case 2:
                    $(this).find("td:eq(1) small").html("Time of "+staff_name+" is over.");
                    break; 

                case 3:
                    $(this).find("td:eq(1) small").html(staff_name+" is not free.");
                    break; 

                case 4:
                    $(this).find("td:eq(1) small").html(staff_name+" does not give this service.");
                    break; 

                default:
                    $(this).find("td:eq(1) small").html("");
                    break; 
            }
        });
    }
}
function edit_cart_item(element)
{
    var flag = element == "cart_list" ? 0 : 1;
    var date = "";
    var time = "";
    var duration = "";
    $("#"+element+" table tbody tr").each(function(i){
        if(i == 0) {
            date = $("#appointment_date").val();
            time = $("#appointment_time").val();
            duration = parseInt($(this).find("td:eq(2) small").text());
            // next_time = $(this).find("td:eq(2) input[name^=service_etime]").val();

            next_time = $(this).find("td:eq(2) input[name^=service_etime]").val();
            let format_date = convertDate(date,"YYYY-MM-DD");
            let format_time = convertDate(format_date+" "+next_time,"hh:mm A");
            let duration = parseInt($(this).find("td:eq(2) small").text());
            let next_etime = add_minutes(next_time,duration);

            $(this).find("td:eq(2) input[name^=service_stime]").val(next_time);
            $(this).find("td:eq(2) input[name^=service_etime]").val(next_etime+":00");
            $(this).find("td:eq(2) span").text(format_time);
            $(this).find("td:eq(2) span").attr("class",next_time);
            $(this).find("td:eq(2) span").attr("name",next_time);
        } else {
            next_time = $(this).prev().find("td:eq(2) input[name^=service_etime]").val();
            let format_date = convertDate(date,"YYYY-MM-DD");
            let format_time = convertDate(format_date+" "+next_time,"hh:mm A");
            let duration = parseInt($(this).find("td:eq(2) small").text());
            let next_etime = add_minutes(next_time,duration);

            $(this).find("td:eq(2) input[name^=service_stime]").val(next_time);
            $(this).find("td:eq(2) input[name^=service_etime]").val(next_etime+":00");
            $(this).find("td:eq(2) span").text(format_time);
            $(this).find("td:eq(2) span").attr("class",next_time);
            $(this).find("td:eq(2) span").attr("name",next_time);
        }
    });
}
function get_cart_total(element = "cart_list")
{
    var total = 0;
    if($("#"+element+" table tbody tr").length > 0)
    {
        var total = 0;
        $("#"+element+" table tbody tr").each(function(){
              total += parseFloat($(this).find("td:eq(3) span").text());
        });
    }
    $("#"+element+" h5 span").text(total);
}
function add_minutes(time,minutes)
{   
    let added_minute = moment.utc(time,'hh:mm').add(parseInt(minutes),'minutes').format('HH:mm');
    return added_minute;
}
function check_digit(e)
{
      var total = parseFloat($("#totAmt").text());
      var payment = 0;
      $("input[name^=payment_type_amt]").each(function(){
            if($.trim($(this).val()) != "")
            {
                  payment = payment + parseFloat($(this).val());
            }
      });
      $("#remainAmt").text(total-payment);
}
function check_walkin_digit(e)
{
      var total = parseFloat($("#walkin_cart_list table tfoot tr:last td:eq(1) span").text());
      var payment = 0;
      $("input[name^=walkin_payment_type_amt]").each(function(){
            if($.trim($(this).val()) != "")
            {
                  payment = payment + parseFloat($(this).val());
            }
      });
      $("#remaining_amt").val(total-payment);
}
function get_customer_info(phone,flag = 0,text = "phone")
{
    // var element = flag == 0 ? "customer_hints" : "walkin_customer_hints";
    if(flag == 0 && text == "phone") {
        element = "customer_hints";
    } else if(flag == 0 && text == "name") {
        element = "customer_name_hints";
    } else if(flag == 1 && text == "phone") {
        element = "walkin_customer_hints";    
    } else {
        element = "walkin_customer_name_hints";
    }
            
    if(phone == "") {
        $("#"+element).html("");
        $("#"+element).css({"border": "none","padding":"0px"});
      } else {
        $.ajax({
            url: base_url+"/get-customer-info",
            type: 'post',
            dataType: 'json',
            data: {phone:phone,flag:flag,text:text},
            success:function(response){
                if(response.content !== "") {
                    $("#"+element).show();
                    $("#"+element).html(response.content);
                    $("#"+element).css({"border": "1px solid #efefef","padding":"5px"});
                    $("#"+element).addClass("show");
                } else {
                    $("#"+element).show();
                    $("#"+element).removeClass("show");
                }
            }
        });     
    }
}
function set_customer_info(phone,name,email,flag,note)
{
    console.log("Heloooooooooooooooo");     
    if(flag == 0) {
        $("#customer_phone").val(phone);
        $("#customer_name").val(name);
        $("#customer_email").val(email);
        $("#customer_note").val(note);
        $("#customer_hints").html("");
        $("#customer_hints").css({"border": "none","padding":"0px"});
        $("#customer_name_hints").html("");
        $("#customer_name_hints").css({"border": "none","padding":"0px"});
    } else {
        $("#walkin_phone").val(phone);
        $("#walkin_name").val(name);
        $("#walkin_email").val(email);
        $("#customer_note").val(note);
        $("#walkin_customer_hints").html("");
        $("#walkin_customer_hints").css({"border": "none","padding":"0px"});
        $("#walkin_customer_name_hints").html("");
        $("#walkin_customer_name_hints").css({"border": "none","padding":"0px"});
    }
    $("#customer_history").show();
    get_customer_appointments(flag);
}
function get_customer_appointments(flag)
{
    if(flag == 0) {
        if($("#customer_phone").val() != "") {
            $.ajax({
                url: base_url+"get-customer-appointments",
                type: "post",
                data:{
                    phone: $("#customer_phone").val()
                },
                dataType: "json",
                success:function(response){
                    $("#total_app").val(response.total);
                    $("#no_show_app").val(response.total_no_show);
                }
            });
        }
    } else {
        if($("#walkin_phone").val() != "") {
            $.ajax({
                url: base_url+"get-customer-appointments",
                type: "post",
                data:{
                    phone: $("#walkin_phone").val()
                },
                dataType: "json",
                success:function(response){
                    $("#walkin_total_app").val(response.total);
                    $("#walkin_no_show_app").val(response.total_no_show);
                }
            });
        }
    }
}
function clear_appointment()
{
    $.ajax({
        url: base_url+"clear-carts",
        type: "post",
        data:{
            uniq_id: $("#uniq_id").val(),
            appointment_id: $("#appointmentID").val()
        }
    });
      $("#appointment_date,#customer_phone,#customer_name,#customer_email,#customer_note,#appointmentID,#resourceID").val('');
      $("#appointment_date,#customer_phone,#customer_name,#customer_email,#customer_note").attr("disabled",false);
      $("#appointment_time").val($("#appointment_time option:first").val());
      $("#sub_service_list").html('');
      $("#cart_list table tbody").html('');
      $("#cart_list,#customer_hints,#customer_name_hints").hide();
      $("#customer_history").hide();
      $("#appointmentModalLabel").text("New Appointment");
      $("#appointmentForm button[type=submit]").text("Add");
}
function clear_walkin()
{
      $("#walkin_date,#walkin_phone,#walkin_name,#walkin_email,#walkin_note").val('');
      $("#walkin_sub_service_list").html('');
      $("#walkin_cart_list table tbody").html('');
      $("#walkin_cart_list").hide();
}
function convertDate(date,format)
{
      var fdate = moment(date).format(format);
      return fdate;
}
function set_time(date,flag = 0)
{
      // alert(date);
      $("#appointment_time option").each(function(){  
            if(flag == 0)
            {
                  if($.trim($(this).text()) == $.trim(convertDate(date,"hh:mm A")))
                        $(this).prop("selected",true);
            } else {
                  if($.trim($(this).val()) == date)
                        $(this).prop("selected",true);
            }
      });
}
function open_walkin()
{
    $.ajax({
        url: base_url+"/open-walkin",
        type: "post",
        dataType: "json",
        success:function(response){
            $("#walkin_uniq_id").val(response.uniq_id);
            $("#walkin_date").val(response.date);
            $("#walkinModal").modal({backdrop: 'static',keyboard: false});            
            $("#walkin_time").val(response.format_time);
        }
    }); 
}
function get_discount_type(discount,flag = 0)
{
    var dis_amt = flag == 0 ? "discounted_amt" : "walkin_discounted_amt";
    var dis_txt = flag == 0 ? "disAmt" : "walkin_cart_list table tfoot tr:eq(1) td:eq(1) span";
    var sub_amt = flag == 0 ? "subAmt" : "walkin_cart_list table tfoot tr:first td:eq(1) span";
    var tot_amt = flag == 0 ? "totAmt" : "walkin_cart_list table tfoot tr:last td:eq(1) span";
    var remain  = flag == 0 ? "remainAmt" : "walkin_cart_list table tfoot tr:last td:eq(1) span";

    if(parseInt(flag) == 0) {
        $("#paymentHistory input[type=text]").each(function(){
            $(this).val("");
        });
    }
    if(discount == "") {
        $("#"+dis_amt).val('');
        $("#"+dis_txt).text("0");
        $("#"+tot_amt).text($("#"+sub_amt).text());
        $("#"+remain).text($("#"+sub_amt).text());
        if(flag == 1) {
            calculate_walkin_item();
        }
    } else {
        var type = discount.split("_");
        if(parseInt(type[1]) == 1) {
            var disAmt = (parseFloat($("#"+sub_amt).text())*parseFloat(type[2]))/100;      
            $("#"+dis_amt).val(disAmt);
            $("#"+dis_txt).text(disAmt);
            $("#"+tot_amt).text(parseFloat($("#"+sub_amt).text())-disAmt);
            $("#"+remain).text(parseFloat($("#"+sub_amt).text())-disAmt);
            if(flag == 1) {
                calculate_walkin_item();
            }
        } else {
            var disAmt = (parseFloat($("#"+sub_amt).text())-parseFloat(type[2]));
            $("#"+dis_amt).val(type[2]);
            $("#"+dis_txt).text(type[2]);
            $("#"+tot_amt).text(parseFloat($("#"+sub_amt).text())-type[2]);
            $("#"+remain).text(parseFloat($("#"+sub_amt).text())-type[2]);
            if(flag == 1) {
                calculate_walkin_item();
            }
        }
    }
}
function calculate_walkin_item()
{
    var total = total_discount = final_amount = 0;
    $("#walkin_cart_list tbody tr").each(function(){
        if($(this).find("td:eq(3) input").val() != "") {
            total = total + parseFloat($(this).find("td:eq(3) input").val());
        }
    });
    if($.trim($("#walkin_discounted_amt").val()) != "") {
        total_discount = total_discount + parseFloat($("#walkin_discounted_amt").val());
    }
    if($.trim($("#walkin_extra_discount").val()) != "") {
        total_discount = total_discount + parseFloat($("#walkin_extra_discount").val());
    }
    final_amount = total - total_discount;
    $("#walkin_cart_list tfoot tr:eq(0) td:eq(1)").html(company_currency+" <span>"+total+"</span>");
    $("#walkin_cart_list tfoot tr:eq(1) td:eq(1)").html(company_currency+" <span>"+total_discount+"</span>");
    $("#walkin_cart_list tfoot tr:eq(2) td:eq(1)").html(company_currency+" <span>"+final_amount+"</span>");
    $("#remaining_amt").val(final_amount);
    $("#walkin_payment_types table tbody tr").each(function(){
        $(this).find("td:eq(1) input:eq(0)").val("");
    });

    // var total = finalAmt = extra_discount = 0;
    // $("#walkin_cart_list tbody tr").each(function(){
    //     if($(this).find("td:eq(3) input").val() != "") {
    //         total = total + parseFloat($(this).find("td:eq(3) input").val());
    //     }
    // });
    // $("#walkin_cart_list tfoot tr:eq(0) td:eq(1)").html(company_currency+" <span>"+total+"</span>");
    // var discount = $("#walkin_cart_list tfoot tr:eq(1) td:eq(1) span").text();
    // if(discount != "") {
    //     discount = parseFloat(discount);
    //     finalAmt = total - discount;
    // }
    // if($.trim($("#walkin_extra_discount").val()) != "") {
    //     finalAmt = finalAmt - parseFloat($("#walkin_extra_discount").val());
    // }
    // $("#walkin_cart_list tfoot tr:eq(2) td:eq(1)").html(company_currency+" <span>"+finalAmt+"</span>");
    // $("#remaining_amt").val(finalAmt);
    // $("#walkin_payment_types table tbody tr").each(function(){
    //     $(this).find("td:eq(1) input:eq(0)").val("");
    // });
}
function convertTime(time)
{
	var dt = moment(time, ["h:mm A"]).format("HH:mm:ss");
	return dt;
}
function get_selected_staff_name(no)
{
    let status = parseInt($("#selected_staff_id_"+no+" option:selected").attr("data-status"));
    let staff_name = $("#selected_staff_id_"+no+" option:selected").text();
    if($("#appointmentModal #appointmentModalLabel").text() != "Edit Appointment") {
        switch(status) {
            case 2:
                $("#selected_staff_id_error_"+no).html("Time of "+staff_name+" is over.");
                break; 
    
            case 3:
                $("#selected_staff_id_error_"+no).html(staff_name+" is not free.");
                break; 
    
            case 4:
                $("#selected_staff_id_error_"+no).html(staff_name+" does not give this service.");
                break; 
    
            default:
                $("#selected_staff_id_error_"+no).html("");
                break; 
        }
    }
    $("#selected_staff_color_"+no).val($("#selected_staff_id_"+no+" option:selected").attr("data-color"));
    $("#selected_staff_name_"+no).val($("#selected_staff_id_"+no+" option:selected").text());
}
function get_customer_history(is_walkin = 0,customer_phone = "")
{
    var phone;
    var is_view_modal = 0;
    if(customer_phone != "") {
        phone = customer_phone;
        is_view_modal = 1;
    } else {
        if(is_walkin == 0) {
            phone = $.trim($("#customer_phone").val());
        } else {
            phone = $.trim($("#walkin_phone").val());
        }
    }
    $("#is_checkout_summary_open").val(is_view_modal);
    if(phone != ""){
        $.ajax({
            url: base_url+"get-customer-history",
            type: 'post',
            dataType: 'json',
            data: {
                phone:phone
            },
            success:function(response){
                if(response.status == 1)
                {
                    $("#is_walkin_customer").val(is_walkin);
                    if(is_walkin == 0) {
                        if(is_view_modal == 0) {
                            $("#appointmentModal").modal("hide");
                        } else {
                            $("#viewAppointmentModal").modal("hide");
                        }
                        $("#customer_history_info tbody").html(response.html);
                        $("#customerHistoryModal").modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                    } else {
                        if(is_view_modal == 0) {
                            $("#walkinModal").modal("hide");
                        } else {
                            $("#viewAppointmentModal").modal("hide");
                        }
                        $("#customer_history_info tbody").html(response.html);
                        $("#customerHistoryModal").modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                    }
                }
            }
        });        
    }
}
function close_customer_history_modal()
{
    $("#customerHistoryModal").modal("hide");
    if(parseInt($("#is_checkout_summary_open").val()) === 1) {
        $("#viewAppointmentModal").modal({
            backdrop: 'static',
            keyboard: false
        });
    } else {
        var element;
        if(parseInt($("#is_walkin_customer").val()) == 1) {
            element = "walkinModal";
        } else {
            element = "appointmentModal";
        }
        $("#"+element).modal({
            backdrop: 'static',
            keyboard: false
        });
    }
}
function somethingAsynchonous(a,b,c)
{
    setTimeout(function(){
        var calendar_view = $('#calendar').fullCalendar('getView');
        if(calendar_view.name == "agendaDay") 
        {
            $.ajax({
                url: base_url+"today_employees",
                type: 'post',
                dataType: 'json',
                data: {date:calendar_view.title},
                success:function(response){
                    successCallback(response);
                }
            });
        }        
    },500);
}
function successCallback(employees)
{
    return employees;
}
function fill_total(amt,pno)
{
    var total = parseFloat($("#totAmt").text());

    // $("#paymentHistory input[type=text]").each(function(){
    //     $(this).val("");
    // });
    // $("input[data-payment-id="+pno+"]").val(total);
    $("input[data-payment-id="+pno+"]").val($("#remainAmt").text());

    var payment = 0;
    $("input[name^=payment_type_amt]").each(function(){
        if($.trim($(this).val()) != "") {
            payment = payment + parseFloat($(this).val());
        }
    });
    $("#remainAmt").text(total-payment);
}
function fill_walkin_total(pno)
{
    var total = parseFloat($("#walkin_cart_list tfoot tr:eq(2) span").text());
    var remaining_amt = $("#remaining_amt").val();
    $("input[data-walkin-id="+pno+"]").val(remaining_amt);
    var payment = 0;
    $("input[name^=walkin_payment_type_amt]").each(function(){
        if($.trim($(this).val()) != "") {
            payment = payment + parseFloat($(this).val());
        }
    });
    $("#remaining_amt").val(total-payment);
}
function view_appointment(id)
{
    $.ajax({
        url: base_url+"view-appointment",
        type: 'post',
        dataType: 'json',
        data:{appointmentId: id},
        success:function(response){
            if(response.status == 1) {
                if ($("#checkoutModal").hasClass("show")) {
                    $("#checkoutModal").modal('hide');
                }
                if(response.appointment_status == 1 && response.isWalkin == "Y")
                {
                    $("#removeAppointment,#editAppointment,#checkoutBtn").show();
                    $("#removeAppointment,#editAppointment,#checkoutBtn").attr("name",response.appointmentId);
                } else {
                    $("#salon_note_view").show();
                    $("#salon_note_view").attr("data-appointment",response.appointmentId);
                }
                $("#view_appointment_info").html(response.html);
                $("#salon_note_view").val(response.salon_note);
                $("#viewAppointmentModal").modal({backdrop: 'static',keyboard: false});
            }
        }
    });
}
function checkout_appointment(id)
{
    $.ajax({
        url: base_url+"checkout-appointment",
        type: 'post',
        dataType: 'json',
        data:{appointmentId: id},
        success:function(response){
            if(response.status == 1) {
                if ($("#appointmentModal").hasClass("show")) {
                    $("#appointmentModal").modal('hide');
                }
                $("#checkout_appointment").html(response.html);
                $("#backViewAppointment,#noShowAppointment,#completeBtn").attr("name",id);
                $("#checkoutModal").modal({backdrop: 'static',keyboard: false});
                $("#viewAppointmentModal").modal('hide');
            }
        }
    });
}