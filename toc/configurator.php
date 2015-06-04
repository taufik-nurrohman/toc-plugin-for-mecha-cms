<form class="form-plugin" action="<?php $toc_config = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize(); $toc_css = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'shell' . DS . 'toc.css')->read(); echo $config->url_current; ?>/update" method="post">
  <?php echo Form::hidden('token', $token); ?>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_title; ?></span>
    <span class="grid span-4">
    <?php echo Form::text('toc_title', Guardian::wayback('toc_title', $toc_config['toc_title']), null, array(
        'class' => 'input-block'
    )); ?>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_back_title; ?></span>
    <span class="grid span-4">
    <?php echo Form::text('toc_back_title', Guardian::wayback('toc_back_title', $toc_config['toc_back_title']), null, array(
        'class' => 'input-block'
    )); ?>
    </span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_prefix; ?></span>
    <span class="grid span-4">
    <?php echo Form::text('id_prefix', Guardian::wayback('id_prefix', $toc_config['id_prefix']), $speak->plugin_toc_placeholder_prefix); ?>
    </span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_suffix; ?></span>
    <span class="grid span-4">
    <?php echo Form::text('id_suffix', Guardian::wayback('id_suffix', $toc_config['id_suffix']), $speak->plugin_toc_placeholder_suffix); ?>
    </span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_back_prefix; ?></span>
    <span class="grid span-4">
    <?php echo Form::text('id_back_prefix', Guardian::wayback('id_back_prefix', $toc_config['id_back_prefix']), $speak->plugin_toc_placeholder_back_prefix); ?>
    </span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_back_suffix; ?></span>
    <span class="grid span-4">
    <?php echo Form::text('id_back_suffix', Guardian::wayback('id_back_suffix', $toc_config['id_back_suffix']), $speak->plugin_toc_placeholder_back_suffix); ?>
    </span>
  </label>
  <label class="grid-group">
    <span class="grid span-2 form-label"><?php echo $speak->plugin_toc_title_css; ?></span>
    <span class="grid span-4">
    <?php echo Form::textarea('css', Guardian::wayback('css', $toc_css), null, array(
        'class' => array(
            'textarea-block',
            'code'
        )
    )); ?>
    </span>
  </label>
  <div class="grid-group">
    <span class="grid span-2"></span>
    <span class="grid span-4"><label><?php echo Form::checkbox('add_toc', 'true', $toc_config['add_toc'], $speak->plugin_toc_title_add_toc); ?></label></span>
  </div>
  <div class="grid-group">
    <span class="grid span-2"></span>
    <span class="grid span-4"><?php echo Jot::button('action', $speak->update); ?></span>
  </div>
</form>