$(document).ready(function() {
    var divs = $('.show-section>section');
    var now = 0; // currently shown div
    divs.hide().first().show(); // hide all divs except first
    $(".next").click(function() {
        var is_fill = 0;
        if(now == 0) {
            if($.trim($("#gender").val()) != "") {
                is_fill = 1; 
            }
        }
        if(now == 1) {
            if($.trim($("#age").val()) != "") {
                is_fill = 1; 
            }
        }
        if(now == 2) {
            if($.trim($("#occupation_id").val()) != "") {
                is_fill = 1; 
            }
        }
        if(now == 3) {
            if($.trim($("#education_id").val()) != "") {
                is_fill = 1; 
            }
        }
        if(now == 4) {
            if($.trim($("#income_id").val()) != "") {
                is_fill = 1; 
            }
        }
        if(is_fill == 1) {
            divs.eq(now).hide();
            now = (now + 1 < divs.length) ? now + 1 : 0;
            divs.eq(now).show(); // show next
        }
    });
    $(".prev").click(function() {
        divs.eq(now).hide();
        now = (now > 0) ? now - 1 : divs.length - 1;
        divs.eq(now).show(); // show previous
    });
});
$(document).ready(function(){
    $('.form-input input').on("change", function(){
        $(".form-input").removeClass("active-input");
        $(this).parent().addClass("active-input");
    })
});
