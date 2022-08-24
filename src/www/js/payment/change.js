function changePayment(paymentId, paymentItemId) {
    $.ajax({
        url: payment_confirmUrl,
        data: {
            order_id: orderId,
            payment_id: paymentId,
            payment_item_id: paymentItemId
        },
        type: "POST",
        success: function (json) {
            if (json.success) {
                window.location.href = json.redirectUrl;
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}

