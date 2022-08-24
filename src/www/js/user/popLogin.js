
$(function () {

    $("#user-pop-login-form").validate({
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
                url: userPopLogin_loginCheckUrl,
                data : $(form).serialize(),
                method: "POST",
                success: function (json) {
                    if (json.success) {
                        window.parent.reload();
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

