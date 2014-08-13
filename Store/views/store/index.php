<?php if (!isset($ajax)) : ?>
    <style>
        #table_content input:not([type="checkbox"]), textarea {
            width: 90px;
        }

        #page {
            width: 50px !important;
        }
    </style>
<div class="admin-box">
<h3>Store</h3>
<?php
$attributes = array(
    'name' => 'admin_listing_form',
    'id' => 'admin_listing_form',
    'method' => 'POST'
);
?>
<?php echo form_open($this->uri->uri_string() . '/index', $attributes); ?>
<div class='grid-filters managements'>
    <input type="hidden" value="" name="sortby" id="sortby" class="reset-input">
    <input type="hidden" value="" name="order" id="order" class="reset-input">
    <input type="hidden" value="" name="action" id="action" class="reset-input">
    <table>
        <tr>
            <td>
                <div class="select_box_22"><label>Search</label>

                    <div class="select_box">
                        <input id="search-field" type='text' class='search-field reset-input align-fix1'
                               rel_id='serach_filed1'
                               name='search[store_name]'/>
                        <select class='search-field-dropdown reset-dropdown category-dp' rel='serach_filed1'
                                style="position:absolute; right:0px; top:0px;">
                            <option value='store_name'>Store Name</option>
                        </select></div>
                </div>
            </td>
            <?php
            if ($is_merchant == FALSE) :
                ?>
                <td>
                    <label for="mer_label">Merchant</label>
                    <select id="mer_label" name="search_merchant_id" class="reset-dropdown select2-dropdown">
                        <option value="">Select Merchant</option>
                        <?php if (isset($merchant_list) && !empty($merchant_list)): ?>
                            <?php foreach ($merchant_list as $merchant): ?>
                                <?php
                                $selected3 = "";
                                if ($this->input->get_post('search_merchant_id') == $merchant['id']) {
                                    $selected3 = "selected='selected'";
                                }
                                ?>
                                <option
                                    <?php echo $selected3; ?>
                                    value="<?php echo $merchant['id'] ?>"><?php echo $merchant['display_name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>&nbsp;&nbsp;
                </td>
            <?php endif; ?>
            <td>
                <!--                        <select id="search_cc_id" name="search_country_id" class="reset-dropdown select2-dropdown"  >
                            <?php if (isset($country_list) && !empty($country_list)): ?>
                                <?php foreach ($country_list as $country): ?>
                                    <option <?php echo $this->session->userdata('default_country_id') == $country['country_id'] ? 'selected="selected"' : '' ?> value="<?php echo $country['country_id'] ?>"><?php echo $country['country_name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>-->
                <input id="country_id" type="hidden" name="search_country_id"
                       value="<?php echo $this->session->userdata('default_country_id') ? $this->session->userdata('default_country_id') : $country[0]['country_id'] ?>"/>
                <label for="region_id">Region</label>
                <select name="search_region_id" id="region_id" class="reset-dropdown select2-dropdown">
                    <option value="">Select Region</option>
                    <?php if (isset($region_list) && !empty($region_list)): ?>
                        <?php foreach ($region_list as $region): ?>
                            <?php
                            $selected = "";
                            if ($this->input->get_post('search_region_id') == $region['region_id']) {
                                $selected = "selected='selected'";
                            }
                            ?>
                            <option
                                <?php echo $selected; ?>
                                value="<?php echo $region['region_id'] ?>"><?php echo $region['region_name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>&nbsp;&nbsp;
            </td>
            <td>
                <label for="state_id">State</label>
                <select name="search_state_id" id="state_id" class="reset-dropdown select2-dropdown">
                    <option value="">Select State</option>
                    <?php if (isset($state_list) && !empty($state_list)): ?>
                        <?php foreach ($state_list as $state): ?>
                            <?php
                            $selected2 = "";
                            if ($this->input->get_post('search_state_id') == $state['state_id']) {
                                $selected2 = "selected='selected'";
                            }
                            ?>
                            <option
                                <?php echo $selected2; ?>
                                value="<?php echo $state['state_id'] ?>"><?php echo $state['state_name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>&nbsp;&nbsp;
            </td>
            <td>
                <label for="city_id">City</label>
                <select name="search_city_id" id="city_id" class="reset-dropdown select2-dropdown">
                    <option value="">Select City</option>
                    <?php if (isset($city_list) && !empty($city_list)): ?>
                        <?php foreach ($city_list as $city): ?>
                            <option
                                value="<?php echo $city['city_id'] ?>"><?php echo $city['city_name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>&nbsp;
            </td>
            <td><label>Status</label>
                <select id="category" name='category' class='category-dropdown reset-dropdown category-dp'
                        style="">
                    <option value='active'>Active</option>
                    <option value='inactive'>Inactive</option>
                    <option value='all'>All</option>
                    <option value='newest'>Newest</option>
                    <option value='oldest'>Oldest</option>
                </select>&nbsp;&nbsp;
            </td>

            <td><label for="date_range_filter2">Date Range</label>
                <?php
                $options = array(
                    "" => "Select Days",
                    "1" => "7 Days",
                    "2" => "15 Days",
                    "3" => "1 Month",
                    "4" => "Custom"
                );
                echo form_dropdown3('date_range_filter', $options, set_value('date_range_filter', $this->input->get_post("date_range_filter")), 'Country Name' . lang('bf_form_label_required'), " id='date_range_filter2' class='te-selects select2-dropdown' ");                ?>
            </td>
            <td>
                <?php
                $style = "style='display:none;'";
                if ($this->input->get_post("date_range_filter") == 4) {
                    $style = "style='display:block;'";
                }
                ?>
                <div id="to_from_div" class="top_bar_date_sel" <?php echo $style; ?> >
                    &nbsp;&nbsp;<input type="text" class="te-date reset-input" id="from_date2" name="from_date"
                                       value="<?php echo set_value('from_date', $this->input->get_post("from_date")) ?>"
                                       placeholder="From Date" readonly/>
                    <input type="text" class="te-date reset-input" id="to_date2" name="to_date"
                           value="<?php echo set_value('to_date', $this->input->get_post("to_date")) ?>"
                           placeholder="To Date" readonly/>
                </div>
            </td>
            <td>&nbsp;
                <button type='button' class='btn submit-filters topposition_botton' title='Find' data-original-title=''>
                    <img src="<?php echo Template::theme_url("images/find.png"); ?>"/>
                </button>
                &nbsp;
            </td>
            <td>
                <button type='button' class='btn reset-filters topposition_botton' title='Reset' data-original-title=''>
                    <img src="<?php echo Template::theme_url("images/Reset.png"); ?>"/>
                </button>
                &nbsp;</td>
            <td>
                <button type='button' class='btn export topposition_botton' title='Export' data-original-title=''><img
                        src="<?php echo Template::theme_url("images/export.png"); ?>"/>
                </button>
                &nbsp;
            </td>
            <td>
                <?php if ($this->auth->has_permission('Store_Management.Store.Delete')) : ?>
                    <button type='button' class='btn delete-selected btn-danger topposition_botton' title='Delete'
                            data-original-title=''>
                        <img src="<?php echo Template::theme_url("images/delete.png"); ?>"/>
                    </button>
                <?php endif; ?>
            </td>


        </tr>


    </table>
</div>


<div id="table_content">
<?php endif; ?>
<?php /*if (isset($ajax) || isset($d_link)) : */ ?>
<?php $this->load->model("store_management/store_category_model"); ?>
<table class="table table-striped">
<thead>
<tr>
    <?php if ($this->auth->has_permission('Store_Management.Store.Delete') && isset($records) && is_array($records) && count($records)) : ?>
        <th class="column-check"><input class="check-all" type="checkbox"/></th>
    <?php endif; ?>

    <th>Store Name<?php /*echo lang('bf_form_label_required') */ ?>
        <i class="icon-arrow-up sort" rel="asc" for="store_name"></i>
        <i class="icon-arrow-down sort" rel="desc" for="store_name"></i>
    </th>
    <th>Address<?php /*echo lang('bf_form_label_required') */ ?></th>
    <th>Zipcode<?php /*echo lang('bf_form_label_required') */ ?></th>
    <th style="display: none;">Country
        <i class="icon-arrow-up sort" rel="asc" for="country_name"></i>
        <i class="icon-arrow-down sort" rel="desc" for="country_name"></i>
    </th>
    <th>Region
        <i class="icon-arrow-up sort" rel="asc" for="region_name"></i>
        <i class="icon-arrow-down sort" rel="desc" for="region_name"></i>
    </th>
    <th>State
        <i class="icon-arrow-up sort" rel="asc" for="state_name"></i>
        <i class="icon-arrow-down sort" rel="desc" for="state_name"></i>
    </th>
    <th>City<?php /*echo lang('bf_form_label_required') */ ?>
        <i class="icon-arrow-up sort" rel="asc" for="city_name"></i>
        <i class="icon-arrow-down sort" rel="desc" for="city_name"></i>
    </th>
    <th>Latitude</th>
    <th>Longitude</th>
    <th>Status</th>
    <?php if ($this->auth->has_permission('Store_Management.Store.Delete') && isset($records) && is_array($records) && count($records)) : ?>
        <th>Actions</th>
    <?php endif; ?>
    <?php if ($is_merchant == FALSE) : ?>
        <th>Category</th>
    <?php endif; ?>
</tr>
</thead>
<tbody>
<?php if (isset($records) && is_array($records) && count($records)) : ?>
    <?php $counter = 1; //dump($records); ?>
    <?php foreach ($records as $record) : ?>
        <tr>
            <?php if ($this->auth->has_permission('Store_Management.Store.Delete')) : ?>
                <td><input type="checkbox" name="checked[]" value="<?php echo $record->store_id ?>"/></td>
            <?php endif; ?>

            <td>
                <div class="store-name1">
                    <input id="store-id<?php echo $counter ?>" type="hidden" name="id" maxlength="255"
                           value="<?php echo $record->store_id ?>" class="pks-id"/>
                    <input id="marchant-id<?php echo $counter ?>" type="hidden" name="merchant_id"
                           value="<?php echo $record->merchant_id ?>" class="merchant-id"/>
                    <a class="fancybox"
                       href="<?php echo site_url('admin/store/store_management/find_store_offers?store_id=' . $record->store_id) ?>">
                        <span class="store-name-text"><?php echo $record->store_name ?></span>
                    </a>
                    <span class="required" style="display: none;">*</span>
                    <input type="text" name="store_name" maxlength="255"
                           value="<?php echo $record->store_name ?>" class="store-name" style="display: none"/>
                    <?php if ($is_merchant == FALSE) : ?>

                        <a class="fancybox"
                           href="<?php echo site_url('admin/store/store_management/get_multilang_fields?id=' . $record->store_id . '&code=SN') ?>"
                           style="display: block"><img src="<?php echo base_url('assets/images/language.png') ?>"
                                                       title=""/>
                        </a>
                    <?php endif; ?>
                </div>
            </td>
            <td>
                <div class="store-address1">
                    <span class="required" style="display: none;">*</span>
                    <textarea id="store-address<?php echo $counter ?>" name="address" class="store-address"
                              style="display: none"><?php echo $record->address; ?></textarea>
                    <span class="store-address-text"><?php echo $record->address; ?></span>
                    <?php if ($is_merchant == FALSE) : ?>
                        <p class="language_flag">
                            <a class="fancybox"
                               href="<?php echo site_url('admin/store/store_management/get_multilang_fields?id=' . $record->store_id . '&code=SA') ?>"
                               style="display: block"><img src="<?php echo base_url('assets/images/language.png') ?>"
                                                           title=""/>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </td>
            <td>
                <div class="store-zipcode2">
                    <span class="required" style="display: none;">*</span>
                    <input id="store-zipcode<?php echo $counter ?>" type="text" name="zip" maxlength="8"
                           value="<?php echo $record->zipcode; ?>" class="store-zipcode" style="display: none"/>
                    <span class="store-zipcode-text"><?php echo $record->zipcode; ?></span>
                </div>
            </td>
            <td style="display: none;">
                <span class="store-country-text"><?php echo $record->country_name; ?></span>
                <input type="hidden" name="cur_country_id" value="<?php echo $record->country_id ?>"
                       class="store-res-country-id"/>
                <select name="country_id" id="store-country-id<?php echo $counter ?>" class="store-country"
                        style="display: none;">
                    <?php if (isset($country_list) && !empty($country_list)): ?>
                        <?php foreach ($country_list as $country): ?>
                            <option
                                value="<?php echo $country['country_id'] ?>" <?php echo $country['country_id'] == $record->country_id ? 'selected' : '' ?> ><?php echo $country['country_name']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <span class="store-region-text"><?php echo $record->region_name; ?></span>
                <input type="hidden" name="cur_region_id" value="<?php echo $record->region_id ?>"
                       class="store-res-region-id"/>
                <select name="region_id" id="store-region-id<?php echo $counter ?>" class="store-region"
                        style="display: none;">
                    <?php if (isset($region_list) && !empty($region_list)): ?>
                        <?php foreach ($region_list as $region): ?>
                            <option
                                value="<?php echo $region['region_id'] ?>" <?php echo $region['region_id'] == $record->region_id ? 'selected' : '' ?> ><?php echo $region['region_name']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <span class="store-state-text"><?php echo $record->state_name ?></span>
                <input type="hidden" name="cur_state_id" value="<?php echo $record->state_id ?>"
                       class="store-res-state-id"/>
                <select name="state_id" id="store-state-id<?php echo $counter ?>" class="store-state"
                        style="display: none;">
                    <?php if (isset($state_list) && !empty($state_list)): ?>
                        <?php foreach ($state_list as $state): ?>
                            <option
                                value="<?php echo $state['state_id'] ?>" <?php echo $state['state_id'] == $record->state_id ? 'selected' : '' ?> ><?php echo $state['state_name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>
            <td>
                <span class="store-city-text"><?php echo $record->city_name; ?></span>
                <span class="required" style="display: none;">*</span>
                <input type="hidden" name="cur_city_id" value="<?php echo $record->city_id ?>"
                       class="store-res-city-id"/>
                <select name="city_id" id="store-city-id<?php echo $counter ?>" class="store-city"
                        style="display: none;">
                    <?php if (isset($city_list) && !empty($city_list)): ?>
                        <?php foreach ($city_list as $city): ?>
                            <option
                                value="<?php echo $city['city_id'] ?>" <?php echo $city['city_id'] == $record->city_id ? 'selected' : '' ?> ><?php echo $city['city_name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>&nbsp;&nbsp;
            </td>
            <td>
                <div class="store-lat1">
                    <input type="text" name="lat" value="<?php echo round($record->latitude, 4) ?>"
                           class="store-lat"
                           style="display: none;" maxlength="7"/>
                    <span class="store-lat-text"><?php echo round($record->latitude, 4) ?></span>
                </div>
            </td>
            <td>
                <div class="store-lng1">
                    <input type="text" name="lng" value="<?php echo round($record->longitude, 4) ?>"
                           class="store-lng"
                           style="display: none;" maxlength="7"/>
                    <span class="store-lng-text"><?php echo round($record->longitude, 4) ?></span>
                </div>
            </td>
            <?php
            if ($record->status == 1) {
                $status = "Active";
                $btn_status = "Inactive";
                $class = "success";
            } else {
                $status = "Inactive";
                $btn_status = "Active";
                $class = "warning";
            }
            ?>

            <?php if ($is_merchant == FALSE) : ?>
                <td><span style="cursor: pointer;" class="label label-<?php echo $class; ?> toggle_status"
                          rel_id="<?php echo $record->store_id; ?>"><?php echo $status ?></span></td>
            <?php else : ?>
                <td><span class="label label-<?php echo $class; ?>"><?php echo $status ?></span></td>
            <?php endif; ?>


            <?php if ($is_merchant == FALSE) : ?>
                <td>
                    <?php if ($this->auth->has_permission('Store_Management.Store.Edit')) : ?>
                        <?php //echo anchor(SITE_AREA . '/store/store_management/edit/' . $record->store_id, '<i class="icon-pencil">&nbsp;</i>') ?>&nbsp;&nbsp;
                        <a href="javascript:void(0);" class="data-edit" title="Edit"><i class="icon-pencil">
                                &nbsp;</i></a>
                        <a href="javascript:void(0);" class="data-save" title="Save" style="display: none;"><i
                                class="icon-ok-sign">&nbsp;</i></a>
                        <a href="javascript:void(0);" class="data-reset" title="Reset" style="display: none;"><i
                                class="icon-remove-circle">&nbsp;</i></a>
                    <?php endif; ?>
                    <?php if ($this->auth->has_permission('Store_Management.Store.Delete') && isset($records) && is_array($records) && count($records)) : ?>
                        <span style="cursor: pointer;" class="delete" data-original-title="" title="Delete"
                              rel="<?php echo $record->store_id; ?>"><i class="icon-remove">
                                &nbsp;</i></span>&nbsp;&nbsp;
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if ($is_merchant == FALSE) : ?>
                <td>
                    <?php
                    $store_id = $record->store_id;
                    $categories = $this->store_category_model->find_all_by(array("store_id" => $store_id));
                    //                    dump($categories);
                    if ($categories) {
                        ?>
                        <a class="fancybox"
                           href="<?php echo site_url('admin/store/store_management/edit_store_category_view/?store_id=' . $record->store_id) ?>"
                           title="">Edit Category</a>
                    <?php
                    } else {
                        ?>
                        <a class="fancybox"
                           href="<?php echo site_url('admin/store/store_management/add_store_category_view/?store_id=' . $record->store_id) ?>"
                           title="">Add Category</a>
                    <?php
                    }
                    ?>
                </td>
            <?php endif; ?>

        </tr>
        <?php $counter++; ?>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="11">No records found that match your selection.</td>
    </tr>
<?php endif; ?>
</tbody>
</table>
<?php
if (isset($pagination)) {
    echo $pagination;
}
?>
<?php /*endif; */ ?>

<?php if (!isset($ajax)) : ?>
</div>
<?php echo form_close(); ?>
</div>
<?php endif; ?>

<script>
    $(function () {
        initCountry($('.store-country'));
        initRegion($('.store-region'));
        initState($('.store-state'));
        initCity($('.store-city'));
    })
</script>

<?php if (!isset($ajax)) : ?>
    <script>

    $(function () {

        //Init select2 dropdowns

        $('.fancybox').fancybox({
            type: 'ajax',
            href: $(this).attr('href'),
            titleShow: false
        });

        $("#from_date2").datepicker({dateFormat: "dd-mm-yy"});
        $("#to_date2").datepicker({dateFormat: "dd-mm-yy"});

        /*$("#from_date2").datepicker();
         $("#to_date2").datepicker();*/

        $("#date_range_filter2").on("change", function () {
            if ($(this).val() == "4") {
                $("#to_from_div").show();
            } else {
                $("#to_from_div").hide();
            }
        });


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

        //Validate store name remote
        $.validator.addMethod("validateStore", function (value, element) {
            if (!$(element).is(":visible")) {
                return true;
            }
            var resultT = false;
            var ele = this.optional(element);
            var $parent = $(element).parents('tr');
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

        $.validator.addMethod("checkLat", function (value, element) {

            return this.optional(element) || /^-?([0-8]?[0-9]|90)\.[0-9]{1,6}$/i.test(value);

        }, "Please specify correct latitude.");

        $.validator.addMethod("checkLong", function (value, element) {

            return this.optional(element) || /^-?((1?[0-7]?|[0-9]?)[0-9]|180)\.[0-9]{1,6}$/i.test(value);

        }, "Please specify correct longitude.");

        var $init_form = $('#admin_listing_form');
        /*validateForm($init_form);*/

        $('.admin-box').delegate('.data-save', 'click', function (e) {
            var $form = $(this).parents('tr');
            var $success = $form.find('.data-success-outer');

            var obj = $form.find('input, select, textarea');
            var b = $("form");
            $.each(obj, function (key, value) {
//                    console.log($(value));
                var $ele = $(value);
//                    console.log($ele.parent());
//                    console.log($("form"));
                var a = $ele.parent();

                if ($ele.hasClass("store-name")) {
                    if (!(a.is(b))) {
                        $ele.wrap("<form name='form_store_name' id='form_store_name'></form>");
                        $("#form_store_name").validate({
                            rules: {
                                store_name: {
                                    required: true,
                                    maxlength: 100,
                                    checkCFUniqueStore: true,
                                    validateStore: true
                                }
                            },
                            onkeyup: false
                        });
                    }
                }
                if ($ele.hasClass("store-address")) {
                    if (!(a.is(b))) {
                        $ele.wrap("<form name='form_address' id='form_address'></form>");
                        $("#form_address").validate({
                            rules: {
                                address: {
                                    required: true,
                                    maxlength: 500
                                }
                            }
                        });
                    }
                }
                if ($ele.hasClass("store-zipcode")) {
                    if (!(a.is(b))) {
                        $ele.wrap("<form name='form_zipcode' id='form_zipcode'></form>");
                        $("#form_zipcode").validate({
                            rules: {
                                zip: {
                                    required: true,
                                    maxlength: 8,
                                    number: true
                                }
                            }
                        });
                    }
                }
                if ($ele.hasClass("store-city")) {
                    if (!(a.is(b))) {
                        $ele.wrap("<form name='form_city_id' id='form_city_id'></form>");
                        $("#form_city_id").validate({
                            rules: {
                                city_id: {
                                    required: true
                                }
                            }
                        });
                    }
                }

                if ($ele.hasClass("store-lat")) {
                    if (!(a.is(b))) {
                        $ele.wrap("<form name='form_lat' id='form_lat'></form>");
                        $("#form_lat").validate({
                            rules: {
                                lat: {
                                    checkLat: true,
                                    maxlength: 7
                                }
                            }
                        });
                    }
                }

                if ($ele.hasClass("store-lng")) {
                    if (!(a.is(b))) {
                        $ele.wrap("<form name='form_lng' id='form_lng'></form>");
                        $("#form_lng").validate({
                            rules: {
                                lng: {
                                    checkLong: true,
                                    maxlength: 7
                                }
                            }
                        });
                    }
                }

            });

            if ($('#form_store_name').valid() & $('#form_address').valid() & $('#form_zipcode').valid() & $('#form_city_id').valid() & $('#form_lat').valid() & $('#form_lng').valid()) {

                ajaxLoaderOverlay($(".admin-box"));

                var param = [];
                param.push({name: 'ci_csrf_token', value: $('[name=ci_csrf_token]').val()});
                $.each($form.find('input'), function () {
                    param.push({name: $(this).attr('name'), value: $(this).val()});
                });
                $.each($form.find('select'), function () {
                    param.push({name: $(this).attr('name'), value: $(this).val()});
                });
                $.each($form.find('textarea'), function () {
                    param.push({name: $(this).attr('name'), value: $(this).val()});
                });
                param.push({name: "from", value: "index"});
                $.ajax({
                    type: 'POST',
                    url: site_url + 'admin/store/store_management/create',
                    data: param,
                    success: function (r) {
                        var $json = $.parseJSON(r);
                        if ($json.hasOwnProperty('success')) {
                            //                                $success.html('<i class="icon-ok">&nbsp;</i>');
                            //
                            //                                //copying data
                            //                                var store_name = $form.find('.store-name').val();
                            //                                $form.find('.store-name-text').html(store_name).show();
                            //                                $form.find('.store-name').hide();
                            //
                            //                                var store_addr = $form.find('.store-address').val();
                            //                                $form.find('.store-address-text').html(store_addr).show();
                            //                                $form.find('.store-address').hide();
                            //
                            //                                var store_zip = $form.find('.store-zipcode').val();
                            //                                $form.find('.store-zipcode-text').html(store_zip).show();
                            //                                $form.find('.store-zipcode').hide();
                            //
                            //                                $form.find('.store-lat').val($json.success.lat).hide();
                            //                                $form.find('.store-lat-text').html($json.success.lat).show();
                            //
                            //                                $form.find('.store-lng').val($json.success.lng).hide();
                            //                                $form.find('.store-lng-text').html($json.success.lng).show();
                            //
                            //                                $form.find('.store-res-country-id').val($form.find('.store-country').val());
                            //                                $form.find('.store-res-region-id').val($form.find('.store-region').val());
                            //                                $form.find('.store-res-state-id').val($form.find('.store-state').val());
                            //                                $form.find('.store-res-city-id').val($form.find('.store-city').val());
                            //
                            //                                $form.find('.data-save').hide();
                            //                                $form.find('.data-reset').hide();
                            //                                $form.find('.data-edit').show();
                            //                                $form.find('.data-delete').show();
                            //
                            //                                setTimeout(function() {
                            //                                    $success.hide();
                            //                                }, 5000);
//                            $form.find('.required').css('display', 'none');
                            $('.submit-filters').trigger('click');
                        } else {
                            $success.show().html('');
                        }
                    }
                });
            } else {
                removeOverlay();
            }

            /*            if ($init_form.valid()) {
             var param = [];
             param.push({name: 'ci_csrf_token', value: $('[name=ci_csrf_token]').val()});
             $.each($form.find('input'), function () {
             param.push({name: $(this).attr('name'), value: $(this).val()});
             });
             $.each($form.find('select'), function () {
             param.push({name: $(this).attr('name'), value: $(this).val()});
             });
             $.each($form.find('textarea'), function () {
             param.push({name: $(this).attr('name'), value: $(this).val()});
             });
             $.ajax({
             type: 'POST',
             url: site_url + 'admin/store/store_management/create',
             data: param,
             success: function (r) {
             var $json = $.parseJSON(r);
             if ($json.hasOwnProperty('success')) {
             //                                $success.html('<i class="icon-ok">&nbsp;</i>');
             //
             //                                //copying data
             //                                var store_name = $form.find('.store-name').val();
             //                                $form.find('.store-name-text').html(store_name).show();
             //                                $form.find('.store-name').hide();
             //
             //                                var store_addr = $form.find('.store-address').val();
             //                                $form.find('.store-address-text').html(store_addr).show();
             //                                $form.find('.store-address').hide();
             //
             //                                var store_zip = $form.find('.store-zipcode').val();
             //                                $form.find('.store-zipcode-text').html(store_zip).show();
             //                                $form.find('.store-zipcode').hide();
             //
             //                                $form.find('.store-lat').val($json.success.lat).hide();
             //                                $form.find('.store-lat-text').html($json.success.lat).show();
             //
             //                                $form.find('.store-lng').val($json.success.lng).hide();
             //                                $form.find('.store-lng-text').html($json.success.lng).show();
             //
             //                                $form.find('.store-res-country-id').val($form.find('.store-country').val());
             //                                $form.find('.store-res-region-id').val($form.find('.store-region').val());
             //                                $form.find('.store-res-state-id').val($form.find('.store-state').val());
             //                                $form.find('.store-res-city-id').val($form.find('.store-city').val());
             //
             //                                $form.find('.data-save').hide();
             //                                $form.find('.data-reset').hide();
             //                                $form.find('.data-edit').show();
             //                                $form.find('.data-delete').show();
             //
             //                                setTimeout(function() {
             //                                    $success.hide();
             //                                }, 5000);
             //                            $form.find('.required').css('display', 'none');
             $('.submit-filters').trigger('click');
             } else {
             $success.show().html('');
             }
             }
             });
             } else {
             removeOverlay();
             }*/
        });

        $('.admin-box').delegate('.data-edit', 'click', function (e) {
            $.each($('.data-reset'), function () {
                $(this).trigger('click');
//                resetRow($(this).parents('tr'), false);
            });
            var $form = $(this).parents('tr');
            $form.find('.required').css('display', 'inline');

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

            $form.find('.store-country-text').hide();
            $form.find('.store-country').show();

            $form.find('.store-region-text').hide();
            $form.find('.store-region').show();

            $form.find('.store-state-text').hide();
            $form.find('.store-state').show();

            $form.find('.store-city-text').hide();
            $form.find('.store-city').show();

            $form.find('.data-save').show();
            $form.find('.data-reset').show();
            $form.find('.data-edit').hide();
            $form.find('.data-delete').hide();

        });

        $('.admin-box').delegate('.data-reset', 'click', function (e) {
            var $form = $(this).parents('tr');

            var obj = $form.find('input, select, textarea');
            var b = $("form");

            $.each(obj, function (key, value) {
//                    console.log($(value));
                var $ele = $(value);
//                    console.log($ele.parent());
//                    console.log($("form"));
                var a = $ele.parent();
//                $ele.unwrap("form");

                if ($ele.hasClass("store-name") || $ele.hasClass("store-address") || $ele.hasClass("store-zipcode") || $ele.hasClass("store-city") || $ele.hasClass("store-lat") || $ele.hasClass("store-lng")) {
                    if ((a.is(b))) {
                        $ele.unwrap("form");
                    }
                }
            });

            $form.find('.required').css('display', 'none');
            resetRow($form, false);
        });

        //Events on select2 dropdowns
        $('.admin-box').delegate('.store-country', 'change', function () {
            var $parent = $(this).parents('tr');
            var $filter = {country_id: $(this).val()};
            setRegions($parent.find('select.store-region'), $filter, 'initRegion');
        });

        $('.admin-box').delegate('.store-region', 'change', function () {
            var $parent = $(this).parents('tr');
            var $filter = {region_id: $(this).val()};
            if ($("#country_id_topsss").val() != "all") {
                $filter.country_id = $parent.find('select.store-country').val();
            }
            setStates($parent.find('select.store-state'), $filter, 'initState');
        });

        $('.admin-box').delegate('.store-state', 'change', function () {
            var $parent = $(this).parents('tr');
            var $filter = {region_id: $parent.find('select.store-region').val(), state_id: $(this).val()};
            if ($("#country_id_topsss").val() != "all") {
                $filter.country_id = $parent.find('select.store-country').val();
            }
            setCities($parent.find('select.store-city'), $filter, 'initCity');
        });

        /*$('.fancybox').fancybox({
         type: 'ajax',
         href: $(this).attr('href'),
         titleShow: false
         });*/


        var country_id = "";
        if ($("#country_id_topsss").val() != "all") {
            country_id = $("#country_id_topsss").val();
        }


        $('#region_id, #state_id').on('change', function (e) {

            switch ($(e.currentTarget).attr('id')) {
                case 'region_id' :
                    $("#state_id").select2("val", "");
                    $("#city_id").select2("val", "");
                    break;
                case 'state_id' :
                    $("#city_id").select2("val", "");
                    break;
            }

            var $obj2 = new Object();

            $obj2.state = new Object();
            $obj2.state.obj = $("#state_id");
            $obj2.state.callback = "initStateDropdown";

            $obj2.city = new Object();
            $obj2.city.obj = $("#city_id");
            $obj2.city.callback = "initCityDropdown";

            var $filter2 = new Object();
            $filter2.country_id = country_id;
            $filter2.region_id = $("#region_id").val();
            $filter2.state_id = $("#state_id").val();

            lazy_load_dropdowns($filter2, $obj2);
        });

        /*$('.fancybox').fancybox({
         type: 'ajax',
         href: $(this).attr('href'),
         titleShow: false
         });*/

        $('.admin-box').delegate('.export', 'click', function (e) {
            createForm();
            $(".form_export:last").submit();
        });

    });
    function validateForm($form) {
        $form.validate({
            rules: {
                store_name: {
                    required: true,
                    maxlength: 250,
                    checkCFUniqueStore: true,
                    validateStore: true

                },
                address: {
                    required: true,
                    maxlength: 200
                },
                city_id: {
                    required: true
                },
                zip: {
                    required: true,
                    number: true
                },
                lat: {
                    checkLat: true
                },
                lng: {
                    checkLong: true
                }
            },
            onkeyup: false
        });
    }

    function initCountry($obj) {
        $($obj).select2({
            placeholder: 'Select Country',
            width: 100,
            allowClear: true
        });
    }

    function initRegion($obj) {
        $($obj).select2({
            placeholder: 'Select Region',
            width: 100,
            allowClear: true
        });
    }

    function initState($obj) {
        $($obj).select2({
            placeholder: 'Select State',
            width: 100,
            allowClear: true
        });
    }

    function initCity($obj) {
        $($obj).select2({
            placeholder: 'Select City',
            width: 100,
            allowClear: true
        });
    }

    function resetRow($obj, isTrigger) {
        $obj.find('.store-name-text').show();
        $obj.find('.store-name').val($obj.find('.store-name-text').html()).hide();

        $obj.find('.store-address-text').show();
        $obj.find('.store-address').val($obj.find('.store-address-text').html()).hide();

        $obj.find('.store-zipcode-text').show();
        $obj.find('.store-zipcode').val($obj.find('.store-zipcode-text').html()).hide();

        $obj.find('.store-lat-text').show();
        $obj.find('.store-lat').val($obj.find('.store-lat-text').html()).hide();

        $obj.find('.store-lng-text').show();
        $obj.find('.store-lng').val($obj.find('.store-lng-text').html()).hide();

        $obj.find('.store-country-text').show();
        $obj.find('.store-region-text').show();
        $obj.find('.store-state-text').show();
        $obj.find('.store-city-text').show();

        var $s_country = $obj.find('.store-country');
        var $s_region = $obj.find('.store-region');
        var $s_state = $obj.find('.store-state');
        var $s_city = $obj.find('.store-city');

        $s_country.hide();
        $s_region.hide();
        $s_state.hide();
        $s_city.hide();

        if (isTrigger) {
            $s_country.val($obj.find('.store-res-country-id').val()).trigger('change').hide();
            $s_region.val($obj.find('.store-res-region-id').val()).trigger('change').hide();
            $s_state.val($obj.find('.store-res-state-id').val()).trigger('change').hide();
            $s_city.val($obj.find('.store-res-city-id').val()).trigger('change').hide();
        }

        $obj.find('.data-save').hide();
        $obj.find('.data-reset').hide();
        $obj.find('.data-edit').show();
        $obj.find('.data-delete').show();

        $obj.find('dd').each(function () {
            $(this).remove();
        });
    }

    //Depenedent filter change...
    var params = {
        width: 120,
        allowClear: true
    };
    /*var country_id = "<?php echo $this->session->userdata("default_country_id"); ?>";
     if (country_id != "") {
     $('.admin-box').delegate('#region_id', 'change', function () {
     var $filter = {country_id: country_id, region_id: $(this).val()};
     setStates($("#state_id"), $filter, 'initStateDropdown');
     });

     $('.admin-box').delegate('#state_id', 'change', function () {
     var $filter = {country_id: country_id, region_id: $('#region_id').val(), state_id: $(this).val()};
     setCities($("#city_id"), $filter, 'initCityDropdown');
     });
     }
     */
    function initStateDropdown(obj) {
        params.placeholder = "Select State";
        makeSelect2($(obj), params);
    }

    function initCityDropdown(obj) {
        params.placeholder = "Select City";
        makeSelect2($(obj), params);
    }

    function createForm() {

        $(".form_export").remove();

        var form = $("<form/>",
            { action: '<?php echo base_url('admin/store/store_management/export'); ?>',
                class: "form_export",
                name: "form_export",
                method: "post"
            }
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: $("#search-field").attr("name"),
                    value: $("#search-field").val()
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'search_merchant_id',
                    value: $("#mer_label").val()
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'search_region_id',
                    value: $("#region_id").val()
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'search_state_id',
                    value: $("#state_id").val()
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'search_city_id',
                    value: $("#city_id").val()
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'search_country_id',
                    value: $("#country_id").val()
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'category',
                    value: $("#category").val()
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'action',
                    value: 'export'
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: '<?php echo $this->security->get_csrf_token_name() ?>',
                    value: '<?php echo $this->security->get_csrf_hash() ?>'
                }
            )
        );

        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'date_range_filter',
                    value: $("#date_range_filter2").val()
                }
            )
        );

        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'from_date',
                    value: $("#from_date2").val()
                }
            )
        );
        form.append(
            $("<input/>",
                { type: 'hidden',
                    name: 'to_date',
                    value: $("#to_date2").val()
                }
            )
        );

        $(".admin-box").append(form);
    }

    </script>
<?php endif; ?>
