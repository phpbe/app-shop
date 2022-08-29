$(function () {
    updateState(stateId);

    $("#user-address-edit-shipping-address-form").validate({
        //debug:true,
        rules: {
            email: {
                required: true,
                email: true
            },
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            address: {
                required: true
            },
            city: {
                required: true
            },
            zip_code: {
                required: true
            },
            mobile: {
                required: true
            },

        },
        messages: {
            email: {
                required: "Please enter your email.",
                email: "The email address you entered is incorrect."
            },
            first_name: {
                required: "Please enter your first name."
            },
            last_name: {
                required: "Please enter your last name."
            },
            address: {
                required: "Please enter your address."
            },
            city: {
                required: "Please enter your city."
            },
            zip_code: {
                required: "Please enter your zip code."
            },
            mobile: {
                required: "Please enter your mobile phone number."
            },
        },

        submitHandler: function (form) {
            $.ajax({
                url: userAddress_editShippingAddressSaveUrl,
                data : $(form).serialize(),
                method: "POST",
                success: function (json) {
                    alert(json.message);
                    if (json.success) {
                        window.location.href = userAddress_addressesUrl;
                    }
                },
                error: function () {
                    alert("System Error!");
                }
            });
        }
    });

});


function updateState(stateId = "") {

    let $countryId = $("#country-id");
    let countryId = $countryId.val();
    if (!countryId) {
        return;
    }

    let eState = document.getElementById("state-id");
    eState.options.length = 0;

    $.ajax({
        url: userAddress_shippingGetStateKeyValuesUrl,
        data: {
            country_id: countryId
        },
        type: "POST",
        success: function (json) {
            if (json.success) {
                let $stateId = $("#state-id");
                if (json.stateKeyValues.length === 0) {
                    $stateId.closest(".shipping-address-state").hide();
                } else {
                    $stateId.closest(".shipping-address-state").show();

                    for (var x in json.stateKeyValues) {
                        eState.add(new Option(json.stateKeyValues[x], x));
                    }

                    if (stateId) {
                        $stateId.val(stateId);
                    }
                }
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}
