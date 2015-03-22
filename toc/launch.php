<?php

// Load the configuration data
$toc_config = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize();

Config::set('toc_id', 1);

function do_TOC($content) {
    global $toc_config;
    $config = Config::get();
    $speak = Config::speak();
    $prefix = $toc_config['id_prefix'];
    $suffix = $toc_config['id_suffix'];
    $prefix_b = $toc_config['id_back_prefix'];
    $suffix_b = $toc_config['id_back_suffix'];
    $regex = '#<h([1-6])(>| +.*?>)(.*?)<\/h\1>#i';
    $regex_exc = '#(?:^| )class=".*? *not-toc-stage *.*?"#i';
    $repeat = 0;
    $depth = 0;
    $toc = "";
    if(preg_match_all($regex, $content, $matches)) {
        if($toc_config['add_toc']) {
            for($i = 0, $count = count($matches[0]); $i < $count; ++$i) {
                $level = (int) $matches[1][$i];
                $matches[2][$i] = rtrim($matches[2][$i], '>');
                if( ! preg_match($regex_exc, $matches[2][$i])) {
                    if($depth < $level) {
                        $toc .= str_repeat('<ol>', $i > 0 ? $level - $depth : 1);
                        $repeat++;
                        $depth = $level;
                    } else {
                        $toc .= '</li>';
                    }
                    if($depth > $level) {
                        $toc .= str_repeat('</ol></li>', $i > 0 ? $depth - $level : 1);
                        $repeat = $depth - $level;
                        $depth = $level;
                    }
                    $title = preg_replace('#<a(>| .*?>)|<\/a>#', "", $matches[3][$i]);
                    if(preg_match('#(?:^| )id="(.*?)"#', $matches[2][$i], $id)) {
                        $toc .= '<li id="' . $prefix_b . $config->toc_id . '-' . ($i + 1) . $suffix_b . '"><a href="#' . $id[1] . '">' . trim($title) . '</a>';
                    } else {
                        $toc .= '<li id="' . $prefix_b . $config->toc_id . '-' . ($i + 1) . $suffix_b . '"><a href="#' . $prefix . Text::parse($title)->to_slug . $suffix . '">' . trim($title) . '</a>';
                    }
                    $toc .= ' <span class="marker">&#9666;</span>';
                }
            }
        }
        $counter = 0;
        $content = preg_replace_callback($regex, function($matches) use($config, $speak, $toc_config, $prefix, $suffix, $prefix_b, $suffix_b, $regex_exc, &$counter) {
            if( ! preg_match($regex_exc, $matches[2])) {
                $counter++;
                $matches[2] = rtrim($matches[2], '>');
                if(strpos($matches[2], 'class="') !== false) {
                    $attrs = ' ' . trim(str_replace('class="', 'class="toc-stage ', $matches[2]));
                } else {
                    $attrs = ' class="toc-stage" ' . trim($matches[2]);
                }
                if(strpos($matches[2], 'id="') === false) {
                    $attrs .= ' id="' . $prefix . Text::parse($matches[3])->to_slug . $suffix . '"';
                }
                if($toc_config['add_toc']) {
                    $anchor = ' <a class="toc-back" href="#' . $prefix_b . $config->toc_id . '-' . $counter . $suffix_b . '"' . ( ! empty($toc_config['toc_back_title']) ? ' title="' . $toc_config['toc_back_title'] . '"' : "") . '>&#9652;</a>';
                } else {
                    if(strpos($matches[2], 'id="') === false) {
                        $anchor = ' <a class="toc-permalink" href="#' . $prefix . Text::parse($matches[3])->to_slug . $suffix . '" title="' . $speak->permalink . '">&#167;</a>';
                    } else {
                        preg_match('#(?:^| )id="(.*?)"#i', $matches[2], $id);
                        $anchor = ' <a class="toc-permalink" href="#' . $id[1] . '" title="' . $speak->permalink . '">&#167;</a>';
                    }
                }
                return '<h' . $matches[1] . str_replace('  id="', ' id="', $attrs) . '>' . trim($matches[3]) . $anchor . '</h' . $matches[1] . '>';
            }
            return $matches[0];
        }, $content);
        return ($toc_config['add_toc'] && ! empty($toc) ? '<div class="toc-block" id="toc-block:' . $config->toc_id . '">' . ( ! empty($toc_config['toc_title']) ? '<h3 class="toc-header">' . $toc_config['toc_title'] . '</h3>' : "") . '<div class="toc-content">' . $toc . str_repeat('</li></ol>', $repeat) . '</div></div>' : "") . $content;
    }
    Config::set('toc_id', $config->toc_id + 1);
    return $content;
}

// Register the filters
Filter::add('article:content', 'do_TOC');
Filter::add('page:content', 'do_TOC');

// Add CSS for table of content
Weapon::add('shell_after', function() {
    echo Asset::stylesheet('cabinet/plugins/' . basename(__DIR__) . '/shell/toc.css');
});


/**
 * Plugin Updater
 * --------------
 */

Route::accept($config->manager->slug . '/plugin/' . basename(__DIR__) . '/update', function() use($config, $speak) {
    if( ! Guardian::happy()) {
        Shield::abort();
    }
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        unset($request['token']); // Remove token from request array
        File::write($request['css'])->saveTo(PLUGIN . DS . basename(__DIR__) . DS . 'shell' . DS . 'toc.css');
        unset($request['css']); // Remove CSS from request array
        $request['add_toc'] = isset($request['add_toc']) ? true : false;
        File::serialize($request)->saveTo(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt', 0600);
        Notify::success(Config::speak('notify_success_updated', array($speak->plugin)));
        Guardian::kick(dirname($config->url_current));
    }
});