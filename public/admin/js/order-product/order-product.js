/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */
"use strict";

function updateTotalByPrice(element) {
    let totalAmount = 0;
    let dataID = $(element).attr('data');
    let qtyID = 'product_qty_' + dataID;
    let price = $(element).val();
    if (price == 0) {
        $(element).val(1);
        price = 1;
    }
    let qty = $('#' + qtyID).val();
    totalAmount = parseFloat(price) * parseFloat(qty);
    $('label#total_amount_' + dataID).text(totalAmount);
    $('input#total_amount_input_' + dataID).val(totalAmount);
    updateOrder();

}

function updateTotalByQty(element) {
    let totalAmount = 0;
    let dataID = $(element).attr('data');
    let priceID = 'product_price_' + dataID;
    let price = $('#' + priceID).val();
    let qty = $(element).val();
    if (qty == 0) {
        $(element).val(1);
        qty = 1;
    }
    let allData = {
        price: price,
        qty: qty,
        dataID: dataID

    };
    checkAvailableQty(allData);
    // updateOrder();
}

function deleteOrderProductFire(route = "", data = {}, tableId = "", confirmMsg = "", confirmMsgTitle = "") {

    data._token = $('meta[name="csrf-token"]').attr('content');
    var msgTitle = '';
    if (confirmMsgTitle != "") {
        msgTitle = confirmMsgTitle;
    } else {
        msgTitle = commonMessages.areYouSure;
    }
    Swal.fire({
        title: msgTitle,
        text: confirmMsg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: commonMessages.cancel,
        confirmButtonText: commonMessages.deleteYes
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "POST",
                url: route,
                data: data,
                beforeSend: function(res) {
                    laravel.startLoader();
                },
                success: function(res) {
                    if (res.status) {
                        // $('#'+tableId).DataTable().ajax.reload();
                        $(tableId).closest("tr").remove();
                        updateOrder('1');
                        Swal.fire(commonMessages.deleted, res.message, 'success');
                    } else {
                        Swal.fire(commonMessages.error, res.message, 'error');
                    }
                },
                error: function(res) {
                    laravel.error(res.message);
                },
                complete: function(res) {
                    laravel.stopLoader();
                }
            });
        }
    })
}

function addMoreOrderProduct() {
    $('#add-more-product').click(function(e) {
        e.preventDefault();
        var selectetValue = $("#product-variant-list").val();
        if (selectetValue.length && selectetValue != '0') {
            //STEP_1:Add row dynamic to data-table
            let selectedOption = $("#product-variant-list option:selected");
            let productId = selectedOption.attr("data-product_id");
            let productVariantId = selectetValue;
            let originalPriceValue = selectedOption.attr("data-original_price");
            let priceValue = selectedOption.attr("data-price");
            let quantityValue = selectedOption.attr("data-quantity");
            let totalValue = parseFloat(originalPriceValue) * parseFloat(quantityValue);
            let productName = '<label for="product-name-' + productVariantId + '">' + selectedOption.attr("data-product_name") + '</label>';
            let imageSrc = selectedOption.attr("data-image-src");
            //Set value in input
            let image = '<a target="_blank" href="' + imageSrc + '"><img class="img-thumbnail" src="' + imageSrc + '"  style="width:40px;height:40px;"></a>';
            let price = '<input type="text" value="' + originalPriceValue + '" onchange="updateTotalByPrice(this);" name="order_products[' + productVariantId + '][product_actual_price]" id="product_price_' + productVariantId + '" data="' + productVariantId + '" class="form-control product_price decimal_number_input"  value="' + priceValue + '" onchange="updateTotalByPrice(this);" class="form-control product_price decimal_number_input" maxlength="9">';
            let quantity = '<input type="text"  value="' + quantityValue + '" onchange="updateTotalByQty(this);" name="order_products[' + productVariantId + '][product_qty]" id="product_qty_' + productVariantId + '" data="' + productVariantId + '" class="form-control product_qty"  value="' + quantityValue + '" onchange="updateTotalByQty(this);" onkeypress="return (event.charCode !=8 && event.charCode == 0 || (event.charCode >= 48 && event.charCode <= 57))" maxlength="4">';
            let productIdInput = '<input id="product_id_' + productVariantId + '" value="' + productId + '" type="hidden" class="product_id" name="order_products[' + productVariantId + '][product_id]">'
            let productVariantIdInput = '<input id="product_variant_id_' + productVariantId + '" value="' + productVariantId + '" type="hidden" class="product_variant_id" name="order_products[' + productVariantId + '][product_variant_id]">';
            let totalAmountInput = '<input id="total_amount_input_' + productVariantId + '" value="' + parseFloat(totalValue) + '" type="hidden" class="total_amount_input" name="order_products[' + productVariantId + '][product_total_price]">';
            let totalAmountLabel = '<label id="total_amount_' + productVariantId + '">' + parseFloat(totalValue) + '</label>';
            let totalAmountDisplay = totalAmountLabel + '' + totalAmountInput + '' + productIdInput + '' + productVariantIdInput;

            let delUrl = "<a href='#' data-name='" + selectedOption.attr("data-product_name") + "' data-id='' class='delete_order_product ml-2' style='text-decoration:none;'><i class='fas fa-trash'></i></a>";
            var newRowData = [image, productName, price, quantity, totalAmountDisplay, delUrl];
            //Get a reference to the table's underlying HTML
            var table = orderProductListTable.table().node();
            //Create a new row element
            var newRow = table.insertRow();
            // Iterate over the new row data and create new cells
            for (var i = 0; i < newRowData.length; i++) {
                var cell = newRow.insertCell();
                cell.innerHTML = newRowData[i];
            }
            $(newRow).addClass('highlight');
            //END add row dynamic to data-table

            //STEP-2:Remove selected option
            selectedOption.remove();
            $("#product-variant-list").val('0').trigger("change");
            //Step-3: Update order price:
            //STEP-4: Remove empty row from table:
            if($('td.dataTables_empty').length){
                $('td.dataTables_empty').remove();
            }
           
            updateOrder();
        }else{
            swalFireOrderProduct(orderProduct.selectMedicineMsgTitle,orderProduct.selectMedicineMsg,orderProduct.contentClass);
        }
    });
}

function checkAvailableQty(allData) {
    $.ajax({
        method: 'POST',
        url: orderProductRoutes.checkAvailableQty,
        data: {
            _token: csrf_token,
            quantity: allData.qty,
            productVariantID: allData.dataID
        },
        beforeSend: function(res) {
            $('#btn-price-calculation').prop('disabled', true);
            $("#order-price-loader").show();
            $("#calculate-loader").show();
            $("#order-price-calculation").hide();
            $('#submit-order-product-form').hide();
        },
        success: function(res) {
            if (res.status) {
                if (res.isOutOfStock) {
                    $('input#product_qty_' + allData.dataID).val(1);
                    allData.qty = 1;

                    let confirmMsgTitle = $('label[for="product-name-' + allData.dataID + '"]').text();
                    let confirmMsg = res.message;
                    swalFireOrderProduct(confirmMsgTitle, confirmMsg)
                }
                setTimeout(function() {
                    let totalAmount = parseFloat(allData.price) * parseFloat(allData.qty);
                    $('label#total_amount_' + allData.dataID).text(totalAmount);
                    $('input#total_amount_input_' + allData.dataID).val(totalAmount);
                    updateOrder();
                    $('#btn-price-calculation').prop('disabled', false);
                    $("#order-price-loader").hide();
                    $("#calculate-loader").hide();
                    $("#order-price-calculation").show();
                    $('#submit-order-product-form').show();
                }, 10);

            } else {
                laravel.error(res.message);
            }
        },
        error: function(res) {},
        complete: function(res) {}
    });
}

function swalFireOrderProduct(confirmMsgTitle, confirmMsg,contentClass='') {
    Swal.fire({
        title: confirmMsgTitle,
        text: confirmMsg,
        showConfirmButton: false,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#002970',
        cancelButtonText: commonMessages.close,
        confirmButtonText: commonMessages.cancel,
        customClass: {
        content: contentClass,
        }
    });
}

function updateOrder(isRemove = '') {
    if (!$('#submit-order-product-form').hasClass('OpenAlert')) {
        $('#submit-order-product-form').addClass('OpenAlert');
        $('#submit-order-product-form').attr('type', 'button');
    }
    if (isRemove == '1') {
        loadOrderProductVariantList();
    }
}