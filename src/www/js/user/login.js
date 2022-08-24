$(function () {

    $("#user-login-form").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true
            }
        },
        messages: {
            email: {
                required: "Please enter email.",
                email: "The email address you entered is incorrect."
            },
            password: {
                required: "Please enter password."
            }
        },

        submitHandler: function (form) {
            $.ajax({
                url: userLogin_loginCheckUrl,
                data : $(form).serialize(),
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

