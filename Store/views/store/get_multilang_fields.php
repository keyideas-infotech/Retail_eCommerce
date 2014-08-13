<style>
    .multilang-field-container{
        margin: 10px;            
    }
    .multilang-field{
        padding: 10px;
        padding-left: 0px;
    }    
    .multilang-field textarea{
        width: 300px;  
    }    
    .multilang-field label{
        font-weight: bold;
    }    
</style>
<?php if (isset($id) && isset($code) && isset($codes) && isset($fields)): ?>
    <div class="multilang-field-container">
        <h2><?php echo $codes[$code]['label'] ?></h2>
        <?php
        $attributes = array(
            'name' => 'multilang_fileds_form',
            'id' => 'multilang_fileds_form'
        );
        ?>
        <?php echo form_open(site_url('admin/store/store_management/save_multilang_fields'), $attributes) ?>
        <input type="hidden" name="code" value="<?php echo $code ?>" />
        <input type="hidden" name="id" value="<?php echo $id ?>" />
        <ul id="myTab" class="nav nav-tabs">
            <?php
            $i = 0;
            foreach ($fields as $f):
                $active = ($i == 0) ? "class='active'" : "";
                ?>
                <li <?php echo $active; ?> >
                    <a href="#tab-<?php echo $f->lang_id; ?>" data-toggle="tab"><?php echo $f->lang_name; ?></a>
                </li>
                <?php
                $i++;
            endforeach;
            ?>
        </ul>
        <div id="myTabContent" class="tab-content">
            <?php
            $i = 0;
            foreach ($fields as $f):
                $active = ($i == 0) ? "in active" : "";
                ?>
                <div class="tab-pane fade <?php echo $active; ?>" id="tab-<?php echo $f->lang_id; ?>">                        
                    <div class="multilang-field">                        
                        <?php if ($codes[$code]['type'] == 'input'): ?>
                            <input class="multiLangStoreName" type="text" name="data[<?php echo $f->lang_id ?>][<?php echo $codes[$code]['field'] ?>]" value="<?php echo $f->$codes[$code]['field']; ?>" />
                        <?php elseif ($codes[$code]['type'] == 'textarea'): ?>
                            <textarea class="multiLangAddress" name="data[<?php echo $f->lang_id ?>][<?php echo $codes[$code]['field'] ?>]"><?php echo $f->$codes[$code]['field'] ?></textarea>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                $i++;
            endforeach;
            ?>
        </div>        
        <input type="submit" name="save" class="btn btn-primary" value="Save" />
        <?php echo form_close(); ?>
    </div>
<?php endif; ?>
<script type="text/javascript">
    $.validator.addMethod("validateStorePop", function(value, element) {
        var resultT = false;
        var ele = this.optional(element);
        var param = [];
        param.push({name: 'store_name', value: value});
        param.push({name: 'id', value: '<?php echo $id ?>'});
        $.ajax({
            type: 'GET',
            async: false,
            data: param,
            url: base_url + 'admin/store/store_management/validate_store',
            success: function(r) {
                r = $.trim(r);
                if (r === 'success') {
                    resultT = true;
                }
            }
        });
        return ele || resultT;
    }, "Store name already exist.");
    $.validator.addClassRules("multiLangStoreName", {
        required: true,
        maxlength: 250,
        validateStorePop: true
    });
    $.validator.addClassRules("multiLangAddress", {
        required: true
    });
    $('#multilang_fileds_form').validate({
        submitHandler: function(form) {
            submitMultiLangFieldForm($(form));
        }
    });
    function submitMultiLangFieldForm($form) {
        var data = $form.serialize();
        $form.find('[type=submit]').val('Saving....').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: data,
            success: function(r) {
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