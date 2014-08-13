$(function () {

    //Init select2 dropdowns
    initMerchants($('#merchant_id'));
    initCountry($('#country_id'));
    initRegion($('#region_id'));
    initState($('#state_id'));
    initCity($('#city_id'));

    //Method checking for current form unique store
    $.validator.addMethod("checkCFUniqueStore", function (value, element) {
        var Tresult = true;
        $('.data-container .store_name').not(element).each(function () {
            var v = $.trim($(this).val());
            if (value === v) {
                Tresult = false;
            }
        });
        return this.optional(element) || Tresult;
    }, "Already exist.");

    $.validator.addMethod("checkLat", function (value, element) {

        return this.optional(element) || /^-?([0-8]?[0-9]|90)\.[0-9]{1,6}$/i.test(value);

    }, "Please specify correct latitude.");

    $.validator.addMethod("checkLong", function (value, element) {

        return this.optional(element) || /^-?((1?[0-7]?|[0-9]?)[0-9]|180)\.[0-9]{1,6}$/i.test(value);

    }, "Please specify correct longitude.");

    //Validate store name remote
    $.validator.addMethod("validateStore", function (value, element) {
        var resultT = false;
        var ele = this.optional(element);
        var $parent = $(element).parents('form');
        var $id = $parent.find('.pks-id:input');
        var param = [];
        param.push({name: 'store_name', value: value});
        if ($id.length !== 0 && $id.val() !== '') {
            param.push({name: 'id', value: $id.val()});
        }
        $.ajax({
            type: 'GET',
            async: false,
            data: param,
            url: site_url + 'admin/store/store_management/validate_store',
            success: function (r) {
                r = $.trim(r);
                if (r === 'success') {
                    resultT = true;
                }
            }
        });
        return ele || resultT;
    }, "Store name already exist.");

    var $init_form = $('form.form-store-management');
    validateForm($init_form);

    $('.data-container').delegate('.data-save-n-add', 'click', function (e) {
        var $form = $(this).parents('form');
        if (validateFilters() && $form.valid()) {
            ajaxLoaderOverlay($(".admin-box"));
            var filterData = '';
            $.each($('.filters-container').find('select'), function () {
                filterData += '&' + $(this).attr('name') + '=' + $(this).val();
            });
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                data: $form.serialize() + filterData,
                success: function (r) {
                    var $json = $.parseJSON(r);
                    if ($json.hasOwnProperty('success')) {
                        //copying data
                        var $parent = $form.parents('.data-container-row');
                        var html = '<div class="data-container-row">';
                        html += $parent.html();
                        html += '<div>';
                        $parent.after(html);
                        var $next = $parent.next();

                        //replicate fields
                        var val1 = $parent.find('.store-name').val();
                        $next.find('.store-name').val(val1);
                        $parent.find('.store-name-text').html(val1);
                        $parent.find('.store-name').hide();

                        var val2 = $parent.find('.store-address').val();
                        $next.find('.store-address').val(val2);
                        $parent.find('.store-address-text').html(val2);
                        $parent.find('.store-address').hide();

                        var val3 = $parent.find('.store-zipcode').val();
                        $next.find('.store-zipcode').val(val3);
                        $parent.find('.store-zipcode-text').html(val3);
                        $parent.find('.store-zipcode').hide();

                        var $lat = $parent.find('.store-lat');
                        $lat.val($json.success.lat);
                        $next.find('.store-lat').val($json.success.lat);
                        $parent.find('.store-lat-text').html($json.success.lat);
                        $parent.find('.store-lat').hide();

                        var $lng = $parent.find('.store-lng');
                        $lng.val($json.success.lng);
                        $next.find('.store-lng').val($json.success.lng);
                        $parent.find('.store-lng-text').html($json.success.lng);
                        $parent.find('.store-lng').hide();

                        //set returned ids
                        $form.find('.pks-id').val($json.success.store_id);
                        $form.find('.mer-id').val($json.success.merchant_id);
                        $form.find('.ct-id').val($json.success.city_id);

                        //replicate controlls                        
                        $form.find('.data-save-n-add').hide();
                        $form.find('.data-edit').show();
                        $form.find('.data-delete').show();

                        //re-validate form
                        validateForm($next.find('form'));
                        removeOverlay();
                    }
                }
            });
        }
    });
    $('.data-container').delegate('.data-save', 'click', function (e) {
        var $form = $(this).parents('form');
        var $success = $form.find('.data-success-outer');
        if (validateFilters() && $form.valid()) {
            ajaxLoaderOverlay($(".admin-box"));
            $success.show().html('...');
            var filterData = '';
            $.each($('.filters-container').find('select'), function () {
                filterData += '&' + $(this).attr('name') + '=' + $(this).val();
            });
            $.ajax({
                type: 'POST',
                url: $form.attr('action'),
                data: $form.serialize() + filterData,
                success: function (r) {
                    var $json = $.parseJSON(r);
                    if ($json.hasOwnProperty('success')) {
                        $success.html('<i class="icon-ok">&nbsp;</i>');

                        //copying data
                        var store_name = $form.find('.store-name').val();
                        $form.find('.store-name-text').html(store_name).show();
                        $form.find('.store-name').hide();

                        var store_addr = $form.find('.store-address').val();
                        $form.find('.store-address-text').html(store_addr).show();
                        $form.find('.store-address').hide();

                        var store_zip = $form.find('.store-zipcode').val();
                        $form.find('.store-zipcode-text').html(store_zip).show();
                        $form.find('.store-zipcode').hide();

                        $form.find('.store-lat').val($json.success.lat).hide();
                        $form.find('.store-lat-text').html($json.success.lat).show();

                        $form.find('.store-lng').val($json.success.lng).hide();
                        $form.find('.store-lng-text').html($json.success.lng).show();

                        $form.find('.mer-id').val($json.success.merchant_id);
                        $form.find('.ct-id').val($json.success.city_id);

                        $form.find('.data-save').hide();
                        $form.find('.data-reset').hide();
                        $form.find('.data-edit').show();
                        $form.find('.data-delete').show();

                        removeOverlay();

                        setTimeout(function () {
                            $success.hide();
                        }, 5000);
                    } else {
                        $success.show().html('');
                    }
                }
            });
        }
    });

    $('.data-container').delegate('.data-edit', 'click', function (e) {
        $.each($('.data-reset:not(:last)'), function () {
            $(this).trigger('click');
        });

        //Reset drop down...
        $("#merchant_id").select2("val", "");
        $("#region_id").select2("val", "");
        $("#state_id").select2("val", "");
        $("#region_id").trigger("change");

        var $form = $(this).parents('form');
        /*var storeId = getStoreByBeacon($form.find('.pks-id').val());
         if (storeId) {
         $("#store").val(storeId).trigger("change");
         }*/


        $form.find('.store-name-text').hide();
        $form.find('.store-name').show();

        $form.find('.store-address-text').hide();
        $form.find('.store-address').show();

        $form.find('.store-zipcode-text').hide();
        $form.find('.store-zipcode').show();

        $form.find('.store-lat-text').hide();
        $form.find('.store-lat').show();

        $form.find('.store-lng-text').hide();
        $form.find('.store-lng').show();

        var merchant_id = $form.find('.mer-id').val();
        $('#merchant_id').val(merchant_id).trigger('change');

        var ct_id = $form.find('.ct-id').val();
//        $('#country_id').val('').trigger('change');
        $('#country_id').trigger('change');
        $('#city_id').val(ct_id).trigger('change');

        $form.find('.data-save').show();
        $form.find('.data-reset').show();
        $form.find('.data-edit').hide();
        $form.find('.data-delete').hide();

    });

    $('.data-container').delegate('.data-reset', 'click', function (e) {
        var $form = $(this).parents('form');

        $form.find('.store-name-text').show();
        $form.find('.store-name').val($form.find('.store-name-text').html()).hide();

        $form.find('.store-address-text').show();
        $form.find('.store-address').val($form.find('.store-address-text').html()).hide();

        $form.find('.store-zipcode-text').show();
        $form.find('.store-zipcode').val($form.find('.store-zipcode-text').html()).hide();

        $form.find('.store-lat-text').show();
        $form.find('.store-lat').val($form.find('.store-lat-text').html()).hide();

        $form.find('.store-lng-text').show();
        $form.find('.store-lng').val($form.find('.store-lng-text').html()).hide();

        $form.find('.data-save').hide();
        $form.find('.data-reset').hide();
        $form.find('.data-edit').show();
        $form.find('.data-delete').show();

        $form.find('dd').each(function () {
            $(this).remove();
        });
    });

    $('.data-container').delegate('.data-delete', 'click', function (e) {
        var $form = $(this).parents('form');
        if (confirm('Are you sure you want to delete this record?')) {
            ajaxLoaderOverlay($(".admin-box"));
            $.ajax({
                type: 'GET',
                url: site_url + 'admin/store/store_management/delete',
                data: {id: $form.find('.pks-id').val()},
                success: function (r) {
                    var $json = $.parseJSON(r);
                    if ($json.hasOwnProperty('success')) {
                        var $parent = $form.parents('.data-container-row');
                        $parent.hide('slow', function () {
                            $(this).remove();
                        });
                    }
                    removeOverlay();
                }
            });
        }
    });


    //Setting select2 dropdowns
    /*setRegions($('#region_id'), {}, "initRegion");
     setStates($('#state_id'), {}, "initState");
     setCities($('#city_id'), {}, "initCity");*/

    var country_id = "";
    if ($("#country_id_topsss").val() != "all") {
        country_id = $("#country_id_topsss").val();
    }

    //Events on select2 dropdowns
    /*$('#country_id').on('change', function () {
     //        var $filter = {country_id: $(this).val()};
     var $filter = {country_id: country_id};
     $(this).next('span.filter-error').html('');
     setRegions($('#region_id'), $filter, "initRegion");
     });

     $('#region_id').on('change', function () {
     //        var $filter = {country_id: $('#country_id').val(), region_id: $(this).val()};
     var $filter = {country_id: country_id, region_id: $(this).val()};
     $(this).next('span.filter-error').html('');
     setStates($('#state_id'), $filter, "initState");
     });

     $('#state_id').on('change', function () {
     //        var $filter = {country_id: $('#country_id').val(), region_id: $('#region_id').val(), state_id: $(this).val()};
     var $filter = {country_id: country_id, region_id: $('#region_id').val(), state_id: $(this).val()};
     $(this).next('span.filter-error').html('');
     setCities($('#city_id'), $filter, "initCity");
     });*/

    var $obj2 = new Object();

    $obj2.region = new Object();
    $obj2.region.obj = $("#region_id");
    $obj2.region.callback = "initRegion";

    $obj2.state = new Object();
    $obj2.state.obj = $("#state_id");
    $obj2.state.callback = "initState";

    $obj2.city = new Object();
    $obj2.city.obj = $("#city_id");
    $obj2.city.callback = "initCity";

    $('#region_id, #state_id ').on('change', function (e) {

        switch ($(e.currentTarget).attr('id')) {
            case 'region_id' :
                $("#state_id").select2("val", "");
                $("#city_id").select2("val", "");
                break;
            case 'state_id' :
                $("#city_id").select2("val", "");
                break;
        }
        var $filter2 = new Object();
        $filter2.country_id = country_id;
        $filter2.region_id = $("#region_id").val();
        $filter2.state_id = $("#state_id").val();

        lazy_load_dropdowns($filter2, $obj2);

    });

    var $filter = new Object();
    $filter.country_id = country_id;
    $filter.region_id = $("#region_id").val();
    $filter.state_id = $("#state_id").val();

    lazy_load_dropdowns($filter, $obj2);


    $('#city_id, #merchant_id').on('change', function () {
        $(this).next('span.filter-error').html('');
    });

    $('#country_id').trigger('change');

    $('body').delegate('.toggle-all2', 'click', function () {
        var $div = $(this).closest('div');
        var $checkboxes = $div.find('input[type=checkbox]');
        $checkboxes.prop("checked", !$checkboxes.prop("checked"));
    });

});

function validateForm($form) {
    $form.validate({
        errorPlacement: function (error, element) {
            if (element.attr("name") == "category_ids[]") {

                // do whatever you need to place label where you want

                /*// just an example
                 error.appendTo($("#somePlace"));

                 // just another example
                 error.insertAfter($("#someOtherPlace"));*/

                // yet another example
                $("#store-errors").html(error);

            } else {

                // the default error placement for the rest
                error.insertAfter(element);

            }
        },
        rules: {
            store_name: {
                required: true,
                maxlength: 100,
                checkCFUniqueStore: true,
                validateStore: true

            },
            address: {
                required: true,
                maxlength: 500
            },
            zip: {
                required: true,
                number: true,
                maxlength: 8
            },
            'category_ids[]': {
                required: true

            },
            lat: {
                checkLat: true,
                maxlength: 7
            },
            lng: {
                checkLong: true,
                maxlength: 7
            }

        },
        onkeyup: false
    });
}

function validateFilters() {
    var flag = true;
    $.each($('.filters-container').find('select.filter-required'), function () {
        if ($(this).val() === '') {
            flag = false;
            $(this).next('span.filter-error').html('This field is required');
        }
    });
    return flag;
}

function initMerchants($obj) {
    $obj.select2({
        placeholder: 'Select Merchant',
        width: 170,
        allowClear: true
    });
}

function initCountry($obj) {
    $obj.select2({
        placeholder: 'Select Country',
        width: 170,
        allowClear: true
    });
}

function initRegion($obj) {
    $obj.select2({
        placeholder: 'Select Region',
        width: 170,
        allowClear: true
    });
}

function initState($obj) {
    $obj.select2({
        placeholder: 'Select State',
        width: 170,
        allowClear: true
    });
}

function initCity($obj) {
    $obj.select2({
        placeholder: 'Select City',
        width: 170,
        allowClear: true
    });
}