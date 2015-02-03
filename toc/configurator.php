<form class="form-plugin" action="<?php $toc_config = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize(); $toc_css = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'shell' . DS . 'toc.css')->read(); echo $config->url_current; ?>/update" method="post">
  <input name="token" type="hidden" value="<?php echo $token; ?>">
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_title; ?></span>
    <span class="grid span-4"><input name="toc_title" type="text" class="input-block" value="<?php echo Text::parse(Guardian::wayback('toc_title', $toc_config['toc_title']))->to_encoded_html; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_back_title; ?></span>
    <span class="grid span-4"><input name="toc_back_title" type="text" class="input-block" value="<?php echo Text::parse(Guardian::wayback('toc_back_title', $toc_config['toc_back_title']))->to_encoded_html; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_prefix; ?></span>
    <span class="grid span-4"><input name="id_prefix" type="text" value="<?php echo Guardian::wayback('id_prefix', $toc_config['id_prefix']); ?>" placeholder="<?php echo $speak->plugin_toc_placeholder_prefix; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_suffix; ?></span>
    <span class="grid span-4"><input name="id_suffix" type="text" value="<?php echo Guardian::wayback('id_suffix', $toc_config['id_suffix']); ?>" placeholder="<?php echo $speak->plugin_toc_placeholder_suffix; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_back_prefix; ?></span>
    <span class="grid span-4"><input name="id_back_prefix" type="text" value="<?php echo Guardian::wayback('id_back_prefix', $toc_config['id_back_prefix']); ?>" placeholder="<?php echo $speak->plugin_toc_placeholder_back_prefix; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_back_suffix; ?></span>
    <span class="grid span-4"><input name="id_back_suffix" type="text" value="<?php echo Guardian::wayback('id_back_suffix', $toc_config['id_back_suffix']); ?>" placeholder="<?php echo $speak->plugin_toc_placeholder_back_suffix; ?>"></span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_css; ?></span>
    <span class="grid span-4"><textarea name="css" class="textarea-block code"><?php echo Text::parse(Guardian::wayback('css', $toc_css))->to_encoded_html; ?></textarea></span>
  </label>
  <div class="grid-group">
    <span class="grid span-2"></span>
    <span class="grid span-4"><label><input name="add_toc" type="checkbox"<?php echo $toc_config['add_toc'] ? ' checked' : ""; ?>> <span><?php echo $speak->plugin_toc_title_add_toc; ?></span></label></span>
  </div>
  <div class="grid-group">
    <span class="grid span-2"></span>
    <span class="grid span-4"><button class="btn btn-action" type="submit"><i class="fa fa-check-circle"></i> <?php echo $speak->update; ?></button></span>
  </div>
</form>