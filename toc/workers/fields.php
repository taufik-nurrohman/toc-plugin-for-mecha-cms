<?php

$scopes = Mecha::walk(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR), function($v) {
    return File::B($v);
});

return array(
    'disable_toc' => array(
        'title' => $speak->plugin_toc->disable_toc,
        'type' => 'boolean',
        'scope' => implode(',', $scopes)
    )
);