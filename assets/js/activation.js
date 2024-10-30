"use strict";

jQuery(document).ready(function ($) {
    $(document).on('click', '#wccbel-activation-activate', function () {
        $('#wccbel-activation-type').val('activate');

        if ($('#wccbel-activation-email').val() != '') {
            if ($('#wccbel-activation-industry').val() != '') {
                setTimeout(function () {
                    $('#wccbel-activation-form').first().submit();
                }, 200)
            } else {
                swal({
                    title: "Industry is required !",
                    type: "warning"
                });
            }
        } else {
            swal({
                title: "Email is required !",
                type: "warning"
            });
        }
    });

    $(document).on('click', '#wccbel-activation-skip', function () {
        $('#wccbel-activation-type').val('skip');

        setTimeout(function () {
            $('#wccbel-activation-form').first().submit();
        }, 200)
    });
})