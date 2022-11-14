$(function () {

    $("#order-contact-form").validate({
        //debug:true,
        rules: {
            content: {
                required: true,
            }
        },
        messages: {
            content: {
                required: "Please enter content!",
            }
        },

        submitHandler: function (form) {
            $.ajax({
                url: beUrl  + "/?route=Shop.Order.contactSave",
                method: "POST",
                cache: false,
                data: new FormData($('#order-contact-form')[0]),
                processData: false,
                contentType: false,
                success: function (json) {
                    if (json.success) {
                        window.location.reload();
                    } else {
                        alert(json.message);
                    }
                },
                error: function () {
                    alert("System Error!", {title: "Error"});
                }
            });
        }
    });
});


function loadImg(obj) {
    var file = obj.files[0],
        reader = new FileReader(),
        imgFile = '';
    if (file) {
        reader.onload = function (e) {
            imgFile = e.target.result;
            window.document.getElementById('upload_image_preview').innerHTML = '<img src="' + imgFile + '" alt="" /><span></span>';
        };
        reader.readAsDataURL(file);
    } else {
        window.document.getElementById('upload_image_preview').innerHTML = '<img src="" alt="" /><span></span>';
    }
}