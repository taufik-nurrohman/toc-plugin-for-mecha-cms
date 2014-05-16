<?php

if( ! $language = File::exist(PLUGIN . '/toc/languages/' . $config->language . '/speak.txt')) {
    $language = PLUGIN . '/toc/languages/en_US/speak.txt';
}

Config::merge('speak', Text::toArray(File::open($language)->read()));
Config::set('toc_id', 1);

$states = unserialize(File::open(PLUGIN . '/toc/states/config.txt')->read());

Filter::add('content', function($content) use($states) {

    $config = Config::get();
    $speak = Config::speak();
    $prefix = $states['id_prefix'];
    $suffix = $states['id_suffix'];
    $regex = '#<h([1-6])(.*?)>(.*?)<\/h([1-6])>#';
    $toc_level = 0;

    $toc = '<div class="toc-block" id="toc-block-' . $config->toc_id . '">' . ( ! empty($states['toc_title']) ? '<h3 class="toc-header">' . $states['toc_title'] . '</h3>' : "");

    if(preg_match_all($regex, $content, $matches)) {

        for($i = 0, $count = count($matches[0]); $i < $count; ++$i) {
            $level = (int) $matches[1][$i];
            if($toc_level < $level) {
                $toc .= '<ol>';
                $toc_level = $level;
            }
            if($toc_level > $level) {
                $toc .= '</ol>';
                $toc_level = $level;
            }
            if( ! preg_match('# ?class="(.*?) ?not-toc-stage ?(.*?)"#', $matches[2][$i])) {
                if(preg_match('#id="(.*?)"#', $matches[2][$i], $id)) {
                    $toc .= '<li id="back:' . ($i + 1) . '"><a href="#' . $id[1] . '">' . trim($matches[3][$i]) . '</a> <span class="marker">&#9666;</span></li>';
                } else {
                    $toc .= '<li id="back:' . $config->toc_id . '-' . ($i + 1) . '"><a href="#' . $prefix . Text::parse($matches[3][$i])->to_slug . $suffix . '">' . trim($matches[3][$i]) . '</a> <span class="marker">&#9666;</span></li>';
                }
            }
        }

        $counter = 0;
        $content = preg_replace_callback($regex, function($matches) use($config, $speak, $states, $prefix, $suffix, &$counter) {
            $counter++;
            if(strpos($matches[2], 'class="') !== false) {
                $attrs = ' ' . trim(str_replace('class="', 'class="toc-stage ', $matches[2]));
            } else {
                $attrs = ' class="toc-stage" ' . trim($matches[2]);
            }
            if(strpos($matches[2], 'id="') === false) {
                $attrs .= ' id="' . $prefix . Text::parse($matches[3])->to_slug . $suffix . '"';
            }
            if($states['add_toc']) {
                $anchor = '<a class="toc-back" href="#back:' . $config->toc_id . '-' . $counter . '"' . ( ! empty($states['toc_back_title']) ? ' title="' . $states['toc_back_title'] . '"' : "") . '>&#9652;</a>';
            } else {
                $anchor = '<a class="toc-permalink" href="#' . $prefix . Text::parse($matches[3])->to_slug . $suffix . '" title="' . $speak->permalink . '">&#182;</a>';
            }
            return '<h' . $matches[1] . $attrs . '>' . trim($matches[3]) . ( ! preg_match('# ?class="(.*?) ?not-toc-stage ?(.*?)"#', $matches[2]) ? ' ' . $anchor : "") . '</h' . $matches[1] . '>';
        }, $content);

        return ($states['add_toc'] ? $toc . '</ol></div>' : "") . $content;

    }

    Config::set('toc_id', $config->toc_id + 1);

    return $content;

});

// Remove TOC in comments
Filter::add('comment', function($content) {
    return preg_replace(
        array(
            '#<div class="toc-block"(.*?)<\/div>#',
            '# ?(not-)?toc-stage ?"#',
            '# <a class="toc-(back|permalink)"(.*?)<\/a>#'
        ), "",
    $content);

});

// Add CSS for table of content
Weapon::add('shell_after', function() {
    echo Asset::stylesheet(str_replace(ROOT, "", PLUGIN) . '/toc/shell/toc.css');
});

/**
 * Plugin Updater
 */
Route::accept($config->manager->slug . '/plugin/toc/update', function() use($config, $speak) {
    if( ! Guardian::happy()) {
        Shield::abort();
    }
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        unset($request['token']); // Remove token from fields
        $request['add_toc'] = isset($request['add_toc']) ? true : false;
        File::write(serialize($request))->saveTo(PLUGIN . '/toc/states/config.txt');
        Notify::success(Config::speak('notify_success_updated', array($speak->plugin)));
        Guardian::kick(dirname($config->url_current));
    }
});