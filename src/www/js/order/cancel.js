$(function () {

    $("#order-cancel-form").validate({
        //debug:true,
        rules: {
            reason: {
                required: true,
            },
        },
        messages: {
            reason: {
                required: "Please enter cancel reason!",
            },
        },

        submitHandler: function (form) {
            $.ajax({
                url: beUrl  + "/?route=ShopFai.Order.cancelSave",
                data: $("#order-cancel-form").serialize(),
                method: "POST",
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
    });

});
