"use strict";

var wccbelOpenFullScreenIcon = '<i class="wccbel-icon-enlarge"></i>';
var wccbelCloseFullScreenIcon = '<i class="wccbel-icon-shrink"></i>';

function openFullscreen() {
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
        document.documentElement.webkitRequestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
        document.documentElement.msRequestFullscreen();
    }
}

function wccbelDataTableFixSize() {
    jQuery('#wccbel-main').css({
        top: jQuery('#wpadminbar').height() + 'px',
        "padding-left": (jQuery('#adminmenu:visible').length) ? jQuery('#adminmenu').width() + 'px' : 0
    });

    jQuery('#wccbel-loading').css({
        top: jQuery('#wpadminbar').height() + 'px',
    });

    let height = parseInt(jQuery(window).height()) - parseInt(jQuery('#wccbel-header').height() + 85);

    jQuery('.wccbel-table').css({
        "max-height": height + 'px'
    });
}

function exitFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    }
}

function wccbelFullscreenHandler() {
    if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
        jQuery('#wccbel-full-screen').html(wccbelOpenFullScreenIcon).attr('title', 'Full screen');
        jQuery('#adminmenuback, #adminmenuwrap').show();
        jQuery('#wpcontent, #wpfooter').css({ "margin-left": "160px" });
    } else {
        jQuery('#wccbel-full-screen').html(wccbelCloseFullScreenIcon).attr('title', 'Exit Full screen');
        jQuery('#adminmenuback, #adminmenuwrap').hide();
        jQuery('#wpcontent, #wpfooter').css({ "margin-left": 0 });
    }

    wccbelDataTableFixSize();
}

function wccbelOpenTab(item) {
    let wccbelTabItem = item;
    let wccbelParentContent = wccbelTabItem.closest(".wccbel-tabs-list");
    let wccbelParentContentID = wccbelParentContent.attr("data-content-id");
    let wccbelDataBox = wccbelTabItem.attr("data-content");
    wccbelParentContent.find("li button.selected").removeClass("selected");
    if (wccbelTabItem.closest('.wccbel-sub-tab').length > 0) {
        wccbelTabItem.closest('li.wccbel-has-sub-tab').find('button').first().addClass("selected");
    } else {
        wccbelTabItem.addClass("selected");
    }

    if (item.closest('.wccbel-tabs-list').attr('data-content-id') && item.closest('.wccbel-tabs-list').attr('data-content-id') == 'wccbel-main-tabs-contents') {
        jQuery('.wccbel-tabs-list[data-content-id="wccbel-main-tabs-contents"] li[data-depend] button').not('.wccbel-tab-item').addClass('disabled');
        jQuery('.wccbel-tabs-list[data-content-id="wccbel-main-tabs-contents"] li[data-depend="' + wccbelDataBox + '"] button').removeClass('disabled');
    }

    jQuery("#" + wccbelParentContentID).children("div.selected").removeClass("selected");
    jQuery("#" + wccbelParentContentID + " div[data-content=" + wccbelDataBox + "]").addClass("selected");

    if (item.attr("data-type") === "main-tab") {
        wccbelFilterFormClose();
    }
}

function wccbelFixModalHeight(modal) {
    if (!modal.attr('data-height-fixed') || modal.attr('data-height-fixed') != 'true') {
        let footerHeight = 0;
        let contentHeight = modal.find(".wccbel-modal-content").height();
        let titleHeight = modal.find(".wccbel-modal-title").height();
        if (modal.find(".wccbel-modal-footer").length > 0) {
            footerHeight = modal.find(".wccbel-modal-footer").height();
        }

        let modalMargin = parseInt((parseInt(jQuery('body').height()) * 20) / 100);
        let bodyHeight = (modal.find(".wccbel-modal-body-content").length) ? parseInt(modal.find(".wccbel-modal-body-content").height() + 30) : contentHeight;
        let bodyMaxHeight = parseInt(jQuery('body').height()) - (titleHeight + footerHeight + modalMargin);
        if (modal.find('.wccbel-modal-top-search').length > 0) {
            bodyHeight += parseInt(modal.find('.wccbel-modal-top-search').height() + 30);
            bodyMaxHeight -= parseInt(modal.find('.wccbel-modal-top-search').height());
        }

        modal.find(".wccbel-modal-content").css({
            "height": parseInt(titleHeight + footerHeight + bodyHeight) + 'px'
        });
        modal.find(".wccbel-modal-body").css({
            "height": parseInt(bodyHeight) + 'px',
            'max-height': parseInt(bodyMaxHeight) + 'px'
        });
        modal.find(".wccbel-modal-box").css({
            "height": parseInt(titleHeight + footerHeight + bodyHeight) + 'px'
        });
        modal.attr('data-height-fixed', 'true');
    }
}

function wccbelOpenFloatSideModal(targetId) {
    let modal = jQuery(targetId);
    modal.fadeIn(20);
    modal.find(".wccbel-float-side-modal-box").animate({
        right: 0
    }, 180);
}

function wccbelCloseFloatSideModal() {
    // fix conflict with "Woo Invoice Pro" plugin
    jQuery('body').removeClass('_winvoice-modal-open');
    jQuery('._winvoice-modal-backdrop').remove();

    jQuery('.wccbel-float-side-modal-box').animate({
        right: "-80%"
    }, 180);
    jQuery('.wccbel-float-side-modal').fadeOut(200);
}

function wccbelCloseModal() {
    // fix conflict with "Woo Invoice Pro" plugin
    jQuery('body').removeClass('_winvoice-modal-open');
    jQuery('._winvoice-modal-backdrop').remove();

    let lastModalOpened = jQuery('#wccbel-last-modal-opened');
    let modal = jQuery(lastModalOpened.val());
    if (lastModalOpened.val() !== '') {
        modal.find(' .wccbel-modal-box').fadeOut();
        modal.fadeOut();
        lastModalOpened.val('');
    } else {
        let lastModal = jQuery('.wccbel-modal:visible').last();
        lastModal.find('.wccbel-modal-box').fadeOut();
        lastModal.fadeOut();
    }

    setTimeout(function () {
        modal.find('.wccbel-modal-box').css({
            height: 'auto',
            "max-height": '80%'
        });
        modal.find('.wccbel-modal-body').css({
            height: 'auto',
            "max-height": '90%'
        });
        modal.find('.wccbel-modal-content').css({
            height: 'auto',
            "max-height": '92%'
        });
    }, 400);
}

function wccbelOpenModal(targetId) {
    let modal = jQuery(targetId);
    modal.fadeIn();
    modal.find(".wccbel-modal-box").fadeIn();
    jQuery("#wccbel-last-modal-opened").val(jQuery(this).attr("data-target"));

    // set height for modal body
    setTimeout(function () {
        wccbelFixModalHeight(modal);
    }, 150)
}

function wccbelReInitColorPicker() {
    if (jQuery('.wccbel-color-picker').length > 0) {
        jQuery('.wccbel-color-picker').wpColorPicker();
    }
    if (jQuery('.wccbel-color-picker-field').length > 0) {
        jQuery('.wccbel-color-picker-field').wpColorPicker();
    }
}

function wccbelReInitDatePicker() {
    if (jQuery.fn.datetimepicker) {
        jQuery('.wccbel-datepicker-with-dash').datetimepicker('destroy');
        jQuery('.wccbel-datepicker').datetimepicker('destroy');
        jQuery('.wccbel-timepicker').datetimepicker('destroy');
        jQuery('.wccbel-datetimepicker').datetimepicker('destroy');

        jQuery('.wccbel-datepicker').datetimepicker({
            timepicker: false,
            format: 'Y/m/d',
            scrollMonth: false,
            scrollInput: false
        });

        jQuery('.wccbel-datepicker-with-dash').datetimepicker({
            timepicker: false,
            format: 'Y-m-d',
            scrollMonth: false,
            scrollInput: false
        });

        jQuery('.wccbel-timepicker').datetimepicker({
            datepicker: false,
            format: 'H:i',
            scrollMonth: false,
            scrollInput: false
        });

        jQuery('.wccbel-datetimepicker').datetimepicker({
            format: 'Y/m/d H:i',
            scrollMonth: false,
            scrollInput: false
        });
    }

}

function wccbelPaginationLoadingStart() {
    jQuery('.wccbel-pagination-loading').show();
}

function wccbelPaginationLoadingEnd() {
    jQuery('.wccbel-pagination-loading').hide();
}

function wccbelLoadingStart() {
    jQuery('#wccbel-loading').removeClass('wccbel-loading-error').removeClass('wccbel-loading-success').text('Loading ...').slideDown(300);
}

function wccbelLoadingSuccess(message = 'Success !') {
    jQuery('#wccbel-loading').removeClass('wccbel-loading-error').addClass('wccbel-loading-success').text(message).delay(1500).slideUp(200);
}

function wccbelLoadingError(message = 'Error !') {
    jQuery('#wccbel-loading').removeClass('wccbel-loading-success').addClass('wccbel-loading-error').text(message).delay(1500).slideUp(200);
}

function wccbelSetColorPickerTitle() {
    jQuery('.wccbel-column-manager-right-item .wp-picker-container').each(function () {
        let title = jQuery(this).find('.wccbel-column-manager-color-field input').attr('title');
        jQuery(this).attr('title', title);
        wccbelSetTipsyTooltip();
    });
}

function wccbelFilterFormClose() {
    if (jQuery('#wccbel-filter-form-content').attr('data-visibility') === 'visible') {
        jQuery('.wccbel-filter-form-icon').addClass('wccbel-icon-chevron-down').removeClass('wccbel-icon-chevron-up');
        jQuery('#wccbel-filter-form-content').slideUp(200).attr('data-visibility', 'hidden');
    }
}

function wccbelSetTipsyTooltip() {
    jQuery('[title]').tipsy({
        html: true,
        arrowWidth: 10, //arrow css border-width * 2, default is 5 * 2
        attr: 'data-tipsy',
        cls: null,
        duration: 150,
        offset: 7,
        position: 'top-center',
        trigger: 'hover',
        onShow: null,
        onHide: null
    });
}

function wccbelCheckUndoRedoStatus(reverted, history) {
    if (reverted) {
        wccbelEnableRedo();
    } else {
        wccbelDisableRedo();
    }
    if (history) {
        wccbelEnableUndo();
    } else {
        wccbelDisableUndo();
    }
}

function wccbelDisableUndo() {
    jQuery('#wccbel-bulk-edit-undo').attr('disabled', 'disabled');
}

function wccbelEnableUndo() {
    jQuery('#wccbel-bulk-edit-undo').prop('disabled', false);
}

function wccbelDisableRedo() {
    jQuery('#wccbel-bulk-edit-redo').attr('disabled', 'disabled');
}

function wccbelEnableRedo() {
    jQuery('#wccbel-bulk-edit-redo').prop('disabled', false);
}

function wccbelHideSelectionTools() {
    jQuery('.wccbel-bulk-edit-form-selection-tools').hide();
    jQuery('#wccbel-bulk-edit-trash-restore').hide();
}

function wccbelShowSelectionTools() {
    jQuery('.wccbel-bulk-edit-form-selection-tools').show();
    jQuery('#wccbel-bulk-edit-trash-restore').show();
}

function wccbelSetColorPickerTitle() {
    jQuery('.wccbel-column-manager-right-item .wp-picker-container').each(function () {
        let title = jQuery(this).find('.wccbel-column-manager-color-field input').attr('title');
        jQuery(this).attr('title', title);
        wccbelSetTipsyTooltip();
    });
}

function wccbelColumnManagerAddField(fieldName, fieldLabel, action) {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'html',
        data: {
            action: 'wccbel_column_manager_add_field',
            nonce: WCCBEL_DATA.ajax_nonce,
            field_name: fieldName,
            field_label: fieldLabel,
            field_action: action
        },
        success: function (response) {
            jQuery('.wccbel-box-loading').hide();
            jQuery('.wccbel-column-manager-added-fields[data-action=' + action + '] .items').append(response);
            fieldName.forEach(function (name) {
                jQuery('.wccbel-column-manager-available-fields[data-action=' + action + '] input:checkbox[data-name=' + name + ']').prop('checked', false).closest('li').attr('data-added', 'true').hide();
            });
            wccbelReInitColorPicker();
            jQuery('.wccbel-column-manager-check-all-fields-btn[data-action=' + action + '] input:checkbox').prop('checked', false);
            jQuery('.wccbel-column-manager-check-all-fields-btn[data-action=' + action + '] span').removeClass('selected').text('Select All');
            setTimeout(function () {
                wccbelSetColorPickerTitle();
            }, 250);
        },
        error: function () {
        }
    })
}

function wccbelAddMetaKeysManual(meta_key_name) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'html',
        data: {
            action: 'wccbel_add_meta_keys_manual',
            nonce: WCCBEL_DATA.ajax_nonce,
            meta_key_name: meta_key_name,
        },
        success: function (response) {
            jQuery('#wccbel-meta-fields-items').append(response);
            wccbelLoadingSuccess();
        },
        error: function () {
            wccbelLoadingError();
        }
    })
}

function wccbelAddACFMetaField(field_name, field_label, field_type) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'html',
        data: {
            action: 'wccbel_add_acf_meta_field',
            nonce: WCCBEL_DATA.ajax_nonce,
            field_name: field_name,
            field_label: field_label,
            field_type: field_type
        },
        success: function (response) {
            jQuery('#wccbel-meta-fields-items').append(response);
            wccbelLoadingSuccess();
        },
        error: function () {
            wccbelLoadingError();
        }
    })
}

function wccbelCheckFilterFormChanges() {
    let isChanged = false;
    jQuery('#wccbel-filter-form-content [data-field="value"]').each(function () {
        if (jQuery.isArray(jQuery(this).val())) {
            if (jQuery(this).val().length > 0) {
                isChanged = true;
            }
        } else {
            if (jQuery(this).val()) {
                isChanged = true;
            }
        }
    });
    jQuery('#wccbel-filter-form-content [data-field="from"]').each(function () {
        if (jQuery(this).val()) {
            isChanged = true;
        }
    });
    jQuery('#wccbel-filter-form-content [data-field="to"]').each(function () {
        if (jQuery(this).val()) {
            isChanged = true;
        }
    });

    jQuery('#filter-form-changed').val(isChanged);

    if (isChanged === true) {
        jQuery('#wccbel-bulk-edit-reset-filter').show();
    } else {
        jQuery('.wccbel-top-nav-status-filter button[data-status="all"]').addClass('active');
    }
}

function wccbelGetCheckedItem() {
    let itemIds;
    let itemsChecked = jQuery("input.wccbel-check-item:checkbox:checked");
    if (itemsChecked.length > 0) {
        itemIds = itemsChecked.map(function (i) {
            return jQuery(this).val();
        }).get();
    }

    return itemIds;
}

function wccbelGetTableCount(countPerPage, currentPage, total) {
    currentPage = (currentPage) ? currentPage : 1;
    let showingTo = parseInt(currentPage * countPerPage);
    let showingFrom = (total > 0) ? parseInt(showingTo - countPerPage) + 1 : 0;
    showingTo = (showingTo < total) ? showingTo : total;
    return "Showing " + showingFrom + " to " + showingTo + " of " + total + " entries";
}

function wccbelGetCouponsChecked() {
    let couponIds = [];
    let couponsChecked = jQuery("input.wccbel-check-item:checkbox:checked");
    if (couponsChecked.length > 0) {
        couponIds = couponsChecked.map(function (i) {
            return jQuery(this).val();
        }).get();
    }
    return couponIds;
}

function wccbelReloadCoupons(edited_ids = [], current_page = wccbelGetCurrentPage()) {
    let data = wccbelGetCurrentFilterData();
    wccbelCouponsFilter(data, 'pro_search', edited_ids, current_page);
}

function wccbelCouponsFilter(data, action, edited_ids = null, page = wccbelGetCurrentPage()) {
    // clear selected coupons in export tab
    jQuery('#wccbel-export-items-selected').html('');

    if (action === 'pagination') {
        wccbelPaginationLoadingStart();
    } else {
        wccbelLoadingStart();
    }
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_coupons_filter',
            nonce: WCCBEL_DATA.ajax_nonce,
            filter_data: data,
            current_page: page,
            search_action: action,
        },
        success: function (response) {
            if (response.success) {
                wccbelLoadingSuccess();
                wccbelSetCouponsList(response, edited_ids)
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelSetStatusFilter(statusFilters) {
    jQuery('.wccbel-top-nav-status-filter').html(statusFilters);

    jQuery('.wccbel-bulk-edit-status-filter-item').removeClass('active');
    let statusFilter = (jQuery('#wccbel-filter-form-coupon-status').val() && jQuery('#wccbel-filter-form-coupon-status').val() != '') ? jQuery('#wccbel-filter-form-coupon-status').val() : 'all';
    if (jQuery.isArray(statusFilter)) {
        statusFilter.forEach(function (val) {
            jQuery('.wccbel-bulk-edit-status-filter-item[data-status="' + val + '"]').addClass('active');
        });
    } else {
        let activeItem = jQuery('.wccbel-bulk-edit-status-filter-item[data-status="' + statusFilter + '"]');
        activeItem.addClass('active');
        jQuery('.wccbel-status-filter-selected-name').text(' - ' + activeItem.text())
    }

}

function wccbelSetCouponsList(response, edited_ids = null) {
    jQuery('#wccbel-items-table').html(response.coupons_list);
    jQuery('.wccbel-items-pagination').html(response.pagination);
    jQuery('.wccbel-items-count').html(wccbelGetTableCount(jQuery('#wccbel-quick-per-page').val(), wccbelGetCurrentPage(), response.coupons_count));
    wccbelSetStatusFilter(response.status_filters);

    wccbelReInitDatePicker();
    wccbelReInitColorPicker();

    if (edited_ids && edited_ids.length > 0) {
        jQuery('tr').removeClass('wccbel-item-edited');
        edited_ids.forEach(function (couponID) {
            jQuery('tr[data-item-id=' + couponID + ']').addClass('wccbel-item-edited');
            jQuery('input[value=' + couponID + ']').prop('checked', true);
        });
        wccbelShowSelectionTools();
    } else {
        wccbelHideSelectionTools();
    }

    wccbelSetTipsyTooltip();
    setTimeout(function () {
        let maxHeightScrollWrapper = jQuery('.scroll-wrapper > .scroll-content').css('max-height');
        jQuery('.scroll-wrapper > .scroll-content').css({
            'max-height': (parseInt(maxHeightScrollWrapper) + 5)
        });

        let actionColumn = jQuery('td.wccbel-action-column');
        if (actionColumn.length > 0) {
            actionColumn.each(function () {
                jQuery(this).css({
                    "min-width": (parseInt(jQuery(this).find('a').length) * 45)
                })
            });
        }
    }, 500);
}

function wccbelGetCouponData(couponID) {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_get_coupon_data',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_id: couponID
        },
        success: function (response) {
            if (response.success) {
                wccbelSetCouponDataBulkEditForm(response.coupon_data);
            } else {

            }
        },
        error: function () {

        }
    });
}

function wccbelDeleteCoupon(couponIDs, deleteType) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_delete_coupons',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_ids: couponIDs,
            delete_type: deleteType,
            filter_data: wccbelGetCurrentFilterData(),
        },
        success: function (response) {
            if (response.success) {
                wccbelReloadCoupons();
                wccbelHideSelectionTools();
                wccbelCheckUndoRedoStatus(response.reverted, response.history_items);
                jQuery('.wccbel-history-items tbody').html(response.history_items);
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelRestoreCoupon(couponIds) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_untrash_coupons',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_ids: couponIds,
        },
        success: function (response) {
            if (response.success) {
                wccbelReloadCoupons();
                wccbelHideSelectionTools();
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelEmptyTrash() {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_empty_trash',
            nonce: WCCBEL_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                wccbelReloadCoupons();
                wccbelHideSelectionTools();
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelDuplicateCoupon(couponIDs, duplicateNumber) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_duplicate_coupon',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_ids: couponIDs,
            duplicate_number: duplicateNumber
        },
        success: function (response) {
            if (response.success) {
                wccbelReloadCoupons([], wccbelGetCurrentPage());
                wccbelCloseModal();
                wccbelHideSelectionTools();
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelCreateNewCoupon(count = 1) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_create_new_coupon',
            nonce: WCCBEL_DATA.ajax_nonce,
            count: count
        },
        success: function (response) {
            if (response.success) {
                wccbelReloadCoupons(response.coupon_ids, 1);
                wccbelCloseModal();
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelSaveColumnProfile(presetKey, items, type) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_save_column_profile',
            nonce: WCCBEL_DATA.ajax_nonce,
            preset_key: presetKey,
            items: items,
            type: type
        },
        success: function (response) {
            if (response.success) {
                wccbelLoadingSuccess();
                location.href = location.href.replace(location.hash, "");
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelLoadFilterProfile(presetKey) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_load_filter_profile',
            nonce: WCCBEL_DATA.ajax_nonce,
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                wccbelResetFilterForm();
                setTimeout(function () {
                    setFilterValues(response);
                }, 500);
                wccbelLoadingSuccess();
                wccbelSetCouponsList(response);
                wccbelCloseModal();
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelDeleteFilterProfile(presetKey) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_delete_filter_profile',
            nonce: WCCBEL_DATA.ajax_nonce,
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                wccbelLoadingSuccess();
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelFilterProfileChangeUseAlways(presetKey) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_filter_profile_change_use_always',
            nonce: WCCBEL_DATA.ajax_nonce,
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                wccbelLoadingSuccess();
            } else {
                wccbelLoadingError()
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelGetCurrentFilterData() {
    return (jQuery('#wccbel-quick-search-text').val()) ? wccbelGetQuickSearchData() : wccbelGetProSearchData()
}

function wccbelResetQuickSearchForm() {
    jQuery('.wccbel-top-nav-filters-search input').val('');
    jQuery('.wccbel-top-nav-filters-search select').prop('selectedIndex', 0);
    jQuery('#wccbel-quick-search-reset').hide();
    jQuery('#wccbel-quick-search-field').trigger('change');
}

function wccbelResetFilterForm() {
    jQuery('#wccbel-float-side-modal-filter input').val('');
    jQuery('#wccbel-float-side-modal-filter textarea').val('');
    jQuery('#wccbel-float-side-modal-filter select').prop('selectedIndex', 0).change();
    jQuery('#wccbel-float-side-modal-filter .wccbel-select2').val(null).trigger('change');
    jQuery('#wccbel-float-side-modal-filter .wccbel-select2-products').html('').val(null).trigger('change');
    jQuery('#wccbel-float-side-modal-filter .wccbel-select2-categories').html('').val(null).trigger('change');
    jQuery('#wccbel-float-side-modal-filter .wccbel-select2-products').html('').val(null).trigger('change');
    jQuery('#wccbel-float-side-modal-filter .wccbel-select2-categories').html('').val(null).trigger('change');
    jQuery('.wccbel-bulk-edit-status-filter-item').removeClass('active');
    jQuery('.wccbel-bulk-edit-status-filter-item[data-status="all"]').addClass('active');
}

function wccbelResetFilters() {
    wccbelResetFilterForm();
    wccbelResetQuickSearchForm();

    jQuery(".wccbel-filter-profiles-items tr").removeClass("wccbel-filter-profile-loaded");
    jQuery('input.wccbel-filter-profile-use-always-item[value="default"]').prop("checked", true).closest("tr");
    jQuery("#wccbel-bulk-edit-reset-filter").hide();
    jQuery('#wccbel-bulk-edit-reset-filter').hide();

    jQuery('.wccbel-reset-filter-form').closest('li').hide();

    setTimeout(function () {
        if (window.location.search !== '?page=wccbel') {
            wccbelClearFilterDataWithRedirect();
        } else {
            let data = wccbelGetCurrentFilterData();
            wccbelFilterProfileChangeUseAlways("default");
            wccbelCouponsFilter(data, "pro_search");
        }
    }, 250);
}

function wccbelCheckResetFilterButton() {
    if (jQuery('#wccbel-bulk-edit-filter-tabs-contents [data-field="value"]').length > 0) {
        jQuery('#wccbel-bulk-edit-filter-tabs-contents [data-field="value"]').each(function () {
            if (jQuery(this).val() != '') {
                jQuery('.wccbel-reset-filter-form').closest('li').show();
                return true;
            }
        });
    }
}

function wccbelClearFilterDataWithRedirect() {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_clear_filter_data',
            nonce: WCCBEL_DATA.ajax_nonce,
        },
        success: function (response) {
            window.location.search = '?page=wccbel';
        },
        error: function () {
        }
    });
}

function wccbelChangeCountPerPage(countPerPage) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_change_count_per_page',
            nonce: WCCBEL_DATA.ajax_nonce,
            count_per_page: countPerPage,
        },
        success: function (response) {
            if (response.success) {
                wccbelReloadCoupons([], 1);
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function changedTabs(item) {
    let change = false;
    let tab = jQuery('nav.wccbel-tabs-navbar a[data-content=' + item.closest('.wccbel-tab-content-item').attr('data-content') + ']');
    item.closest('.wccbel-tab-content-item').find('[data-field=operator]').each(function () {
        if (jQuery(this).val() === 'text_remove_duplicate') {
            change = true;
            return false;
        }
    });
    item.closest('.wccbel-tab-content-item').find('[data-field=value]').each(function () {
        if (jQuery(this).val()) {
            change = true;
            return false;
        }
    });
    if (change === true) {
        tab.addClass('wccbel-tab-changed');
    } else {
        tab.removeClass('wccbel-tab-changed');
    }
}

function wccbelGetQuickSearchData() {
    return {
        search_type: 'quick_search',
        quick_search_text: jQuery('#wccbel-quick-search-text').val(),
        quick_search_field: jQuery('#wccbel-quick-search-field').val(),
        quick_search_operator: jQuery('#wccbel-quick-search-operator').val(),
    };
}

function wccbelSortByColumn(columnName, sortType) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_sort_by_column',
            nonce: WCCBEL_DATA.ajax_nonce,
            filter_data: wccbelGetCurrentFilterData(),
            column_name: columnName,
            sort_type: sortType,
        },
        success: function (response) {
            if (response.success) {
                wccbelLoadingSuccess();
                wccbelSetCouponsList(response)
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelColumnManagerFieldsGetForEdit(presetKey) {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_column_manager_get_fields_for_edit',
            nonce: WCCBEL_DATA.ajax_nonce,
            preset_key: presetKey
        },
        success: function (response) {
            jQuery('#wccbel-modal-column-manager-edit-preset .wccbel-box-loading').hide();
            jQuery('.wccbel-column-manager-added-fields[data-action=edit] .items').html(response.html);
            setTimeout(function () {
                wccbelSetColorPickerTitle();
            }, 250);
            jQuery('.wccbel-column-manager-available-fields[data-action=edit] li').each(function () {
                if (jQuery.inArray(jQuery(this).attr('data-name'), response.fields.split(',')) !== -1) {
                    jQuery(this).attr('data-added', 'true').hide();
                } else {
                    jQuery(this).attr('data-added', 'false').show();
                }
            });
            jQuery('.wccbel-color-picker').wpColorPicker();
        },
    })
}

function wccbelAddMetaKeysByCouponID(couponID) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'html',
        data: {
            action: 'wccbel_add_meta_keys_by_coupon_id',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_id: couponID,
        },
        success: function (response) {
            jQuery('#wccbel-meta-fields-items').append(response);
            wccbelLoadingSuccess();
        },
        error: function () {
            wccbelLoadingError();
        }
    })
}

function wccbelHistoryUndo() {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_history_undo',
            nonce: WCCBEL_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                wccbelCheckUndoRedoStatus(response.reverted, response.history_items);
                jQuery('.wccbel-history-items tbody').html(response.history_items);
                wccbelReloadCoupons(response.coupon_ids);
            }
        },
        error: function () {

        }
    });
}

function wccbelHistoryRedo() {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_history_redo',
            nonce: WCCBEL_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                wccbelCheckUndoRedoStatus(response.reverted, response.history_items);
                jQuery('.wccbel-history-items tbody').html(response.history_items);
                wccbelReloadCoupons(response.coupon_ids);
            }
        },
        error: function () {

        }
    });
}

function wccbelHistoryFilter(filters = null) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_history_filter',
            nonce: WCCBEL_DATA.ajax_nonce,
            filters: filters,
        },
        success: function (response) {
            if (response.success) {
                wccbelLoadingSuccess();
                if (response.history_items) {
                    jQuery('.wccbel-history-items tbody').html(response.history_items);
                } else {
                    jQuery('.wccbel-history-items tbody').html("<td colspan='4'><span>Not Found!</span></td>");
                }
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelHistoryChangePage(page = 1, filters = null) {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_history_change_page',
            nonce: WCCBEL_DATA.ajax_nonce,
            page: page,
            filters: filters,
        },
        success: function (response) {
            if (response.success) {
                wccbelLoadingSuccess();
                if (response.history_items) {
                    jQuery('.wccbel-history-items tbody').html(response.history_items);
                    jQuery('.wccbel-history-pagination-container').html(response.history_pagination);
                } else {
                    jQuery('.wccbel-history-items tbody').html("<td colspan='4'><span>" + wccbelTranslate.notFound + "</span></td>");
                }
                jQuery('.wccbel-history-pagination-loading').hide();
            } else {
                jQuery('.wccbel-history-pagination-loading').hide();
            }
        },
        error: function () {
            jQuery('.wccbel-history-pagination-loading').hide();
        }
    });
}

function wccbelGetCurrentPage() {
    return jQuery('.wccbel-top-nav-filters .wccbel-top-nav-filters-paginate button.current').attr('data-index');
}

function wccbelGetDefaultFilterProfileCoupons() {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_get_default_filter_profile_coupons',
            nonce: WCCBEL_DATA.ajax_nonce,
        },
        success: function (response) {
            if (response.success) {
                setTimeout(function () {
                    setFilterValues(response);
                }, 500);
                wccbelSetCouponsList(response)
            }
        },
        error: function () {
        }
    });
}

function setFilterValues(response) {
    let filterData = response.filter_data;
    if (filterData) {
        jQuery('.wccbel-top-nav-status-filter button').removeClass('active');
        jQuery.each(filterData, function (key, values) {
            if (values instanceof Object) {
                if (values.operator) {
                    jQuery('#wccbel-float-side-modal-filter .wccbel-form-group[data-name="' + key + '"]').find('[data-field=operator]').val(values.operator).change();
                }
                if (values.value) {
                    switch (key) {
                        case 'post_status':
                            if (values.value[0]) {
                                jQuery('.wccbel-top-nav-status-filter button[data-status="' + values.value[0] + '"]').addClass('active');
                                jQuery('#wccbel-filter-form-coupon-status').val(values.value).change();
                            } else {
                                jQuery('.wccbel-top-nav-status-filter button[data-status="all"]').addClass('active');
                            }
                            break;
                        case 'product_ids':
                            if (values.value.length > 0) {
                                values.value.forEach(function (key) {
                                    if (response.product_ids[key]) {
                                        jQuery('#wccbel-filter-form-coupon-products').append("<option value='" + key + "' selected='selected'>" + response.product_ids[key] + "</option>");
                                    }
                                });
                            }
                            break;
                        case 'exclude_product_ids':
                            if (values.value.length > 0) {
                                values.value.forEach(function (key) {
                                    if (response.exclude_product_ids[key]) {
                                        jQuery('#wccbel-filter-form-coupon-exclude-products').append("<option value='" + key + "' selected='selected'>" + response.exclude_product_ids[key] + "</option>");
                                    }
                                });
                            }
                            break;
                        case 'product_categories':
                            if (values.value.length > 0) {
                                values.value.forEach(function (key) {
                                    if (response.product_categories[key]) {
                                        jQuery('#wccbel-filter-form-coupon-product-categories').append("<option value='" + key + "' selected='selected'>" + response.product_categories[key] + "</option>");
                                    }
                                });
                            }
                            break;
                        case 'exclude_product_categories':
                            if (values.value.length > 0) {
                                values.value.forEach(function (key) {
                                    if (response.exclude_product_categories[key]) {
                                        jQuery('#wccbel-filter-form-coupon-exclude-product-categories').append("<option value='" + key + "' selected='selected'>" + response.exclude_product_categories[key] + "</option>");
                                    }
                                });
                            }
                            break;
                        default:
                            jQuery('#wccbel-float-side-modal-filter .wccbel-form-group[data-name=' + key + ']').find('[data-field="value"]').val(values.value).change();
                    }
                }
                if (values.from) {
                    jQuery('#wccbel-float-side-modal-filter .wccbel-form-group[data-name=' + key + ']').find('[data-field="from"]').val(values.from).change();
                }
                if (values.to) {
                    jQuery('#wccbel-float-side-modal-filter .wccbel-form-group[data-name=' + key + ']').find('[data-field="to"]').val(values.to);
                }
            } else {
                jQuery('#wccbel-float-side-modal-filter .wccbel-form-group[data-name=' + key + ']').find('[data-field="value"]').val(values);
            }
        });

        wccbelCheckFilterFormChanges();
        wccbelCheckResetFilterButton();
    }
}

function wccbelSaveFilterPreset(data, presetName) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_save_filter_preset',
            nonce: WCCBEL_DATA.ajax_nonce,
            filter_data: data,
            preset_name: presetName
        },
        success: function (response) {
            if (response.success) {
                wccbelLoadingSuccess();
                jQuery('#wccbel-float-side-modal-filter-profiles').find('tbody').append(response.new_item);
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelResetBulkEditForm() {
    jQuery('#wccbel-float-side-modal-bulk-edit input').val('').change();
    jQuery('#wccbel-float-side-modal-bulk-edit select').prop('selectedIndex', 0).change();
    jQuery('#wccbel-float-side-modal-bulk-edit textarea').val('');
    jQuery('#wccbel-float-side-modal-bulk-edit .wccbel-select2').val(null).trigger('change');
    jQuery('#wccbel-float-side-modal-bulk-edit .wccbel-select2-products').html('').val(null).trigger('change');
    jQuery('#wccbel-float-side-modal-bulk-edit .wccbel-select2-categories').html('').val(null).trigger('change');
    jQuery('#wccbel-float-side-modal-bulk-edit .wccbel-select2-products').html('').val(null).trigger('change');
    jQuery('#wccbel-float-side-modal-bulk-edit .wccbel-select2-categories').html('').val(null).trigger('change');
}

function wccbelGetProSearchData() {
    let data;
    let custom_fields = [];
    let j = 0;
    jQuery('.wccbel-tab-content-item[data-content=filter_custom_fields] .wccbel-form-group').each(function () {
        if (jQuery(this).find('input').length === 2) {
            let dataFieldType;
            let values = jQuery(this).find('input').map(function () {
                dataFieldType = jQuery(this).attr('data-field-type');
                if (jQuery(this).val()) {
                    return jQuery(this).val()
                }
            }).get();
            custom_fields[j++] = {
                type: 'from-to-' + dataFieldType,
                taxonomy: jQuery(this).attr('data-name'),
                value: values
            }
        } else if (jQuery(this).find('input[data-field=value]').length === 1) {
            if (jQuery(this).find('input[data-field=value]').val() != null) {
                custom_fields[j++] = {
                    type: 'text',
                    taxonomy: jQuery(this).attr('data-name'),
                    operator: jQuery(this).find('select[data-field=operator]').val(),
                    value: jQuery(this).find('input[data-field=value]').val()
                }
            }
        } else if (jQuery(this).find('select[data-field=value]').length === 1) {
            if (jQuery(this).find('select[data-field=value]').val() != null) {
                custom_fields[j++] = {
                    type: 'select',
                    taxonomy: jQuery(this).attr('data-name'),
                    value: jQuery(this).find('select[data-field=value]').val()
                }
            }
        }
    });

    data = {
        search_type: 'pro_search',
        coupon_ids: {
            operator: jQuery('#wccbel-filter-form-coupon-ids-operator').val(),
            value: jQuery('#wccbel-filter-form-coupon-ids').val(),
        },
        coupon_code: {
            operator: jQuery('#wccbel-filter-form-coupon-title-operator').val(),
            value: jQuery('#wccbel-filter-form-coupon-title').val(),
        },
        description: {
            operator: jQuery('#wccbel-filter-form-coupon-description-operator').val(),
            value: jQuery('#wccbel-filter-form-coupon-description').val(),
        },
        date_created: {
            from: jQuery('#wccbel-filter-form-coupon-date-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-date-to').val(),
        },
        post_modified: {
            from: jQuery('#wccbel-filter-form-coupon-modified-date-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-modified-date-to').val(),
        },
        post_status: {
            value: jQuery('#wccbel-filter-form-coupon-status').val(),
        },
        discount_type: {
            value: jQuery('#wccbel-filter-form-coupon-discount-type').val(),
        },
        coupon_amount: {
            from: jQuery('#wccbel-filter-form-coupon-amount-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-amount-to').val(),
        },
        free_shipping: {
            value: jQuery('#wccbel-filter-form-coupon-free-shipping').val(),
        },
        individual_use: {
            value: jQuery('#wccbel-filter-form-coupon-individual-use').val(),
        },
        exclude_sale_items: {
            value: jQuery('#wccbel-filter-form-coupon-exclude-sale-items').val(),
        },
        date_expires: {
            from: jQuery('#wccbel-filter-form-coupon-expiry-date-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-expiry-date-to').val(),
        },
        minimum_amount: {
            from: jQuery('#wccbel-filter-form-coupon-minimum-amount-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-minimum-amount-to').val(),
        },
        maximum_amount: {
            from: jQuery('#wccbel-filter-form-coupon-maximum-amount-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-maximum-amount-to').val(),
        },
        product_ids: {
            operator: jQuery('#wccbel-filter-form-coupon-products-operator').val(),
            value: jQuery('#wccbel-filter-form-coupon-products').val(),
        },
        exclude_product_ids: {
            operator: jQuery('#wccbel-filter-form-coupon-exclude-products-operator').val(),
            value: jQuery('#wccbel-filter-form-coupon-exclude-products').val(),
        },
        product_categories: {
            operator: jQuery('#wccbel-filter-form-coupon-product-categories-operator').val(),
            value: jQuery('#wccbel-filter-form-coupon-product-categories').val(),
        },
        exclude_product_categories: {
            operator: jQuery('#wccbel-filter-form-coupon-exclude-product-categories-operator').val(),
            value: jQuery('#wccbel-filter-form-coupon-exclude-product-categories').val(),
        },
        customer_email: {
            operator: jQuery('#wccbel-filter-form-coupon-customer-email-operator').val(),
            value: jQuery('#wccbel-filter-form-coupon-customer-email').val(),
        },
        usage_limit: {
            from: jQuery('#wccbel-filter-form-coupon-usage-limit-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-usage-limit-to').val(),
        },
        limit_usage_to_x_items: {
            from: jQuery('#wccbel-filter-form-coupon-limit-usage-to-x-items-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-limit-usage-to-x-items-to').val(),
        },
        usage_limit_per_user: {
            from: jQuery('#wccbel-filter-form-coupon-usage-limit-per-user-from').val(),
            to: jQuery('#wccbel-filter-form-coupon-usage-limit-per-user-to').val(),
        },
        custom_fields: custom_fields,
    };
    return data;
}

function wccbelCouponEdit(couponIds, couponData) {
    wccbelLoadingStart();
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_coupon_edit',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_ids: couponIds,
            coupon_data: couponData,
            current_page: wccbelGetCurrentPage(),
            filter_data: wccbelGetCurrentFilterData()
        },
        success: function (response) {
            if (response.success) {
                wccbelReloadRows(response.coupons, response.coupon_statuses);
                wccbelSetStatusFilter(response.status_filters);
                wccbelCheckUndoRedoStatus(response.reverted, response.history_items);
                jQuery('.wccbel-history-items tbody').html(response.history_items);
                wccbelReInitDatePicker();
                wccbelReInitColorPicker();
                let wccbelTextEditors = jQuery('input[name="wccbel-editors[]"]');
                if (wccbelTextEditors.length > 0) {
                    wccbelTextEditors.each(function () {
                        tinymce.execCommand('mceRemoveEditor', false, jQuery(this).val());
                        tinymce.execCommand('mceAddEditor', false, jQuery(this).val());
                    })
                }
                wccbelLoadingSuccess();
            } else {
                wccbelLoadingError();
            }
        },
        error: function () {
            wccbelLoadingError();
        }
    });
}

function wccbelReloadRows(coupons, statuses) {
    let currentStatus = (jQuery('#wccbel-filter-form-coupon-status').val());
    jQuery('tr').removeClass('wccbel-item-edited').find('.wccbel-check-item').prop('checked', false);
    if (Object.keys(coupons).length > 0) {
        jQuery.each(coupons, function (key, val) {
            if (statuses[key] === currentStatus || (currentStatus.length < 1 && statuses[key] !== 'trash')) {
                jQuery('#wccbel-items-list').find('tr[data-item-id="' + key + '"]').replaceWith(val);
                jQuery('tr[data-item-id="' + key + '"]').addClass('wccbel-item-edited').find('.wccbel-check-item').prop('checked', true);
            } else {
                jQuery('#wccbel-items-list').find('tr[data-item-id="' + key + '"]').remove();
            }
        });
        wccbelShowSelectionTools();
    } else {
        wccbelHideSelectionTools();
    }
}

function wccbelClearInputs(element) {
    element.find('input').val('');
    element.find('textarea').val('');
    element.find('select option:first').prop('selected', true);
}

function wccbelGetProducts() {
    let query;
    jQuery(".wccbel-select2-products").select2({
        ajax: {
            type: "post",
            delay: 200,
            url: WCCBEL_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                query = {
                    action: "wccbel_get_products",
                    nonce: WCCBEL_DATA.ajax_nonce,
                    search: params.term,
                };
                return query;
            },
        },
        minimumInputLength: 1
    });
}

function wccbelGetCategories() {
    let query;
    jQuery(".wccbel-select2-categories").select2({
        ajax: {
            type: "post",
            delay: 200,
            url: WCCBEL_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                query = {
                    action: "wccbel_get_categories",
                    nonce: WCCBEL_DATA.ajax_nonce,
                    search: params.term,
                };
                return query;
            },
        },
        minimumInputLength: 1
    });
}

function wccbelGetCouponProducts(couponId, field) {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_get_coupon_products',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_id: couponId,
            field: field
        },
        success: function (response) {
            if (response.success && response.coupon_products) {
                jQuery.each(response.coupon_products, function (id) {
                    jQuery('#wccbel-modal-products-items').append('<option value="' + id + '" selected>' + response.coupon_products[id] + '</option>');
                })
            }
        },
        error: function () {
        }
    });
}

function wccbelGetCouponCategories(couponId, field) {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_get_coupon_categories',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_id: couponId,
            field: field
        },
        success: function (response) {
            if (response.success && response.product_categories) {
                jQuery.each(response.product_categories, function (id) {
                    jQuery('#wccbel-modal-categories-items').append('<option value="' + id + '" selected>' + response.product_categories[id] + '</option>');
                })
            }
        },
        error: function () {
        }
    });
}

function wccbelGetCouponUsedIn(couponCode) {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_get_coupon_used_in',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_code: couponCode,
        },
        success: function (response) {
            if (response.success && response.orders.length !== 0) {
                jQuery.each(response.orders, function (id) {
                    jQuery('#wccbel-modal-coupon-used-in-items').append('<li><a target="_blank" href="' + response.orders[id] + '">Order #' + id + '</a></li>');
                })
            } else {
                jQuery('#wccbel-modal-coupon-used-in-items').append('<span class="wccbel-red-text">This coupon has not been used in any order.</span>');
            }
        },
        error: function () {
        }
    });
}

function wccbelGetCouponUsedBy(couponId) {
    jQuery.ajax({
        url: WCCBEL_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wccbel_get_coupon_used_by',
            nonce: WCCBEL_DATA.ajax_nonce,
            coupon_id: couponId,
        },
        success: function (response) {
            if (response.users) {
                jQuery.each(response.users, function (id) {
                    jQuery('#wccbel-modal-coupon-used-by-items').append('<li><a target="_blank" href="' + response.users[id].link + '">' + response.users[id].name + '</a></li>');
                })
            } else {
                jQuery('#wccbel-modal-coupon-used-by-items').append('<span class="wccbel-red-text">This coupon has not been used by any user.</span>');
            }
        },
        error: function () {
        }
    });
}