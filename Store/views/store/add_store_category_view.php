<style>
    .beacon-container {
        margin: 10px;
    }

    .filters-container {
        padding: 10px;
    }

    .filters-container .filter {
        float: left;
        padding: 10px;
    }

    .filter-error {
        color: red;
        display: block;
    }

    #bea_beacons {
        max-height: 200px;
        height: 200px;
        overflow-y: auto;
        padding: 10px;

    }
</style>
<?php if (isset($store_id)): ?>
    <div class="beacon-container">
        <h2 class="bea-title4"><?php echo (is_object($store_info)) ? $store_info->store_name : "" ?></h2>
        <br/>
        <?php
        $attributes = array(
            'name' => 'form_store_category',
            'id' => 'form_store_category'
        );
        ?>
        <?php echo form_open(site_url('admin/store/store_management/save_store_category'), $attributes) ?>
        <input type="hidden" name="store_id" id="store_id" value="<?php echo $store_id ?>"/>

        <div style="clear: both;"></div>
        <div id="bea_beacons" class="bea_beacons2">
            <?php
            if (isset($categories) && !empty($categories)) {
                foreach ($categories as $category) {
                    ?>
                    <li>
                        <input type="checkbox" name="category_ids[]" value="<?php echo $category->category_id ?>">
                        <span><?php echo $category->category_name; ?></span>
                    </li>
                <?php
                }
            }
            ?>
        </div>
        <input type="submit" name="save" class="btn btn-primary bea-btn5" value="Save"/>
        <?php echo form_close(); ?>
    </div>
<?php endif; ?>
<script type="text/javascript">
    $(function () {

        $('#form_store_category').validate({
            submitHandler: function (form) {
                submitCategoryForm($(form));
            }
        });
    });

    function submitCategoryForm($form) {
        var data = $form.serialize();
        $form.find('[type=submit]').val('Saving....').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: data,
            success: function (r) {
                var $json = $.parseJSON(r);
                $form.find('[type=submit]').val('Save').removeAttr('disabled');
                if ($json.hasOwnProperty('success')) {
                    $.fancybox.close();
                    $('.submit-filters').trigger('click');
                } else {
                    $.fancybox.close();
                }
            }
        });
    }
</script>