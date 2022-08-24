$(function () {

    $("#user-register-form").validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: true
            },
            password2: {
                required: true,
                equalTo: "#password"
            },
        },
        messages: {
            first_name: {
                required: "Please enter first name."
            },
            last_name: {
                required: "Please enter last name."
            },
            email: {
                required: "Please enter email.",
                email: "The email address you entered is incorrect."
            },
            password: {
                required: "Please enter password."
            },
            password2: {
                required: "Please enter confirm password.",
                equalTo: "Passwords do not match. Please try again."
            },
        },

        submitHandler: function (form) {
            $.ajax({
                url: userRegister_registerSaveUrl,
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

