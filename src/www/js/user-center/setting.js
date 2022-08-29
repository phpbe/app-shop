
$(function () {

    $("#user-center-setting-Update-profile-form").validate({
        //debug:true,
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            }
        },
        messages: {
            first_name: {
                required: "Please enter your first name."
            },
            last_name: {
                required: "Please enter your last name."
            }
        },

        submitHandler: function (form) {
            $.ajax({
                url: userCenter_updateProfileUrl,
                data : $(form).serialize(),
                method: "POST",
                success: function (json) {
                    alert(json.message);
                },
                error: function () {
                    alert("System Error!");
                }
            });
        }
    });

    $("#user-center-setting-change-email-form").validate({
        //debug:true,
        rules: {
            password: {
                required: true
            },
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            password: {
                required: "Please enter your existing password."
            },
            email: {
                required: "Please enter your email.",
                email: "The email address you entered is incorrect."
            }
        },

        submitHandler: function (form) {
            $.ajax({
                url: userCenter_changeEmailUrl,
                data : $(form).serialize(),
                method: "POST",
                success: function (json) {
                    alert(json.message);
                },
                error: function () {
                    alert("System Error!");
                }
            });
        }
    });


    $("#user-center-setting-change-password-form").validate({
        //debug:true,
        rules: {
            password: {
                required: true
            },
            new_password: {
                required: true
            },
            new_password2: {
                required: true,
                equalTo: "#new_password"
            }
        },
        messages: {
            password: {
                required: "Please enter your existing password."
            },
            new_password: {
                required: "Please enter your new password."
            },
            new_password2: {
                required: "Please re-enter your new password.",
                equalTo: "Passwords do not match. Please try again."
            }
        },

        submitHandler: function (form) {
            $.ajax({
                url: userCenter_changePasswordlUrl,
                data : $(form).serialize(),
                method: "POST",
                success: function (json) {
                    alert(json.message);
                },
                error: function () {
                    alert("System Error!");
                }
            });
        }
    });


});

