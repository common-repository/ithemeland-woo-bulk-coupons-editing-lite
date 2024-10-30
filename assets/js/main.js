"use strict";

var wccbelWpEditorSettings = {
    mediaButtons: true,
    tinymce: {
        branding: false,
        theme: 'modern',
        skin: 'lightgray',
        language: 'en',
        formats: {
            alignleft: [
                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'left' } },
                { selector: 'img,table,dl.wp-caption', classes: 'alignleft' }
            ],
            aligncenter: [
                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'center' } },
                { selector: 'img,table,dl.wp-caption', classes: 'aligncenter' }
            ],
            alignright: [
                { selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: { textAlign: 'right' } },
                { selector: 'img,table,dl.wp-caption', classes: 'alignright' }
            ],
            strikethrough: { inline: 'del' }
        },
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        browser_spellcheck: true,
        fix_list_elements: true,
        entities: '38,amp,60,lt,62,gt',
        entity_encoding: 'raw',
        keep_styles: false,
        paste_webkit_styles: 'font-weight font-style color',
        preview_styles: 'font-family font-size font-weight font-style text-decoration text-transform',
        end_container_on_empty_block: true,
        wpeditimage_disable_captions: false,
        wpeditimage_html5_captions: true,
        plugins: 'charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wplink,wpdialogs,wptextpattern,wpview',
        menubar: false,
        wpautop: true,
        indent: false,
        resize: true,
        theme_advanced_resizing: true,
        theme_advanced_resize_horizontal: false,
        statusbar: true,
        toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_adv',
        toolbar2: 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
        toolbar3: '',
        toolbar4: '',
        tabfocus_elements: ':prev,:next',
    },
    quicktags: {
        buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,more,close"
    }
}

jQuery(document).ready(function ($) {
    $(document).on('click', '.wccbel-timepicker, .wccbel-datetimepicker, .wccbel-datepicker', function () {
        $(this).attr('data-val', $(this).val());
    });

    wccbelReInitDatePicker();
    wccbelReInitColorPicker();

    // Select2
    if ($.fn.select2) {
        let wccbelSelect2 = $(".wccbel-select2");
        if (wccbelSelect2.length) {
            wccbelSelect2.select2({
                placeholder: "Select ..."
            });
        }
    }

    $(document).on("click", ".wccbel-tabs-list li button.wccbel-tab-item", function (event) {
        if ($(this).attr('data-disabled') !== 'true') {
            event.preventDefault();

            if ($(this).closest('.wccbel-tabs-list').attr('data-type') == 'url') {
                window.location.hash = $(this).attr('data-content');
            }

            wccbelOpenTab($(this));
        }
    });

    // Modal
    $(document).on("click", '[data-toggle="modal"]', function () {
        wccbelOpenModal($(this).attr("data-target"));
    });

    $(document).on("click", '[data-toggle="modal-close"]', function () {
        wccbelCloseModal();
    });

    // Float side modal
    $(document).on("click", '[data-toggle="float-side-modal"]', function () {
        wccbelOpenFloatSideModal($(this).attr("data-target"));
    });

    $(document).on("click", '[data-toggle="float-side-modal-close"]', function () {
        if ($('.wccbel-float-side-modal:visible').length && $('.wccbel-float-side-modal:visible').hasClass('wccbel-float-side-modal-close-with-confirm')) {
            swal({
                title: 'Are you sure?',
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
                confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
                confirmButtonText: wccbelTranslate.iAmSure,
                closeOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $('.wccbel-float-side-modal:visible').removeClass('wccbel-float-side-modal-close-with-confirm');
                    wccbelCloseFloatSideModal();
                }
            });
        } else {
            wccbelCloseFloatSideModal();
        }
    });

    $(document).on("keyup", function (e) {
        if (e.keyCode === 27) {
            if (jQuery('.wccbel-modal:visible').length > 0) {
                wccbelCloseModal();
            } else {
                if ($('.wccbel-float-side-modal:visible').length && $('.wccbel-float-side-modal:visible').hasClass('wccbel-float-side-modal-close-with-confirm')) {
                    swal({
                        title: ($('.wccbel-float-side-modal:visible').attr('data-confirm-message') && $('.wccbel-float-side-modal:visible').attr('data-confirm-message') != '') ? $('.wccbel-float-side-modal:visible').attr('data-confirm-message') : 'Are you sure?',
                        type: "warning",
                        showCancelButton: true,
                        cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
                        confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
                        confirmButtonText: wccbelTranslate.iAmSure,
                        closeOnConfirm: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            $('.wccbel-float-side-modal:visible').removeClass('wccbel-float-side-modal-close-with-confirm');
                            wccbelCloseFloatSideModal();
                        }
                    });
                } else {
                    wccbelCloseFloatSideModal();
                }
            }

            $("[data-type=edit-mode]").each(function () {
                $(this).closest("span").html($(this).attr("data-val"));
            });

            if ($("#wccbel-filter-form-content").css("display") === "block") {
                $("#wccbel-bulk-edit-filter-form-close-button").trigger("click");
            }
        }
    });

    // Color Picker Style
    $(document).on("change", "input[type=color]", function () {
        this.parentNode.style.backgroundColor = this.value;
    });

    $(document).on('click', '#wccbel-full-screen', function () {
        if ($('#adminmenuback').css('display') === 'block') {
            openFullscreen();
        } else {
            exitFullscreen();
        }
    });

    if (document.addEventListener) {
        document.addEventListener('fullscreenchange', wccbelFullscreenHandler, false);
        document.addEventListener('mozfullscreenchange', wccbelFullscreenHandler, false);
        document.addEventListener('MSFullscreenChange', wccbelFullscreenHandler, false);
        document.addEventListener('webkitfullscreenchange', wccbelFullscreenHandler, false);
    }

    $(document).on("click", ".wccbel-top-nav-duplicate-button", function () {
        let itemIds = $("input.wccbel-check-item:visible:checkbox:checked").map(function () {
            if ($(this).attr('data-item-type') === 'variation') {
                swal({
                    title: "Duplicate for variations product is disabled!",
                    type: "warning"
                });
                return false;
            }
            return $(this).val();
        }).get();

        if (!itemIds.length) {
            swal({
                title: (WCCBEL_DATA.strings && WCCBEL_DATA.strings['please_select_one_item']) ? WCCBEL_DATA.strings['please_select_one_item'] : "Please select one item",
                type: "warning"
            });
            return false;
        } else {
            wccbelOpenModal('#wccbel-modal-item-duplicate');
        }
    });

    // Select Items (Checkbox) in table
    $(document).on("change", ".wccbel-check-item-main", function () {
        let checkbox_items = $(".wccbel-check-item");
        if ($(this).prop("checked") === true) {
            checkbox_items.prop("checked", true);
            $("#wccbel-items-list tr").addClass("wccbel-tr-selected");
            checkbox_items.each(function () {
                $("#wccbel-export-items-selected").append("<input type='hidden' name='item_ids[]' value='" + $(this).val() + "'>");
            });
            wccbelShowSelectionTools();
            $("#wccbel-export-only-selected-items").prop("disabled", false);
        } else {
            checkbox_items.prop("checked", false);
            $("#wccbel-items-list tr").removeClass("wccbel-tr-selected");
            $("#wccbel-export-items-selected").html("");
            wccbelHideSelectionTools();
            $("#wccbel-export-only-selected-items").prop("disabled", true);
            $("#wccbel-export-all-items-in-table").prop("checked", true);
        }
    });

    $(document).on("change", ".wccbel-check-item", function () {
        if ($(this).prop("checked") === true) {
            $("#wccbel-export-items-selected").append("<input type='hidden' name='item_ids[]' value='" + $(this).val() + "'>");
            if ($(".wccbel-check-item:checked").length === $(".wccbel-check-item").length) {
                $(".wccbel-check-item-main").prop("checked", true);
            }
            $(this).closest("tr").addClass("wccbel-tr-selected");
        } else {
            $("#wccbel-export-items-selected").find("input[value=" + $(this).val() + "]").remove();
            $(this).closest("tr").removeClass("wccbel-tr-selected");
            $(".wccbel-check-item-main").prop("checked", false);
        }

        // Disable and enable "Only Selected items" in "Import/Export"
        if ($(".wccbel-check-item:checkbox:checked").length > 0) {
            $("#wccbel-export-only-selected-items").prop("disabled", false);
            wccbelShowSelectionTools();
        } else {
            wccbelHideSelectionTools();
            $("#wccbel-export-only-selected-items").prop("disabled", true);
            $("#wccbel-export-all-items-in-table").prop("checked", true);
        }
    });

    $(document).on("click", "#wccbel-bulk-edit-unselect", function () {
        $("input.wccbel-check-item").prop("checked", false);
        $("input.wccbel-check-item-main").prop("checked", false);
        wccbelHideSelectionTools();
    });

    // Start "Column Profile"
    $(document).on("change", "#wccbel-column-profiles-choose", function () {
        let preset = $(this).val();
        $('.wccbel-column-profiles-fields input[type="checkbox"]').prop('checked', false);
        $('#wccbel-column-profile-select-all').prop('checked', false);
        $('.wccbel-column-profile-select-all span').text('Select All');
        $("#wccbel-column-profiles-apply").attr("data-preset-key",);
        if (defaultPresets && $.inArray(preset, defaultPresets) === -1) {
            $("#wccbel-column-profiles-update-changes").show();
        } else {
            $("#wccbel-column-profiles-update-changes").hide();
        }

        if (columnPresetsFields && columnPresetsFields[preset]) {
            columnPresetsFields[preset].forEach(function (val) {
                $('.wccbel-column-profiles-fields input[type="checkbox"][value="' + val + '"]').prop('checked', true);
            });
        }
    });

    $(document).on("keyup", "#wccbel-column-profile-search", function () {
        let wccbelSearchFieldValue = $(this).val().toLowerCase().trim();
        $(".wccbel-column-profile-fields ul li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(wccbelSearchFieldValue) > -1);
        });
    });

    $(document).on('change', '#wccbel-column-profile-select-all', function () {
        if ($(this).prop('checked') === true) {
            $(this).closest('label').find('span').text('Unselect');
            $('.wccbel-column-profile-fields input:checkbox:visible').prop('checked', true);
        } else {
            $(this).closest('label').find('span').text('Select All');
            $('.wccbel-column-profile-fields input:checkbox').prop('checked', false);
        }
        $(".wccbel-column-profile-save-dropdown").show();
    });
    // End "Column Profile"

    // Calculator for numeric TD
    $(document).on({
        mouseenter: function () {
            $(this)
                .children(".wccbel-calculator")
                .show();
        },
        mouseleave: function () {
            $(this)
                .children(".wccbel-calculator")
                .hide();
        }
    },
        "td[data-content-type=regular_price], td[data-content-type=sale_price], td[data-content-type=numeric]"
    );

    // delete items button
    $(document).on("click", ".wccbel-bulk-edit-delete-item", function () {
        $(this).find(".wccbel-bulk-edit-delete-item-buttons").slideToggle(200);
    });

    $(document).on("change", ".wccbel-column-profile-fields input:checkbox", function () {
        $(".wccbel-column-profile-save-dropdown").show();
    });

    $(document).on("click", ".wccbel-column-profile-save-dropdown", function () {
        $(this).find(".wccbel-column-profile-save-dropdown-buttons").slideToggle(200);
    });

    $('#wp-admin-bar-root-default').append('<li id="wp-admin-bar-wccbel-col-view"></li>');

    $(document).on({
        mouseenter: function () {
            $('#wp-admin-bar-wccbel-col-view').html('#' + $(this).attr('data-item-id') + ' | ' + $(this).attr('data-item-title') + ' [<span class="wccbel-col-title">' + $(this).attr('data-col-title') + '</span>] ');
        },
        mouseleave: function () {
            $('#wp-admin-bar-wccbel-col-view').html('');
        }
    },
        "#wccbel-items-list td"
    );

    $(document).on("click", ".wccbel-open-uploader", function (e) {
        let target = $(this).attr("data-target");
        let element = $(this).closest('div');
        let type = $(this).attr("data-type");
        let mediaUploader;
        let wccbelNewImageElementID = $(this).attr("data-id");
        let wccbelProductID = $(this).attr("data-item-id");
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        if (type === "single") {
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: "Choose Image",
                button: {
                    text: "Choose Image"
                },
                multiple: false
            });
        } else {
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: "Choose Images",
                button: {
                    text: "Choose Images"
                },
                multiple: true
            });
        }

        mediaUploader.on("select", function () {
            let attachment = mediaUploader.state().get("selection").toJSON();
            switch (target) {
                case "inline-file":
                    $("#url-" + wccbelNewImageElementID).val(attachment[0].url);
                    break;
                case "inline-file-custom-field":
                    $("#wccbel-file-url").val(attachment[0].url);
                    $('#wccbel-file-id').val(attachment[0].id)
                    break;
                case "inline-edit":
                    $("#" + wccbelNewImageElementID).val(attachment[0].url);
                    $("[data-image-preview-id=" + wccbelNewImageElementID + "]").html("<img src='" + attachment[0].url + "' alt='' />");
                    $("#wccbel-modal-image button[data-item-id=" + wccbelProductID + "][data-button-type=save]").attr("data-image-id", attachment[0].id).attr("data-image-url", attachment[0].url);
                    break;
                case "variations-inline-edit":
                    $("#wccbel-variation-thumbnail-modal .wccbel-inline-image-preview").html("<img src='" + attachment[0].url + "' alt='' />");
                    $('#wccbel-variation-thumbnail-modal .wccbel-variations-table-thumbnail-inline-edit-button[data-button-type="save"]').attr("data-image-id", attachment[0].id).attr("data-image-url", attachment[0].url);
                    break;
                case "inline-edit-gallery":
                    attachment.forEach(function (item) {
                        $("#wccbel-modal-gallery-items").append('<div class="wccbel-inline-edit-gallery-item"><img src="' + item.url + '" alt=""><input type="hidden" class="wccbel-inline-edit-gallery-image-ids" value="' + item.id + '"></div>');
                    });
                    break;
                case "bulk-edit-image":
                    element.find(".wccbel-bulk-edit-form-item-image").val(attachment[0].id);
                    element.find(".wccbel-bulk-edit-form-item-image-preview").html('<div><img src="' + attachment[0].url + '" width="43" height="43" alt=""><button type="button" class="wccbel-bulk-edit-form-remove-image"><i class="wccbel-icon-x"></i></button></div>');
                    break;
                case "variations-bulk-actions-image":
                    element.find(".wccbel-variations-bulk-actions-image").val(attachment[0].id);
                    element.find(".wccbel-variations-bulk-actions-image-preview").html('<div><img src="' + attachment[0].url + '" width="43" height="43" alt=""><button type="button" class="wccbel-variations-bulk-actions-remove-image"><i class="wccbel-icon-x"></i></button></div>');
                    break;
                case "variations-bulk-actions-file":
                    element.find(".wccbel-variation-bulk-actions-file-item-url-input").val(attachment[0].url);
                    break;
                case "bulk-edit-file":
                    element.find(".wccbel-bulk-edit-form-item-file").val(attachment[0].id);
                    break;
                case "bulk-edit-gallery":
                    attachment.forEach(function (item) {
                        $(".wccbel-bulk-edit-form-item-gallery").append('<input type="hidden" value="' + item.id + '" data-field="value">');
                        $(".wccbel-bulk-edit-form-item-gallery-preview").append('<div><img src="' + item.url + '" width="43" height="43" alt=""><button type="button" data-id="' + item.id + '" class="wccbel-bulk-edit-form-remove-gallery-item"><i class="wccbel-icon-x"></i></button></div>');
                    });
                    break;
            }
        });
        mediaUploader.open();
    });

    $(document).on("click", ".wccbel-inline-edit-gallery-image-item-delete", function () {
        $(this).closest("div").remove();
    });

    $(document).on("change", ".wccbel-column-manager-check-all-fields-btn input:checkbox", function () {
        if ($(this).prop("checked")) {
            $(this).closest("label").find("span").addClass("selected").text("Unselect");
            $(".wccbel-column-manager-available-fields[data-action=" + $(this).closest("label").attr("data-action") + "] li:visible").each(function () {
                $(this).find("input:checkbox").prop("checked", true);
            });
        } else {
            $(this).closest("label").find("span").removeClass("selected").text("Select All");
            $(".wccbel-column-manager-available-fields[data-action=" + $(this).closest("label").attr("data-action") + "] li:visible input:checked").prop("checked", false);
        }
    });

    $(document).on("click", ".wccbel-column-manager-add-field", function () {
        let fieldName = [];
        let fieldLabel = [];
        let action = $(this).attr("data-action");
        let checked = $(".wccbel-column-manager-available-fields[data-action=" + action + "] input[data-type=field]:checkbox:checked");
        if (checked.length > 0) {
            $('.wccbel-column-manager-empty-text').hide();
            if (action === 'new') {
                $('.wccbel-column-manager-added-fields-wrapper .wccbel-box-loading').show();
            } else {
                $('#wccbel-modal-column-manager-edit-preset .wccbel-box-loading').show();
            }
            checked.each(function (i) {
                fieldName[i] = $(this).attr("data-name");
                fieldLabel[i] = $(this).val();
            });
            wccbelColumnManagerAddField(fieldName, fieldLabel, action);
        }
    });

    $(".wccbel-column-manager-delete-preset").on("click", function () {
        var $this = $(this);
        $("#wccbel_column_manager_delete_preset_key").val($this.val());
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                $("#wccbel-column-manager-delete-preset-form").submit();
            }
        });
    });

    $(document).on("keyup", ".wccbel-column-manager-search-field", function () {
        let wccbelSearchFieldValue = $(this).val().toLowerCase().trim();
        $(".wccbel-column-manager-available-fields[data-action=" + $(this).attr("data-action") + "] ul li[data-added=false]").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(wccbelSearchFieldValue) > -1);
        });
    });

    $(document).on("click", ".wccbel-column-manager-remove-field", function () {
        $(".wccbel-column-manager-available-fields[data-action=" + $(this).attr("data-action") + "] li[data-name=" + $(this).attr("data-name") + "]").attr("data-added", "false").show();
        $(this).closest(".wccbel-column-manager-right-item").remove();
        if ($('.wccbel-column-manager-added-fields-wrapper .wccbel-column-manager-right-item').length < 1) {
            $('.wccbel-column-manager-empty-text').show();
        }
    });

    if ($.fn.sortable) {
        let wccbelColumnManagerFields = $(".wccbel-column-manager-added-fields .items");
        wccbelColumnManagerFields.sortable({
            handle: ".wccbel-column-manager-field-sortable-btn",
            cancel: ""
        });
        wccbelColumnManagerFields.disableSelection();

        let wccbelMetaFieldItems = $(".wccbel-meta-fields-right");
        wccbelMetaFieldItems.sortable({
            handle: ".wccbel-meta-field-item-sortable-btn",
            cancel: ""
        });
        wccbelMetaFieldItems.disableSelection();
    }

    $(document).on("click", "#wccbel-add-meta-field-manual", function () {
        $(".wccbel-meta-fields-empty-text").hide();
        let input = $("#wccbel-meta-fields-manual_key_name");
        wccbelAddMetaKeysManual(input.val());
        input.val("");
    });

    $(document).on("click", "#wccbel-add-acf-meta-field", function () {
        let input = $("#wccbel-add-meta-fields-acf");
        if (input.val()) {
            $(".wccbel-meta-fields-empty-text").hide();
            wccbelAddACFMetaField(input.val(), input.find('option:selected').text(), input.find('option:selected').attr('data-type'));
            input.val("").change();
        }
    });

    $(document).on("click", ".wccbel-meta-field-remove", function () {
        $(this).closest(".wccbel-meta-fields-right-item").remove();
        if ($(".wccbel-meta-fields-right-item").length < 1) {
            $(".wccbel-meta-fields-empty-text").show();
        }
    });

    $(document).on("click", ".wccbel-history-delete-item", function () {
        $("#wccbel-history-clicked-id").attr("name", "delete").val($(this).val());
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                $("#wccbel-history-items").submit();
            }
        });
    });

    $(document).on("click", "#wccbel-history-clear-all-btn", function () {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                $("#wccbel-history-clear-all").submit();
            }
        });
    });

    $(document).on("click", ".wccbel-history-revert-item", function () {
        $("#wccbel-history-clicked-id").attr("name", "revert").val($(this).val());
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                $("#wccbel-history-items").submit();
            }
        });
    });

    $(document).on('click', '.wccbel-modal', function (e) {
        if ($(e.target).hasClass('wccbel-modal') || $(e.target).hasClass('wccbel-modal-container') || $(e.target).hasClass('wccbel-modal-box')) {
            wccbelCloseModal();
        }
    });

    $(document).on("change", 'select[data-field="operator"]', function () {
        if ($(this).val() === "number_formula") {
            $(this).closest("div").find("input[type=number]").attr("type", "text");
        }
    });

    $(document).on('change', '#wccbel-filter-form-content [data-field=value], #wccbel-filter-form-content [data-field=from], #wccbel-filter-form-content [data-field=to]', function () {
        wccbelCheckFilterFormChanges();
    });

    $(document).on('change', 'input[type=number][data-field=to]', function () {
        let from = $(this).closest('.wccbel-form-group').find('input[type=number][data-field=from]');
        if (parseFloat($(this).val()) < parseFloat(from.val())) {
            from.val('').addClass('wccbel-input-danger').focus();
        }
    });

    $(document).on('change', 'input[type=number][data-field=from]', function () {
        let to = $(this).closest('.wccbel-form-group').find('input[type=number][data-field=to]');
        if (parseFloat($(this).val()) > parseFloat(to.val())) {
            $(this).val('').addClass('wccbel-input-danger');
        } else {
            $(this).removeClass('wccbel-input-danger')
        }
    });

    $(document).on('change', '#wccbel-switcher', function () {
        wccbelLoadingStart();
        $('#wccbel-switcher-form').submit();
    });

    $(document).on('click', 'span[data-target="#wccbel-modal-image"]', function () {
        let tdElement = $(this).closest('td');
        let modal = $('#wccbel-modal-image');
        let col_title = tdElement.attr('data-col-title');
        let id = $(this).attr('data-id');
        let image_id = $(this).attr('data-image-id');
        let item_id = tdElement.attr('data-item-id');
        let full_size_url = $(this).attr('data-full-image-src');
        let field = tdElement.attr('data-field');
        let field_type = tdElement.attr('data-field-type');

        $('#wccbel-modal-image-item-title').text(col_title);
        modal.find('.wccbel-open-uploader').attr('data-id', id).attr('data-item-id', item_id);
        modal.find('.wccbel-inline-image-preview').attr('data-image-preview-id', id).html('<img src="' + full_size_url + '" />');
        modal.find('.wccbel-image-preview-hidden-input').attr('id', id);
        modal.find('button[data-button-type="save"]').attr('data-item-id', item_id).attr('data-field', field).attr('data-image-url', full_size_url).attr('data-image-id', image_id).attr('data-field-type', field_type).attr('data-name', tdElement.attr('data-name')).attr('data-update-type', tdElement.attr('data-update-type'));
        modal.find('button[data-button-type="remove"]').attr('data-item-id', item_id).attr('data-field', field).attr('data-field-type', field_type).attr('data-name', tdElement.attr('data-name')).attr('data-update-type', tdElement.attr('data-update-type'));
    });

    $(document).on('click', 'button[data-target="#wccbel-modal-file"]', function () {
        let modal = $('#wccbel-modal-file');
        modal.find('#wccbel-modal-select-file-item-title').text($(this).closest('td').attr('data-col-title'));
        modal.find('#wccbel-modal-file-apply').attr('data-item-id', $(this).attr('data-item-id')).attr('data-field', $(this).attr('data-field')).attr('data-field-type', $(this).attr('data-field-type'));
        modal.find('#wccbel-file-id').val($(this).attr('data-file-id'));
        modal.find('#wccbel-file-url').val($(this).attr('data-file-url'));
    });

    $(document).on('click', '#wccbel-modal-file-clear', function () {
        let modal = $('#wccbel-modal-file');
        modal.find('#wccbel-file-id').val(0).change();
        modal.find('#wccbel-file-url').val('').change();
    });

    $(document).on('click', '.wccbel-sub-tab-title', function () {
        $(this).closest('.wccbel-sub-tab-titles').find('.wccbel-sub-tab-title').removeClass('active');
        $(this).addClass('active');

        $(this).closest('div').find('.wccbel-sub-tab-content').hide();
        $(this).closest('div').find('.wccbel-sub-tab-content[data-content="' + $(this).attr('data-content') + '"]').show();
    });

    if ($('.wccbel-sub-tab-titles').length > 0) {
        $('.wccbel-sub-tab-titles').each(function () {
            $(this).find('.wccbel-sub-tab-title').first().trigger('click');
        });
    }

    $(document).on("mouseenter", ".wccbel-thumbnail", function () {
        let position = $(this).offset();
        let imageHeight = $(this).find('img').first().height();
        let top = ((position.top - imageHeight) > $('#wpadminbar').offset().top) ? position.top - imageHeight : position.top + 15;

        $('.wccbel-thumbnail-hover-box').css({
            top: top,
            left: position.left - 100,
            display: 'block',
            height: imageHeight
        }).html($(this).find('.wccbel-original-thumbnail').clone());
    });

    $(document).on("mouseleave", ".wccbel-thumbnail", function () {
        $('.wccbel-thumbnail-hover-box').hide();
    });

    setTimeout(function () {
        $('#wccbel-column-profiles-choose').trigger('change');
    }, 500);

    $(document).on('click', '.wccbel-filter-form-action', function () {
        wccbelFilterFormClose();
    });

    $(document).on('click', '#wccbel-license-renew-button', function () {
        $(this).closest('#wccbel-license').find('.wccbel-license-form').slideDown();
    });

    $(document).on('click', '#wccbel-license-form-cancel', function () {
        $(this).closest('#wccbel-license').find('.wccbel-license-form').slideUp();
    });

    $(document).on('click', '#wccbel-license-deactivate-button', function () {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                $('#wccbel-license-deactivation-form').submit();
            }
        });
    });

    wccbelSetTipsyTooltip();

    $(window).on('resize', function () {
        wccbelDataTableFixSize();
    });

    $(document).on('click', 'body', function (e) {
        if (!$(e.target).hasClass('wccbel-status-filter-button') && $(e.target).closest('.wccbel-status-filter-button').length == 0) {
            $('.wccbel-top-nav-status-filter').hide();
        }

        if (!$(e.target).hasClass('wccbel-quick-filter') && $(e.target).closest('.wccbel-quick-filter').length == 0) {
            $('.wccbel-top-nav-filters').hide();
        }

        if (!$(e.target).hasClass('wccbel-post-type-switcher') && $(e.target).closest('.wccbel-post-type-switcher').length == 0) {
            $('.wccbel-top-nav-filters-switcher').hide();
        }

        if (!$(e.target).hasClass('wccbel-float-side-modal') &&
            !$(e.target).closest('.wccbel-float-side-modal-box').length &&
            !$('.sweet-overlay:visible').length &&
            !$('.wccbel-modal:visible').length &&
            $(e.target).attr('data-toggle') != 'float-side-modal' &&
            !$(e.target).closest('.select2-container').length &&
            !$(e.target).is('i') &&
            !$(e.target).closest('.media-modal').length &&
            !$(e.target).closest('.sweet-alert').length &&
            !$(e.target).closest('[data-toggle="float-side-modal"]').length &&
            !$(e.target).closest('[data-toggle="float-side-modal-after-confirm"]').length) {
            if ($('.wccbel-float-side-modal:visible').length && $('.wccbel-float-side-modal:visible').hasClass('wccbel-float-side-modal-close-with-confirm')) {
                swal({
                    title: ($('.wccbel-float-side-modal:visible').attr('data-confirm-message') && $('.wccbel-float-side-modal:visible').attr('data-confirm-message') != '') ? $('.wccbel-float-side-modal:visible').attr('data-confirm-message') : 'Are you sure?',
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
                    confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
                    confirmButtonText: wccbelTranslate.iAmSure,
                    closeOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        $('.wccbel-float-side-modal:visible').removeClass('wccbel-float-side-modal-close-with-confirm');
                        wccbelCloseFloatSideModal();
                    }
                });
            } else {
                wccbelCloseFloatSideModal();
            }
        }
    });

    $(document).on('click', '.wccbel-status-filter-button', function () {
        $(this).closest('.wccbel-status-filter-container').find('.wccbel-top-nav-status-filter').toggle();
    });

    $(document).on('click', '.wccbel-quick-filter > button', function (e) {
        if (!$(e.target).closest('.wccbel-top-nav-filters').length) {
            $('.wccbel-top-nav-filters').slideToggle(150);
        }
    });
    $(document).on('click', '.wccbel-post-type-switcher > button', function (e) {
        if (!$(e.target).closest('.wccbel-top-nav-filters-switcher').length) {
            $('.wccbel-top-nav-filters-switcher').slideToggle(150);
        }
    });

    $(document).on('click', '.wccbel-bind-edit-switch', function () {
        if ($('#wccbel-bind-edit').prop('checked') === true) {
            $('#wccbel-bind-edit').prop('checked', false);
            $(this).removeClass('active');
        } else {
            $('#wccbel-bind-edit').prop('checked', true);
            $(this).addClass('active');
        }
    });

    if ($('#wccbel-bind-edit').prop('checked') === true) {
        $('.wccbel-bind-edit-switch').addClass('active');
    } else {
        $('.wccbel-bind-edit-switch').removeClass('active');
    }

    if ($('.wccbel-flush-message').length) {
        setTimeout(function () {
            $('.wccbel-flush-message').slideUp();
        }, 3000);
    }

    wccbelDataTableFixSize();

    // Inline edit
    $(document).on("click", "td[data-action=inline-editable]", function (e) {
        if ($(e.target).attr("data-type") !== "edit-mode" && $(e.target).find("[data-type=edit-mode]").length === 0) {
            // Close All Inline Edit
            $("[data-type=edit-mode]").each(function () {
                $(this).closest("span").html($(this).attr("data-val"));
            });
            // Open Clicked Inline Edit
            switch ($(this).attr("data-content-type")) {
                case "text":
                case "select":
                case "password":
                case "url":
                case "email":
                    $(this).children("span").html("<textarea data-item-id='" + $(this).attr("data-item-id") + "' data-field='" + $(this).attr("data-field") + "' data-field-type='" + $(this).attr("data-field-type") + "' data-type='edit-mode' data-val='" + $(this).text().trim() + "'>" + $(this).text().trim() + "</textarea>").children("textarea").focus().select();
                    break;
                case "numeric":
                case "regular_price":
                case "sale_price":
                    $(this).children("span").html("<input type='number' min='-1' data-item-id='" + $(this).attr("data-item-id") + "' data-field='" + $(this).attr("data-field") + "' data-field-type='" + $(this).attr("data-field-type") + "' data-type='edit-mode' data-val='" + $(this).text().trim() + "' value='" + $(this).text().trim() + "'>").children("input[type=number]").focus().select();
                    break;
            }
        }
    });

    // Discard Save
    $(document).on("click", function (e) {
        if ($(e.target).attr("data-action") !== "inline-editable" && $(e.target).attr("data-type") !== "edit-mode") {
            $("[data-type=edit-mode]").each(function () {
                $(this).closest("span").html($(this).attr("data-val"));
            });
        }
    });

    // Save Inline Edit By Enter Key
    $(document).on("keypress", '[data-type="edit-mode"]', function (event) {
        let wccbelKeyCode = event.keyCode ? event.keyCode : event.which;
        if (wccbelKeyCode === 13) {
            let couponData = [];
            let couponIds = [];
            let tdElement = $(this).closest('td');

            if ($('#wccbel-bind-edit').prop('checked') === true) {
                couponIds = wccbelGetCouponsChecked();
            }
            couponIds.push($(this).attr("data-item-id"));

            couponData.push({
                name: tdElement.attr('data-name'),
                sub_name: (tdElement.attr('data-sub-name')) ? tdElement.attr('data-sub-name') : '',
                type: tdElement.attr('data-update-type'),
                value: $(this).val(),
                operation: 'inline_edit'
            });

            $(this).closest("span").html($(this).val());
            wccbelCouponEdit(couponIds, couponData);
        }
    });

    // fetch coupon data by click to bulk edit button
    $(document).on("click", "#wccbel-bulk-edit-bulk-edit-btn", function () {
        if ($(this).attr("data-fetch-coupon") === "yes") {
            let couponID = $("input.wccbel-check-item:checkbox:checked");
            if (couponID.length === 1) {
                wccbelGetCouponData(couponID.val());
            } else {
                wccbelResetBulkEditForm();
            }
        }
    });

    $(document).on('click', '.wccbel-inline-edit-color-action', function () {
        $(this).closest('td').find('input.wccbel-inline-edit-action').trigger('change');
    });

    $(document).on("change", ".wccbel-inline-edit-action", function (e) {
        let $this = $(this);
        setTimeout(function () {
            if ($('div.xdsoft_datetimepicker:visible').length > 0) {
                e.preventDefault();
                return false;
            }

            if ($this.hasClass('wccbel-datepicker') || $this.hasClass('wccbel-timepicker') || $this.hasClass('wccbel-datetimepicker')) {
                if ($this.attr('data-val') == $this.val()) {
                    e.preventDefault();
                    return false;
                }
            }

            let couponData = [];
            let couponIds = [];
            let tdElement = $this.closest('td');
            if ($('#wccbel-bind-edit').prop('checked') === true) {
                couponIds = wccbelGetCouponsChecked();
            }
            couponIds.push($this.attr("data-item-id"));
            let wccbelValue;
            switch (tdElement.attr("data-content-type")) {
                case 'checkbox_dual_mode':
                    wccbelValue = $this.prop("checked") ? "yes" : "no";
                    break;
                case 'checkbox':
                    let checked = [];
                    tdElement.find('input[type=checkbox]:checked').each(function () {
                        checked.push($(this).val());
                    });
                    wccbelValue = checked;
                    break;
                default:
                    wccbelValue = $this.val();
                    break;
            }

            couponData.push({
                name: tdElement.attr('data-name'),
                sub_name: (tdElement.attr('data-sub-name')) ? tdElement.attr('data-sub-name') : '',
                type: tdElement.attr('data-update-type'),
                value: wccbelValue,
                operation: 'inline_edit'
            });

            wccbelCouponEdit(couponIds, couponData);
        }, 250)
    });

    $(document).on("click", ".wccbel-inline-edit-clear-date", function () {
        let couponData = [];
        let couponIds = [];
        let tdElement = $(this).closest('td');

        if ($('#wccbel-bind-edit').prop('checked') === true) {
            couponIds = wccbelGetCouponsChecked();
        }
        couponIds.push($(this).attr("data-item-id"));
        couponData.push({
            name: tdElement.attr('data-name'),
            sub_name: (tdElement.attr('data-sub-name')) ? tdElement.attr('data-sub-name') : '',
            type: tdElement.attr('data-update-type'),
            value: '',
            operation: 'inline_edit'
        });

        wccbelCouponEdit(couponIds, couponData);
    });

    $(document).on("click", ".wccbel-edit-action-price-calculator", function () {
        let couponId = $(this).attr("data-item-id");
        let fieldName = $(this).attr("data-field");
        let couponIds = [];
        let couponData = [];

        if ($('#wccbel-bind-edit').prop('checked') === true) {
            couponIds = wccbelGetCouponsChecked();
        }
        couponIds.push(couponId);

        couponData.push({
            name: fieldName,
            sub_name: '',
            type: $(this).attr('data-update-type'),
            operator: $("#wccbel-" + fieldName + "-calculator-operator-" + couponId).val(),
            value: $("#wccbel-" + fieldName + "-calculator-value-" + couponId).val(),
            operator_type: $("#wccbel-" + fieldName + "-calculator-type-" + couponId).val(),
            round: $("#wccbel-" + fieldName + "-calculator-round-" + couponId).val()
        });

        wccbelCouponEdit(couponIds, couponData);
    });

    $(document).on("click", ".wccbel-bulk-edit-delete-action", function () {
        let deleteType = $(this).attr('data-delete-type');
        let CouponIds = wccbelGetCouponsChecked();

        if (!CouponIds.length && deleteType != 'all') {
            swal({
                title: "Please select one coupon",
                type: "warning"
            });
            return false;
        }

        let alertMessage = "Are you sure?";

        if (deleteType == 'all') {
            alertMessage = ($('.wccbel-reset-filter-form:visible').length) ? "All of filtered coupons will be delete. Are you sure?" : "All of coupons will be delete. Are you sure?";
        }

        swal({
            title: alertMessage,
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                if (CouponIds.length > 0 || deleteType == 'all') {
                    wccbelDeleteCoupon(CouponIds, deleteType);
                } else {
                    swal({
                        title: "Please Select Coupon !",
                        type: "warning"
                    });
                }
            }
        });
    });

    $(document).on("click", "#wccbel-bulk-edit-duplicate-start", function () {
        let couponIDs = $("input.wccbel-check-item:checkbox:checked").map(function () {
            if ($(this).attr('data-item-type') === 'variation') {
                swal({
                    title: "Duplicate for variations coupon is disabled!",
                    type: "warning"
                });
                return false;
            }
            return $(this).val();
        }).get();
        wccbelDuplicateCoupon(couponIDs, parseInt($("#wccbel-bulk-edit-duplicate-number").val()));
    });

    $(document).on("click", "#wccbel-create-new-item", function () {
        let count = $("#wccbel-new-item-count").val();
        wccbelCreateNewCoupon(count);
    });

    $(document).on("click", "#wccbel-column-profiles-save-as-new-preset", function () {
        let presetKey = $("#wccbel-column-profiles-choose").val();
        let items = $(".wccbel-column-profile-fields input:checkbox:checked").map(function () {
            return $(this).val();
        }).get();
        wccbelSaveColumnProfile(presetKey, items, "save_as_new");
    });

    $(document).on("click", "#wccbel-column-profiles-update-changes", function () {
        let presetKey = $("#wccbel-column-profiles-choose").val();
        let items = $(".wccbel-column-profile-fields input:checkbox:checked").map(function () {
            return $(this).val();
        }).get();
        wccbelSaveColumnProfile(presetKey, items, "update_changes");
    });

    $(document).on("click", ".wccbel-bulk-edit-filter-profile-load", function () {
        wccbelLoadFilterProfile($(this).val());
        if ($(this).val() !== "default") {
            $("#wccbel-bulk-edit-reset-filter").show();
        }
        $(".wccbel-filter-profiles-items tr").removeClass("wccbel-filter-profile-loaded");
        $(this).closest("tr").addClass("wccbel-filter-profile-loaded");

        if (WCCBEL_DATA.wccbel_settings.close_popup_after_applying == 'yes') {
            wccbelCloseFloatSideModal();
        }
    });

    $(document).on("click", ".wccbel-bulk-edit-filter-profile-delete", function () {
        let presetKey = $(this).val();
        let item = $(this).closest("tr");
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                wccbelDeleteFilterProfile(presetKey);
                if (item.hasClass('wccbel-filter-profile-loaded')) {
                    $('.wccbel-filter-profiles-items tbody tr:first-child').addClass('wccbel-filter-profile-loaded').find('input[type=radio]').prop('checked', true);
                    $('#wccbel-bulk-edit-reset-filter').trigger('click');
                }
                item.remove();

                if (WCCBEL_DATA.wccbel_settings.close_popup_after_applying == 'yes') {
                    wccbelCloseFloatSideModal();
                }
            }
        });
    });

    $(document).on("change", "input.wccbel-filter-profile-use-always-item", function () {
        if ($(this).val() !== "default") {
            $("#wccbel-bulk-edit-reset-filter").show();
        } else {
            $("#wccbel-bulk-edit-reset-filter").hide();
        }
        wccbelFilterProfileChangeUseAlways($(this).val());

        if (WCCBEL_DATA.wccbel_settings.close_popup_after_applying == 'yes') {
            wccbelCloseFloatSideModal();
        }
    });

    $(document).on("click", ".wccbel-filter-form-action", function (e) {
        let data = wccbelGetCurrentFilterData();
        let page;
        let action = $(this).attr("data-search-action");
        if (action === "pagination") {
            page = $(this).attr("data-index");
        }
        if (action === "quick_search" && $('#wccbel-quick-search-text').val() !== '') {
            wccbelResetFilterForm();
        }
        if (action === "pro_search") {
            $('#wccbel-bulk-edit-reset-filter').show();
            wccbelResetQuickSearchForm();
            $(".wccbel-filter-profiles-items tr").removeClass("wccbel-filter-profile-loaded");
            $('input.wccbel-filter-profile-use-always-item[value="default"]').prop("checked", true).closest("tr");
            wccbelFilterProfileChangeUseAlways("default");
        }
        wccbelCouponsFilter(data, action, null, page);

        if (WCCBEL_DATA.wccbel_settings.close_popup_after_applying == 'yes') {
            wccbelCloseFloatSideModal();
        }

        wccbelCheckResetFilterButton();
    });

    $(document).on("click", "#wccbel-filter-form-reset", function () {
        wccbelResetFilters();
    });

    $(document).on("click", "#wccbel-bulk-edit-reset-filter", function () {
        wccbelResetFilters();
    });

    $(document).on("change", "#wccbel-quick-search-field", function () {
        let options = $("#wccbel-quick-search-operator option");
        switch ($(this).val()) {
            case "title":
                options.each(function () {
                    $(this).closest("select").prop("selectedIndex", 0);
                    $(this).prop("disabled", false);
                });
                break;
            case "id":
                options.each(function () {
                    $(this).closest("select").prop("selectedIndex", 1);
                    if ($(this).attr("value") === "exact") {
                        $(this).prop("disabled", false);
                    } else {
                        $(this).prop("disabled", true);
                    }
                });
                break;
        }
    });

    // Quick Per Page
    $("#wccbel-quick-per-page").on("change", function () {
        wccbelChangeCountPerPage($(this).val());
    });

    $(document).on("click", ".wccbel-edit-action-with-button", function () {
        let couponIds = [];
        let couponData = [];

        if ($('#wccbel-bind-edit').prop('checked') === true) {
            couponIds = wccbelGetCouponsChecked();
        }
        couponIds.push($(this).attr("data-item-id"));

        let wccbelValue;
        switch ($(this).attr("data-content-type")) {
            case "textarea":
                wccbelValue = tinymce.get("wccbel-text-editor").getContent();
                break;
            case "select_coupons":
                wccbelValue = $('#wccbel-select-coupons-value').val();
                break;
            case "select_files":
                let names = $('.wccbel-inline-edit-file-name').map(function () {
                    return $(this).val();
                }).get();

                let urls = $('.wccbel-inline-edit-file-url').map(function () {
                    return $(this).val();
                }).get();

                wccbelValue = {
                    files_name: names,
                    files_url: urls,
                };
                break;
            case "file":
                wccbelValue = $('#wccbel-modal-file #wccbel-file-id').val();
                break;
            case "image":
                wccbelValue = $(this).attr("data-image-id");
                break;
            case "gallery":
                wccbelValue = $("#wccbel-modal-gallery-items input.wccbel-inline-edit-gallery-image-ids").map(function () {
                    return $(this).val();
                }).get();
                break;
        }

        couponData.push({
            name: $(this).attr('data-name'),
            sub_name: ($(this).attr('data-sub-name')) ? $(this).attr('data-sub-name') : '',
            type: $(this).attr('data-update-type'),
            value: wccbelValue,
            operation: 'inline_edit'
        });

        wccbelCouponEdit(couponIds, couponData);
    });

    $(document).on("click", ".wccbel-load-text-editor", function () {
        let couponId = $(this).attr("data-item-id");
        let field = $(this).attr("data-field");
        let fieldType = $(this).attr("data-field-type");
        $('#wccbel-modal-text-editor-item-title').text($(this).attr('data-item-name'));
        $("#wccbel-text-editor-apply").attr("data-field", field).attr("data-field-type", fieldType).attr("data-item-id", couponId);
        $.ajax({
            url: WCCBEL_DATA.ajax_url,
            type: "post",
            dataType: "json",
            data: {
                action: "wccbel_get_text_editor_content",
                nonce: WCCBEL_DATA.ajax_nonce,
                coupon_id: couponId,
                field: field,
                field_type: fieldType
            },
            success: function (response) {
                if (response.success) {
                    tinymce.get("wccbel-text-editor").setContent(response.content);
                    tinymce.execCommand('mceFocus', false, 'wccbel-text-editor');
                }
            },
            error: function () { }
        });
    });

    //Search
    $(document).on("keyup", ".wccbel-search-in-list", function () {
        let wccbelSearchValue = this.value.toLowerCase().trim();
        $($(this).attr("data-id") + " .wccbel-coupon-items-list li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(wccbelSearchValue) > -1);
        });
    });

    $(document).on('click', 'button[data-target="#wccbel-modal-select-coupons"]', function () {
        let childrenIds = $(this).attr('data-children-ids').split(',');
        $('#wccbel-modal-select-coupons-item-title').text($(this).attr('data-item-name'));
        $('#wccbel-modal-select-coupons .wccbel-edit-action-with-button').attr('data-item-id', $(this).attr('data-item-id')).attr('data-field', $(this).attr('data-field')).attr('data-field-type', $(this).attr('data-field-type'));
        let coupons = $('#wccbel-select-coupons-value');
        if (coupons.length > 0) {
            coupons.val(childrenIds).change();
        }
    });

    $(document).on('click', '#wccbel-modal-select-files-add-file-item', function () {
        wccbelAddNewFileItem();
    });

    $(document).on('click', 'button[data-toggle="modal"][data-target="#wccbel-modal-select-files"]', function () {
        $('#wccbel-modal-select-files-apply').attr('data-item-id', $(this).attr('data-item-id')).attr('data-field', $(this).attr(('data-field')));
        $('#wccbel-modal-select-files-item-title').text($(this).closest('td').attr('data-col-title'));
        wccbelGetCouponFiles($(this).attr('data-item-id'));
    });

    $(document).on('click', '.wccbel-inline-edit-file-remove-item', function () {
        $(this).closest('.wccbel-modal-select-files-file-item').remove();
    });

    $(document).on("change", "select[data-field=operator]", function () {
        let id = $(this).closest(".wccbel-form-group").find("label").attr("for");
        if ($(this).val() === "text_replace") {
            $(this).closest(".wccbel-form-group").append('<div class="wccbel-bulk-edit-form-extra-field"><select id="' + id + '-sensitive" data-field="sensitive"><option value="yes">Same Case</option><option value="no">Ignore Case</option></select><input type="text" id="' + id + '-replace" data-field="replace" placeholder="Text ..."><select class="wccbel-bulk-edit-form-variable" title="Select Variable" data-field="variable"><option value="">Variable</option><option value="title">Coupon Code</option><option value="id">ID</option></select></div>');
        } else if ($(this).val() === "number_round") {
            $(this).closest(".wccbel-form-group").append('<div class="wccbel-bulk-edit-form-extra-field"><select id="' + id + '-round-item"><option value="5">5</option><option value="10">10</option><option value="19">19</option><option value="29">29</option><option value="39">39</option><option value="49">49</option><option value="59">59</option><option value="69">69</option><option value="79">79</option><option value="89">89</option><option value="99">99</option></select></div>');
        } else {
            $(this).closest(".wccbel-form-group").find(".wccbel-bulk-edit-form-extra-field").remove();
        }
        if ($(this).val() === "number_clear") {
            $(this).closest(".wccbel-form-group").find('input[data-field=value]').prop('disabled', true);
        } else {
            $(this).closest(".wccbel-form-group").find('input[data-field=value]').prop('disabled', false);
        }
        changedTabs($(this));
    });

    $(document).on("change", ".wccbel-bulk-edit-form-variable", function () {
        let newVal = $(this).val() ? $(this).closest("div").find("input[type=text]").val() + "{" + $(this).val() + "}" : "";
        $(this).closest("div").find("input[type=text]").first().val(newVal).change();
    });

    $("#wccbel-float-side-modal-bulk-edit .wccbel-tab-content-item").on("change", "[data-field=value]", function () {
        changedTabs($(this));
    });

    $(document).on("change", ".wccbel-date-from", function () {
        let field_to = $('#' + $(this).attr('data-to-id'));
        let datepicker = true;
        let timepicker = false;
        let format = 'Y/m/d';

        if ($(this).hasClass('wccbel-datetimepicker')) {
            timepicker = true;
            format = 'Y/m/d H:i'
        }

        if ($(this).hasClass('wccbel-timepicker')) {
            datepicker = false;
            timepicker = true;
            format = 'H:i'
        }

        field_to.val("");
        field_to.datetimepicker("destroy");
        field_to.datetimepicker({
            format: format,
            datepicker: datepicker,
            timepicker: timepicker,
            minDate: $(this).val(),
        });
    });

    $(document).on("click", ".wccbel-bulk-edit-form-remove-image", function () {
        $(this).closest("div").remove();
        $("#wccbel-bulk-edit-form-coupon-image").val("");
    });

    $(document).on("click", ".wccbel-bulk-edit-form-remove-gallery-item", function () {
        $(this).closest("div").remove();
        $("#wccbel-bulk-edit-form-coupon-gallery input[value=" + $(this).attr("data-id") + "]").remove();
    });

    var sortType = 'DESC'
    $(document).on('click', '.wccbel-sortable-column', function () {
        if (sortType === 'DESC') {
            sortType = 'ASC';
            $(this).find('i.wccbel-sortable-column-icon').text('d');
        } else {
            sortType = 'DESC';
            $(this).find('i.wccbel-sortable-column-icon').text('u');
        }
        wccbelSortByColumn($(this).attr('data-column-name'), sortType);
    });

    $(document).on("click", ".wccbel-column-manager-edit-field-btn", function () {
        $('#wccbel-modal-column-manager-edit-preset .wccbel-box-loading').show();
        let presetKey = $(this).val();
        $('#wccbel-modal-column-manager-edit-preset .items').html('');
        $("#wccbel-column-manager-edit-preset-key").val(presetKey);
        $("#wccbel-column-manager-edit-preset-name").val($(this).attr("data-preset-name"));
        wccbelColumnManagerFieldsGetForEdit(presetKey);
    });

    $(document).on("click", "#wccbel-get-meta-fields-by-coupon-id", function () {
        $(".wccbel-meta-fields-empty-text").hide();
        let input = $("#wccbel-add-meta-fields-coupon-id");
        wccbelAddMetaKeysByCouponID(input.val());
        input.val("");
    });

    $(document).on("click", "#wccbel-bulk-edit-undo", function () {
        wccbelHistoryUndo();
    });

    $(document).on("click", "#wccbel-bulk-edit-redo", function () {
        wccbelHistoryRedo();
    });

    $(document).on("click", "#wccbel-history-filter-apply", function () {
        let filters = {
            operation: $("#wccbel-history-filter-operation").val(),
            author: $("#wccbel-history-filter-author").val(),
            fields: $("#wccbel-history-filter-fields").val(),
            date: {
                from: $("#wccbel-history-filter-date-from").val(),
                to: $("#wccbel-history-filter-date-to").val()
            }
        };
        wccbelHistoryFilter(filters);
    });

    $(document).on("click", "#wccbel-history-filter-reset", function () {
        $(".wccbel-history-filter-fields input").val("");
        $(".wccbel-history-filter-fields select").val("").change();
        wccbelHistoryFilter();
    });

    $(document).on("change", ".wccbel-meta-fields-main-type", function () {
        let item = $(this).closest('.wccbel-meta-fields-right-item');
        if ($(this).val() === "textinput") {
            item.find(".wccbel-meta-fields-sub-type").show();
        } else {
            item.find(".wccbel-meta-fields-sub-type").hide();
        }

        if ($.inArray($(this).val(), ['select', 'array']) !== -1) {
            item.find(".wccbel-meta-fields-key-value").show();
        } else {
            item.find(".wccbel-meta-fields-key-value").hide();
        }
    });

    $("#wccbel-column-manager-add-new-preset").on("submit", function (e) {
        if ($(this).find(".wccbel-column-manager-added-fields .items .wccbel-column-manager-right-item").length < 1) {
            e.preventDefault();
            swal({
                title: "Please Add Columns !",
                type: "warning"
            });
        }
    });

    $(document).on("click", "#wccbel-bulk-edit-form-reset", function () {
        wccbelResetBulkEditForm();
        $("nav.wccbel-tabs-navbar li a").removeClass("wccbel-tab-changed");
    });

    $(document).on("click", "#wccbel-filter-form-save-preset", function () {
        let presetName = $("#wccbel-filter-form-save-preset-name").val();
        if (presetName !== "") {
            let data = wccbelGetProSearchData();
            wccbelSaveFilterPreset(data, presetName);
        } else {
            swal({
                title: "Preset name is required !",
                type: "warning"
            });
        }
    });

    $(document).on("click", "#wccbel-bulk-edit-form-do-bulk-edit", function (e) {
        let couponIds = wccbelGetCouponsChecked();
        let couponData = [];

        $("#wccbel-float-side-modal-bulk-edit .wccbel-form-group").each(function () {
            let value;
            if ($(this).find("[data-field=value]").length > 1) {
                value = $(this).find("[data-field=value]").map(function () {
                    if ($(this).val() !== '') {
                        return $(this).val();
                    }
                }).get();
            } else {
                value = $(this).find("[data-field=value]").val();
            }

            if (($.isArray(value) && value.length > 0) || (!$.isArray(value) && value)) {
                let name = $(this).attr('data-name');
                let type = $(this).attr('data-type');

                couponData.push({
                    name: name,
                    sub_name: ($(this).attr('data-sub-name')) ? $(this).attr('data-sub-name') : '',
                    type: type,
                    operator: $(this).find("[data-field=operator]").val(),
                    value: value,
                    replace: $(this).find("[data-field=replace]").val(),
                    sensitive: $(this).find("[data-field=sensitive]").val(),
                    round: $(this).find("[data-field=round]").val(),
                    operation: 'bulk_edit'
                });
            }
        });

        if (couponIds.length > 0) {
            if (WCCBEL_DATA.wccbel_settings.close_popup_after_applying == 'yes') {
                wccbelCloseFloatSideModal();
            }
            wccbelCouponEdit(couponIds, couponData);
            if (WCCBEL_DATA.wccbel_settings.keep_filled_data_in_bulk_edit_form == 'yes') {
                wccbelResetBulkEditForm();
            }
        } else {
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
                confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
                confirmButtonText: "Yes, i'm sure",
                closeOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    if (WCCBEL_DATA.wccbel_settings.close_popup_after_applying == 'yes') {
                        wccbelCloseFloatSideModal();
                    }
                    wccbelCouponEdit(couponIds, couponData);
                }
            });
        }
    });

    $(document).on('click', '[data-target="#wccbel-modal-new-item"]', function () {
        $('#wccbel-new-item-title').html("New Coupon");
        $('#wccbel-new-item-description').html("Enter how many new coupon(s) to create!");
    });

    // keypress: Enter
    $(document).on("keypress", function (e) {
        if (e.keyCode === 13) {
            if ($("#wccbel-filter-form-content").attr("data-visibility") === "visible") {
                wccbelReloadCoupons();
                $("#wccbel-bulk-edit-reset-filter").show();
                wccbelFilterFormClose();
            }
            if ($('#wccbel-quick-search-text').val() !== '' && $($('#wccbel-last-modal-opened').val()).css('display') !== 'block' && $('.wccbel-tabs-list button[data-content="bulk-edit"]').hasClass('selected')) {
                wccbelReloadCoupons();
                $('#wccbel-quick-search-reset').show();
            }
            if ($("#wccbel-modal-new-coupon-taxonomy").css("display") === "block") {
                $("#wccbel-create-new-coupon-taxonomy").trigger("click");
            }
            if ($("#wccbel-modal-new-item").css("display") === "block") {
                $("#wccbel-create-new-item").trigger("click");
            }
            if ($("#wccbel-modal-item-duplicate").css("display") === "block") {
                $("#wccbel-bulk-edit-duplicate-start").trigger("click");
            }

            let metaFieldManualInput = $("#wccbel-meta-fields-manual_key_name");
            let metaFieldCouponId = $("#wccbel-add-meta-fields-coupon-id");
            if (metaFieldManualInput.val() !== "") {
                $(".wccbel-meta-fields-empty-text").hide();
                wccbelAddMetaKeysManual(metaFieldManualInput.val());
                metaFieldManualInput.val("");
            }
            if (metaFieldCouponId.val() !== "") {
                $(".wccbel-meta-fields-empty-text").hide();
                wccbelAddMetaKeysByCouponID(metaFieldCouponId.val());
                metaFieldCouponId.val("");
            }

            // filter form
            if ($('#wccbel-float-side-modal-filter:visible').length) {
                $('#wccbel-float-side-modal-filter:visible').find('.wccbel-filter-form-action').trigger('click');
            }
        }
    });

    $(document).on("click", 'button.wccbel-calculator[data-target="#wccbel-modal-numeric-calculator"]', function () {
        let btn = $("#wccbel-modal-numeric-calculator .wccbel-edit-action-numeric-calculator");
        let tdElement = $(this).closest('td');
        btn.attr("data-item-id", $(this).attr("data-item-id"));
        btn.attr("data-field", $(this).attr("data-field"));
        btn.attr("data-field-type", $(this).attr("data-field-type"));
        btn.attr("data-name", tdElement.attr('data-name'));
        btn.attr("data-update-type", tdElement.attr('data-update-type'));
        if ($(this).attr('data-field') === 'download_limit' || $(this).attr('data-field') === 'download_expiry') {
            $('#wccbel-modal-numeric-calculator #wccbel-numeric-calculator-type').val('n').change().hide();
            $('#wccbel-modal-numeric-calculator #wccbel-numeric-calculator-round').val('').change().hide();
        } else {
            $('#wccbel-modal-numeric-calculator #wccbel-numeric-calculator-type').show();
            $('#wccbel-modal-numeric-calculator #wccbel-numeric-calculator-round').show();
        }
        $('#wccbel-modal-numeric-calculator-item-title').text($(this).attr('data-item-name'));
    });

    $(document).on("click", ".wccbel-edit-action-numeric-calculator", function () {
        let couponId = $(this).attr("data-item-id");
        let couponIds = [];
        let couponData = [];

        if ($('#wccbel-bind-edit').prop('checked') === true) {
            couponIds = wccbelGetCouponsChecked();
        }
        couponIds.push(couponId);

        couponData.push({
            name: $(this).attr("data-name"),
            sub_name: ($(this).attr("data-name")) ? $(this).attr("data-name") : '',
            type: $(this).attr('data-update-type'),
            operator: $("#wccbel-numeric-calculator-operator").val(),
            value: $("#wccbel-numeric-calculator-value").val(),
            operator_type: ($("#wccbel-numeric-calculator-type").val()) ? $("#wccbel-numeric-calculator-type").val() : 'n',
            round: $("#wccbel-numeric-calculator-round").val()
        });

        wccbelCouponEdit(couponIds, couponData);
    });

    $(document).on('click', '#wccbel-quick-search-button', function () {
        if ($('#wccbel-quick-search-text').val() !== '') {
            $('#wccbel-quick-search-reset').show();
        }
    });

    $(document).on('click', '#wccbel-quick-search-reset', function () {
        wccbelResetFilters()
    });

    $(document).on(
        {
            mouseenter: function () {
                $(this).addClass('wccbel-disabled-column');
            },
            mouseleave: function () {
                $(this).removeClass('wccbel-disabled-column');
            }
        },
        "td[data-editable=no]"
    );

    $(document).on('click', '.wccbel-bulk-edit-status-filter-item', function () {
        $('.wccbel-top-nav-status-filter').hide();

        $('.wccbel-bulk-edit-status-filter-item').removeClass('active');
        $(this).addClass('active');
        $('.wccbel-status-filter-selected-name').text(' - ' + $(this).text());

        if ($(this).attr('data-status') === 'all') {
            $('#wccbel-filter-form-reset').trigger('click');
        } else {
            $('#wccbel-filter-form-coupon-status').val($(this).attr('data-status')).change();
            setTimeout(function () {
                $('#wccbel-filter-form-get-coupons').trigger('click');
            }, 250);
        }
    });

    $(document).on('click', '.wccbel-coupon-products-button', function () {
        let couponId = $(this).attr('data-item-id');
        let field = $(this).attr('data-field');
        $('#wccbel-modal-products-item-title').text($(this).attr('data-item-name'));
        $('#wccbel-modal-products-items').html('').val('').change();
        $('.wccbel-modal-products-save-changes').attr('data-item-id', couponId).attr('data-field', field).attr('data-name', field);
        wccbelGetCouponProducts(couponId, field);
    });

    $(document).on('click', '.wccbel-coupon-categories-button', function () {
        let couponId = $(this).attr('data-item-id');
        let field = $(this).attr('data-field');
        $('#wccbel-modal-categories-item-title').text($(this).attr('data-item-name'));
        $('#wccbel-modal-categories-items').html('').val('').change();
        $('.wccbel-modal-categories-save-changes').attr('data-item-id', couponId).attr('data-field', field).attr('data-name', field);
        wccbelGetCouponCategories(couponId, field);
    });

    $(document).on('click', '.wccbel-coupon-used-in-button', function () {
        let couponCode = $(this).attr('data-item-name');
        $('#wccbel-modal-used-in-item-title').text(" - " + $(this).attr('data-item-name'));
        $('#wccbel-modal-coupon-used-in-items').html('');
        wccbelGetCouponUsedIn(couponCode);
    });

    $(document).on('click', '.wccbel-coupon-used-by-button', function () {
        let couponId = $(this).attr('data-item-id');
        $('#wccbel-modal-used-by-item-title').text(" - " + $(this).attr('data-item-name'));
        $('#wccbel-modal-coupon-used-by-items').html('');
        wccbelGetCouponUsedBy(couponId);
    });

    $(document).on('click', '.wccbel-modal-products-save-changes', function () {
        let couponIds = [];

        if ($('#wccbel-bind-edit').prop('checked') === true) {
            couponIds = wccbelGetCouponsChecked();
        }

        couponIds.push($(this).attr('data-item-id'));

        let couponData = [{
            name: $(this).attr('data-field'),
            type: 'woocommerce_field',
            value: $('#wccbel-modal-products-items').val()
        }];
        wccbelCouponEdit(couponIds, couponData);
    });

    $(document).on('click', '.wccbel-modal-categories-save-changes', function () {
        let couponIds = [];

        if ($('#wccbel-bind-edit').prop('checked') === true) {
            couponIds = wccbelGetCouponsChecked();
        }

        couponIds.push($(this).attr('data-item-id'));

        let couponData = [{
            name: $(this).attr('data-field'),
            type: 'woocommerce_field',
            value: $('#wccbel-modal-categories-items').val()
        }];
        wccbelCouponEdit(couponIds, couponData);
    });

    if (itemIdInUrl && itemIdInUrl > 0) {
        wccbelResetFilterForm();
        setTimeout(function () {
            $('#wccbel-filter-form-coupon-ids').val(itemIdInUrl);
            $('#wccbel-filter-form-get-coupons').trigger('click');
        }, 500);
    }

    $(document).on('click', '.wccbel-delete-item-btn', function () {
        let couponIds = [];
        couponIds.push($(this).attr('data-item-id'));
        let deleteType = $(this).attr('data-delete-type');
        swal({
            title: 'Are you sure?',
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                wccbelDeleteCoupon(couponIds, deleteType);
            }
        });
    });

    $(document).on('click', '.wccbel-restore-item-btn', function () {
        let couponIds = [];
        couponIds.push($(this).attr('data-item-id'));
        swal({
            title: 'Are you sure?',
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                wccbelRestoreCoupon(couponIds);
            }
        });
    });

    $(document).on('change', '#wccbel-filter-form-coupon-status', function () {
        if ($.isArray($(this).val()) && $.inArray('trash', $(this).val()) !== -1) {
            $('.wccbel-top-navigation-trash-buttons').show();
        } else {
            $('.wccbel-top-navigation-trash-buttons').hide();
        }
    });

    $(document).on('click', '#wccbel-bulk-edit-trash-empty', function () {
        swal({
            title: 'Are you sure?',
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                wccbelEmptyTrash();
            }
        });
    });

    $(document).on('click', '#wccbel-bulk-edit-trash-restore', function () {
        let couponIds = wccbelGetCouponsChecked();
        wccbelRestoreCoupon(couponIds);
    });

    $(document).on('click', '.wccbel-history-pagination-item', function () {
        $('.wccbel-history-pagination-loading').show();

        let filters = {
            operation: $("#wccbel-history-filter-operation").val(),
            author: $("#wccbel-history-filter-author").val(),
            fields: $("#wccbel-history-filter-fields").val(),
            date: {
                from: $("#wccbel-history-filter-date-from").val(),
                to: $("#wccbel-history-filter-date-to").val()
            }
        };

        wccbelHistoryChangePage($(this).attr('data-index'), filters);
    });

    $(document).on('click', '.wccbel-reload-table', function () {
        wccbelReloadCoupons();
    });

    $(document).on('click', '.wccbel-reset-filter-form', function () {
        wccbelResetFilters();
    });

    $(document).on('change', '#wccbel-filter-form-coupon-status', function () {
        if ($.inArray('trash', $(this).val()) !== -1) {
            $('.wccbel-trash-options').closest('li').show();
        } else {
            $('.wccbel-trash-options').closest('li').hide();
        }
    });

    $(document).on('click', '.wccbel-trash-option-restore-selected-items', function () {
        let couponIds = wccbelGetCouponsChecked();
        if (!couponIds.length) {
            swal({
                title: "Please select one coupon",
                type: "warning"
            });
            return false;
        } else {
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
                confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
                confirmButtonText: "Yes, i'm sure",
                closeOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    wccbelRestoreCoupon(couponIds);
                }
            });
        }
    });

    $(document).on('click', '.wccbel-trash-option-restore-all', function () {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                wccbelRestoreCoupon([]);
            }
        });
    });

    $(document).on('click', '.wccbel-trash-option-delete-selected-items', function () {
        let couponIds = wccbelGetCouponsChecked();
        if (!couponIds.length) {
            swal({
                title: "Please select one coupon",
                type: "warning"
            });
            return false;
        } else {
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
                confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
                confirmButtonText: "Yes, i'm sure",
                closeOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    wccbelDeleteCoupon(couponIds, 'permanently');
                }
            });
        }
    });

    $(document).on('click', '.wccbel-trash-option-delete-all', function () {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wccbel-button wccbel-button-lg wccbel-button-white",
            confirmButtonClass: "wccbel-button wccbel-button-lg wccbel-button-green",
            confirmButtonText: "Yes, i'm sure",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                wccbelEmptyTrash()
            }
        });
    });

    wccbelGetProducts();
    wccbelGetCategories();
    wccbelGetDefaultFilterProfileCoupons();
});