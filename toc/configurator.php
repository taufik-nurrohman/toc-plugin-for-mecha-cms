<?php $c_toc = $config->states->{'plugin_' . md5(File::B(__DIR__))}; ?>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->plugin_toc->title; ?></span>
  <span class="grid span-5">
  <?php echo Form::text('title', Guardian::wayback('title_back', $c_toc->title), null, array(
      'class' => 'input-block'
  )); ?>
  </span>
</label>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->plugin_toc->title_back; ?></span>
  <span class="grid span-5">
  <?php echo Form::text('title_back', Guardian::wayback('title_back', $c_toc->title_back), null, array(
      'class' => 'input-block'
  )); ?>
  </span>
</label>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->plugin_toc->id; ?></span>
  <span class="grid span-5">
  <?php echo Form::text('id', Guardian::wayback('id', $c_toc->id), 'section:%s'); ?>
  </span>
</label>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->plugin_toc->id_back; ?></span>
  <span class="grid span-5">
  <?php echo Form::text('id_back', Guardian::wayback('id_back', $c_toc->id_back), 'back:%s'); ?>
  </span>
</label>
<label class="grid-group">
  <span class="grid span-1 form-label"><?php echo $speak->plugin_toc->css; ?></span>
  <span class="grid span-5">
  <?php echo Form::textarea('css', Guardian::wayback('css', File::open(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'toc.css')->read()), null, array(
      'class' => array(
          'textarea-block',
          'textarea-expand',
          'code'
      )
  )); ?>
  </span>
</label>
<div class="grid-group">
  <span class="grid span-1"></span>
  <span class="grid span-5"><label><?php echo Form::checkbox('add_toc', 'true', $c_toc->add_toc, $speak->plugin_toc->add_toc); ?></label></span>
</div>