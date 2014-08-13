
<?php if (validation_errors()) : ?>
    <div class="alert alert-block alert-error fade in ">
        <a class="close" data-dismiss="alert">&times;</a>
        <h4 class="alert-heading">Please fix the following errors :</h4>
        <?php echo validation_errors(); ?>
    </div>
<?php endif; ?>
<?php
// Change the css classes to suit your needs
if (isset($store_management)) {
    $store_management = (array) $store_management;
}
$id = isset($store_management['store_id']) ? $store_management['store_id'] : '';
?>
<div class="admin-box">
    <h3>Store Management</h3>
    <?php echo form_open($this->uri->uri_string(), 'class="form-custom-horizontal" id="form-edit-store-management" '); ?>
    <fieldset class="form-row">
        <!-- Start Form Container --> 
        <div class="data-container">
            <div class="data-container-row">
                <div class="column_33">
                    <?php
                    $options = array(
                        '0' => 'Select Merchant'
                    );
                    if (isset($merchants) && !empty($merchants)) {
                        foreach ($merchants as $values) {
                            $options[$values['id']] = $values['display_name'];
                        }
                    }
                    ?>

                    <?php echo form_dropdown('merchant_id', $options, set_value('merchant_id', isset($store_management['merchant_id']) ? $store_management['merchant_id'] : ''), 'Merchant' . lang('bf_form_label_required')) ?>


                    <div class="control-group <?php echo form_error('store_name') ? 'error' : ''; ?>">
                        <?php echo form_label('Store Name' . lang('bf_form_label_required'), 'store_name', array('class' => "control-label")); ?>
                        <div class='controls'>
                            <input id="store_name" type="text" name="store_name" maxlength="255" value="<?php echo set_value('store_name', isset($store_management['store_name']) ? $store_management['store_name'] : ''); ?>"  />
                            <span class="help-inline"><?php echo form_error('store_name'); ?></span>
                        </div>
                    </div> 

                    <div class="control-group <?php echo form_error('address1') ? 'error' : ''; ?>">
                        <?php echo form_label(lang('store_management_address1') . lang('bf_form_label_required'), 'address1', array('class' => "control-label")); ?>
                        <div class='controls'>
                            <input type="text" name="address1" maxlength="255" value="<?php echo set_value('address1', isset($store_address['address1']) ? $store_address['address1'] : ''); ?>"  />
                            <span class="help-inline"><?php echo form_error('address1'); ?></span>
                        </div>
                    </div>
                    <div class="control-group <?php echo form_error('address2') ? 'error' : ''; ?>">
                        <?php echo form_label(lang('store_management_address2'), 'address2', array('class' => "control-label")); ?>
                        <div class='controls'>
                            <input type="text" name="address2" maxlength="255" value="<?php echo set_value('address2', isset($store_address['address2']) ? $store_address['address2'] : ''); ?>"  />
                            <span class="help-inline"><?php echo form_error('address2'); ?></span>
                        </div>
                    </div>  
                </div>                                

                <div class="column_33">
                    <?php echo form_dropdown('city_id', $city_list_dropdown, set_value('city_id', isset($store_address['city_id']) ? $store_address['city_id'] : ''), lang('store_management_city') . lang('bf_form_label_required')) ?>
                    <span class="help-inline"><?php echo form_error('city_id'); ?></span>

                    <div class="control-group <?php echo form_error('zip') ? 'error' : ''; ?>">
                        <?php echo form_label(lang('store_management_zip') . lang('bf_form_label_required'), 'zip', array('class' => "control-label")); ?>
                        <div class='controls'>
                            <input type="text" name="zip" maxlength="6" value="<?php echo set_value('zip', isset($store_address['zip']) ? $store_address['zip'] : ''); ?>"  />
                            <span class="help-inline"><?php echo form_error('zip'); ?></span>
                        </div>
                    </div> 

                    <?php
                    $options = array();
                    if (isset($languages) && !empty($languages)) {
                        foreach ($languages as $lang) {
                            $options[$lang->lang_id] = $lang->lang_name;
                        }
                    }
                    ?>
                    <?php echo form_dropdown('lang_id', $options, set_value('lang_id', isset($store_management['lang_id']) ? $store_management['lang_id'] : ''), 'Language') ?>                

                    <?php // Change the values in this array to populate your dropdown as required    ?>

                    <?php
                    $options = array(
                        1 => 'Active', 0 => 'Inactive',
                    );
                    ?>

                    <?php echo form_dropdown('status', $options, set_value('status', isset($store_management['status']) ? $store_management['status'] : ''), 'Status') ?>

                    <input type="hidden" name="address_id" value="<?php echo isset($store_management['address_id']) ? $store_management['address_id'] : 0; ?>" />
                </div>
                <div class="column_33">
                    <div class="control-group <?php echo form_error('category') ? 'error' : ''; ?>">
                        <?php echo form_label('Categories' . lang('bf_form_label_required'), 'address_id', array('class' => "control-label")); ?>
                        <div class='controls'>                
                            <div class="category_list">
                                <?php echo nested2ul($categories, $store_categories); ?>
                            </div>
                            <span class="help-inline"><?php echo form_error('address_id'); ?></span>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

        <div class="form-actions">
            <div class="shift_right">                
                <input type="submit" name="save" class="btn btn-primary" value="Edit Store" />
                or <?php echo anchor(SITE_AREA . '/store/store_management', lang('store_management_cancel'), 'class="btn btn-warning"'); ?>


                <?php if ($this->auth->has_permission('Store_Management.Store.Delete')) : ?> or 
                    <button type="submit" name="delete" class="btn btn-danger" id="delete-me" onclick="return confirm('<?php echo lang('store_management_delete_confirm'); ?>')">
                        <i class="icon-trash icon-white">&nbsp;</i>&nbsp;<?php echo lang('store_management_delete_record'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </fieldset>
    <?php echo form_close(); ?>


</div>
