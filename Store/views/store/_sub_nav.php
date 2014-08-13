<ul class="nav nav-pills">
	<li <?php echo $this->uri->segment(4) == '' ? 'class="active"' : '' ?>>
		<a href="<?php echo site_url(SITE_AREA .'/store/store_management') ?>" id="list"><?php echo lang('store_management_list'); ?></a>
	</li>
	<?php if ($this->auth->has_permission('Store_Management.Store.Create')) : ?>
	<li <?php echo $this->uri->segment(4) == 'create' ? 'class="active"' : '' ?> >
		<a href="<?php echo site_url(SITE_AREA .'/store/store_management/create') ?>" id="create_new"><?php echo lang('store_management_new'); ?></a>
	</li>
	<?php endif; ?>
</ul>