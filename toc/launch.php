<?php

// Load the configuration data
$toc_config = File::open(PLUGIN . DS . 'toc' . DS . 'states' . DS . 'config.txt')->unserialize();

Config::set('toc_id', 1);

function page_TOC($content) {
    global $toc_config;
    $config = Config::get();
    $speak = Config::speak();
    $prefix = $toc_config['id_prefix'];
    $suffix = $toc_config['id_suffix'];
    $regex = '#<h([1-6])(.*?)>(.*?)<\/h([1-6])>#';
    $repeat = 0;
    $depth = 0;
    $toc = "";
    if(preg_match_all($regex, $content, $matches)) {
        for($i = 0, $count = count($matches[0]); $i < $count; ++$i) {
            $level = (int) $matches[1][$i];
            if( ! preg_match('# ?class="(.*?) ?not-toc-stage ?(.*?)"#', $matches[2][$i])) {
                if($depth < $level) {
                    $toc .= '<ol>';
                    $depth = $level;
                    $repeat++;
                } else {
                    $toc .= '</li>';
                }
                if($depth > $level) {
                    $toc .= '</ol></li>';
                    $depth = $level;
                    $repeat--;
                }
                $title = preg_replace('#<a .*?>|<\/a>#', "", $matches[3][$i]);
                if(preg_match('#id="(.*?)"#', $matches[2][$i], $id)) {
                    $toc .= '<li id="back:' . $config->toc_id . '-' . ($i + 1) . '"><a href="#' . $id[1] . '">' . trim($title) . '</a>';
                } else {
                    $toc .= '<li id="back:' . $config->toc_id . '-' . ($i + 1) . '"><a href="#' . $prefix . Text::parse($title)->to_slug . $suffix . '">' . trim($title) . '</a>';
                }
                $toc .= ' <span class="marker">&#9666;</span>';
            }
        }
        $counter = 0;
        $content = preg_replace_callback($regex, function($matches) use($config, $speak, $toc_config, $prefix, $suffix, $repeat, &$counter) {
            $counter++;
            if(strpos($matches[2], 'class="') !== false) {
                $attrs = ' ' . trim(str_replace('class="', 'class="toc-stage ', $matches[2]));
            } else {
                $attrs = ' class="toc-stage" ' . trim($matches[2]);
            }
            if(strpos($matches[2], 'id="') === false) {
                $attrs .= ' id="' . $prefix . Text::parse($matches[3])->to_slug . $suffix . '"';
            }
            if($toc_config['add_toc']) {
                $anchor = '<a class="toc-back" href="#back:' . $config->toc_id . '-' . $counter . '"' . ( ! empty($toc_config['toc_back_title']) ? ' title="' . $toc_config['toc_back_title'] . '"' : "") . '>&#9652;</a>';
            } else {
                if(strpos($matches[2], 'id="') === false) {
                    $anchor = '<a class="toc-permalink" href="#' . $prefix . Text::parse($matches[3])->to_slug . $suffix . '" title="' . $speak->permalink . '">&#182;</a>';
                } else {
                    preg_match('#id="(.*?)"#i', $matches[2], $m);
                    $anchor = '<a class="toc-permalink" href="#' . $m[1] . '" title="' . $speak->permalink . '">&#182;</a>';
                }
            }
            return '<h' . $matches[1] . str_replace('  id="', ' id="', $attrs) . '>' . trim($matches[3]) . ( ! preg_match('# ?class="(.*?) ?not-toc-stage ?(.*?)"#', $matches[2]) ? ' ' . $anchor : "") . '</h' . $matches[1] . '>';
        }, $content);
        return ($toc_config['add_toc'] && ! empty($toc) ? '<div class="toc-block" id="toc-block-' . $config->toc_id . '">' . ( ! empty($toc_config['toc_title']) ? '<h3 class="toc-header">' . $toc_config['toc_title'] . '</h3>' : "") . $toc . str_repeat('</li></ol>', $repeat) . '</div>' : "") . $content;
    }
    Config::set('toc_id', $config->toc_id + 1);
    return $content;
}

// Register the filters
Filter::add('article:content', 'page_TOC');
Filter::add('page:content', 'page_TOC');

// Add CSS for table of content
Weapon::add('shell_after', function() {
    echo Asset::stylesheet('cabinet/plugins/toc/shell/toc.css');
});


/**
 * Plugin Updater
 * --------------
 */

Route::accept($config->manager->slug . '/plugin/toc/update', function() use($config, $speak) {
    if( ! Guardian::happy()) {
        Shield::abort();
    }
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        unset($request['token']); // Remove token from request array
        $request['add_toc'] = isset($request['add_toc']) ? true : false;
        File::serialize($request)->saveTo(PLUGIN . DS . 'toc' . DS . 'states' . DS . 'config.txt');
        Notify::success(Config::speak('notify_success_updated', array($speak->plugin)));
        Guardian::kick(dirname($config->url_current));
    }
});