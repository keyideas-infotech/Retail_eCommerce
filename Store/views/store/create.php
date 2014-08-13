<div class="admin-box">
    <h3>Store</h3>

    <div class="data-container">
        <div class="filters-container">
            <div class="filter">
                <label>Merchant <span class="required">*</span></label>
                <select name="merchant_id" id="merchant_id" class="filter-required">
                    <option value=""></option>
                    <?php if (isset($merchants)): ?>
                        <?php foreach ($merchants as $record): ?>
                            <option
                                value="<?php echo $record['id'] ?>"><?php echo $record['display_name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="filter-error"></span>
            </div>
            <div class="filter" style="display: none;">
                <label>Country</label>
                <select name="country_id" id="country_id">
                    <!--<option value=""></option>-->
                    <?php if (isset($country_list)): ?>
                        <?php foreach ($country_list as $record): ?>
                            <option
                                value="<?php echo $record['country_id'] ?>" <?php echo $this->session->userdata('default_country_id') == $record['country_id'] ? 'selected="selected"' : '' ?>><?php echo $record['country_name'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="filter-error"></span>
            </div>
            <div class="filter">
                <label>Region</label>
                <select name="region_id" id="region_id">
                    <option value=""></option>
                </select>
                <span class="filter-error"></span>
            </div>
            <div class="filter">
                <label>State</label>
                <select name="state_id" id="state_id">
                    <option value=""></option>
                </select>
                <span class="filter-error"></span>
            </div>
            <div class="filter">
                <label>
                    City
                    <span class="required">*</span>
                </label>
                <select name="city_id" id="city_id" class="filter-required">
                    <option value=""></option>
                </select>
                <span class="filter-error"></span>
            </div>
        </div>
        <div style="clear: both"></div>

        <div class="merchant_managementt">
            <div class="c-label-left">
                <label><?php echo lang('store_management_store_name') . lang('bf_form_label_required') ?></label>
            </div>
            <div class="c-label-left">
                <label><?php echo lang('store_management_address1') . lang('bf_form_label_required') ?></label>
            </div>
            <div class="c-label-left">
                <label><?php echo lang('store_management_zip') . lang('bf_form_label_required') ?></label>
            </div>
            <div class="c-label-left">
                <label><?php echo lang('bf_common_lat') ?></label>
            </div>
            <div class="c-label-left">
                <label><?php echo lang('bf_common_lng') ?></label>
            </div>
            <div class="c-label-left">
                <label><?php echo lang('bf_common_category') . lang('bf_form_label_required') ?></label>
            </div>
        </div>
        <div style="clear: both"></div>
        <div class="data-container-row">
            <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal form-store-management"'); ?>
            <fieldset>
                <input type="hidden" name="id" value="" class="pks-id"/>
                <input type="hidden" name="address_id" value="" class="address-id"/>
                <input type="hidden" name="mer_id" value="" class="mer-id"/>
                <input type="hidden" name="ct_id" value="" class="ct-id"/>

                <div class="row-colum-left">
                    <input type="text" name="store_name" maxlength="255" value="" class="store-name"/>
                    <span class="store-name-text"></span>
                </div>
                <div class="row-colum-left">
                    <textarea name="address" class="store-address"></textarea>
                    <span class="store-address-text"></span>
                </div>
                <div class="row-colum-left">
                    <input type="text" name="zip" maxlength="6" value="" class="store-zipcode"/>
                    <span class="store-zipcode-text"></span>
                </div>
                <div class="row-colum-left">
                    <input type="text" name="lat" maxlength="7" value="" class="store-lat"/>
                    <span class="store-lat-text"></span>
                </div>
                <div class="row-colum-left">
                    <input type="text" name="lng" maxlength="7" value="" class="store-lng"/>
                    <span class="store-lng-text"></span>
                </div>

                <div class="categories" style="float:left;">
                    <div class="row-colum-left row-columleft1" style="">
                        <li><a class="btn toggle-all2" data-rel-class="categories" href="javascript:void(0);">toggle all</a></li>
                        <?php
                        if (isset($categories) && !empty($categories)) {
                            foreach ($categories as $category) {
                                ?>
                                <li><input type="checkbox" name="category_ids[]"
                                           value="<?php echo $category->category_id ?>">
                                    <span><?php echo $category->category_name ?></span>

                                </li>
                            <?php
                            }
                        }
                        ?>
                    </div>
                    <div id="store-errors"></div>
                </div>

                <div class="row-colum-contorls">
                    <a href="javascript:void(0);" class="data-save-n-add" title="Save & Add More"><i class="icon-plus">
                            &nbsp;</i></a>
                    <a href="javascript:void(0);" class="data-save" style="display: none;" title="Save"><i
                            class="icon-ok-sign">&nbsp;</i></a>
                    <a href="javascript:void(0);" class="data-edit" style="display: none;" title="Edit"><i
                            class="icon-pencil">&nbsp;</i></a>
                    <a href="javascript:void(0);" class="data-reset" style="display: none;" title="Cancel"><i
                            class="icon-remove-circle">&nbsp;</i></a>
                    <a href="javascript:void(0);" class="data-delete" style="display: none;" title="Delete"><i
                            class="icon-remove">&nbsp;</i></a>
                    <span class="data-success-outer" style="display: none"><i class="icon-ok">&nbsp;</i></span>
                </div>
            </fieldset>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
