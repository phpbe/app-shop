
function deleteFavorite(productId) {
    if (!confirm("Are you sure delete this record?")) {
        return;
    }

    $.ajax({
        url: userFavorite_deleteFavoriteUrl,
        data: {productId: productId},
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
