function setDefaultShippingAddress(addressId) {
    $.ajax({
        url: userAddress_setDefaultShippingAddressUrl,
        data: {id: addressId},
        method: "POST",
        success: function (json) {
            if (json.success) {
                window.reload();
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}


function deleteShippingAddress(addressId) {
    if (!confirm("Are you sure you want to delete this address?")) {
        return;
    }

    $.ajax({
        url: userAddress_deleteShippingAddressUrl,
        data: {id: addressId},
        method: "POST",
        success: function (json) {
            if (json.success) {
                window.reload();
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}


function deleteBillingAddress(addressId) {
    if (!confirm("Are you sure you want to delete this address?")) {
        return;
    }

    $.ajax({
        url: userAddress_deleteBillingAddressUrl,
        data: {id: addressId},
        method: "POST",
        success: function (json) {
            if (json.success) {
                window.reload();
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}