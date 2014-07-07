<form class="form-plugin" action="<?php $states = File::open(PLUGIN . DS . 'toc' . DS . 'states' . DS . 'config.txt')->unserialize(); echo $config->url_current; ?>/update" method="post">
  <input name="token" type="hidden" value="<?php echo Guardian::makeToken(); ?>">
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_title; ?></span>
    <span class="grid span-4"><input name="toc_title" type="text" class="input-block" value="<?php echo Text::parse($states['toc_title'])->to_encoded_html; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_back_title; ?></span>
    <span class="grid span-4"><input name="toc_back_title" type="text" class="input-block" value="<?php echo Text::parse($states['toc_back_title'])->to_encoded_html; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_prefix; ?></span>
    <span class="grid span-4"><input name="id_prefix" type="text" value="<?php echo $states['id_prefix']; ?>" placeholder="<?php echo $speak->plugin_toc_placeholder_prefix; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_suffix; ?></span>
    <span class="grid span-4"><input name="id_suffix" type="text" value="<?php echo $states['id_suffix']; ?>" placeholder="<?php echo $speak->plugin_toc_placeholder_suffix; ?>"></span>
  </label>
  <div class="grid-group">
    <span class="grid span-2 form-label">&nbsp;</span>
    <span class="grid span-4"><label><input name="add_toc" type="checkbox"<?php echo $states['add_toc'] ? ' checked' : ""; ?>> <span><?php echo $speak->plugin_toc_title_add_toc; ?></span></label></span>
  </div>
  <div class="grid-group">
    <span class="grid span-2"></span>
    <span class="grid span-4"><button class="btn btn-primary btn-update" type="submit"><i class="fa fa-check-circle"></i> <?php echo $speak->update; ?></button></span>
  </div>
</form>