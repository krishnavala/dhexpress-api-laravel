// allow only integer value in input box
$(document).on("keypress keyup blur",".int_number_input",function(e){    
    $(this).val($(this).val().replace(/[^0-9]/g,''));
    if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        e.preventDefault();
    }
});

// allow only decimal value in input box
$(document).on("keypress keyup blur",".decimal_number_input",function(e){    
    $(this).val($(this).val().replace(/[^0-9/.]/g,''));
    if ((e.which != 46 || $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
        e.preventDefault();
    }
});
$('body').on('click', '.openOrderDetails', function(e) {
    let orderId= $(this).attr('data-id');
    loadProductDetailsView(orderId);
  
});
function loadProductDetailsView(orderID=1) {
    var orderID = orderID;
    
    $.ajax({
        method: "POST",
        url: orderDetailsURL,
        data: {'_token':csrf_token, 'order_id':orderID},
        beforeSend:function(res) {
            $("#order-view-loader").show();
        },
        success: function (res) {
            if(res.status) {
                $(".modal-content").html(res.html);
                var $modal = $("#myModal");
                $modal.modal('show');
            } else {
                laravel.error(res.message);
            }
            $("#order-view-loader").hide();
        },
        error: function(res){
            $("#order-view-loader").hide();
        },
        complete: function(res) {
            $("#order-view-loader").hide();
        }
    });
}