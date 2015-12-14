<?php

// Load the configuration data
$toc_config = File::open(__DIR__ . DS . 'states' . DS . 'config.txt')->unserialize();

Config::set('toc_id', 1);

function do_TOC($content) {
    // No headlines
    if( ! Text::check($content)->has('</h')) {
        return $content;
    }
    global $toc_config;
    $config = Config::get();
    $speak = Config::speak();
    $prefix = $toc_config['id_prefix'];
    $suffix = $toc_config['id_suffix'];
    $prefix_b = $toc_config['id_back_prefix'];
    $suffix_b = $toc_config['id_back_suffix'];
    $regex = '#<h([1-6])(>|\s+.*?>)(.*?)<\/h\1>#si';
    $counter = $repeat = $depth = 0;
    $toc = "";
    if(preg_match_all($regex, $content, $matches)) {
        if($toc_config['add_toc']) {
            for($i = 0, $count = count($matches[0]); $i < $count; ++$i) {
                $level = (int) $matches[1][$i];
                $matches[2][$i] = rtrim($matches[2][$i], '>');
                if( ! Text::check('"not-toc-stage"', '"not-toc-stage ', ' not-toc-stage"', ' not-toc-stage ')->in($matches[2][$i])) {
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
                    $title = Text::parse($matches[3][$i], '->text', '<abbr><b><code><del><dfn><em><i><ins><kbd><mark><strong><sub><sup><time><u>');
                    if(preg_match('#(?:^|\s)id="(.*?)"#', $matches[2][$i], $id)) {
                        $toc .= '<li id="' . $prefix_b . $config->toc_id . '-' . ($i + 1) . $suffix_b . '"><a href="#' . $id[1] . '">' . trim($title) . '</a>';
                    } else {
                        $toc .= '<li id="' . $prefix_b . $config->toc_id . '-' . ($i + 1) . $suffix_b . '"><a href="#' . $prefix . Text::parse($title, '->slug') . $suffix . '">' . trim($title) . '</a>';
                    }
                    $toc .= ' <span class="marker">&#9666;</span>';
                }
            }
        }
        $content = preg_replace_callback($regex, function($matches) use($config, $speak, $toc_config, $prefix, $suffix, $prefix_b, $suffix_b, &$counter) {
            if( ! Text::check('"not-toc-stage"', '"not-toc-stage ', ' not-toc-stage"', ' not-toc-stage ')->in($matches[2])) {
                $counter++;
                $matches[2] = rtrim($matches[2], '>');
                if(Text::check(' ' . $matches[2])->has(' class="')) {
                    $attrs = ' ' . trim(str_replace('class="', 'class="toc-stage ', $matches[2]));
                } else {
                    $attrs = ' class="toc-stage" ' . trim($matches[2]);
                }
                if( ! Text::check(' ' . $matches[2])->has(' id="')) {
                    $attrs .= ' id="' . $prefix . Text::parse($matches[3], '->slug') . $suffix . '"';
                }
                if($toc_config['add_toc']) {
                    $anchor = ' <a class="toc-back" href="#' . $prefix_b . $config->toc_id . '-' . $counter . $suffix_b . '"' . ( ! empty($toc_config['toc_back_title']) ? ' title="' . $toc_config['toc_back_title'] . '"' : "") . '>&#9652;</a>';
                } else {
                    if( ! Text::check(' ' . $matches[2])->has(' id="')) {
                        $anchor = ' <a class="toc-point" href="#' . $prefix . Text::parse($matches[3], '->slug') . $suffix . '" title="' . $speak->permalink . '">&#167;</a>';
                    } else {
                        preg_match('#(?:^|\s)id="(.*?)"#i', $matches[2], $id);
                        $anchor = ' <a class="toc-point" href="#' . $id[1] . '" title="' . $speak->permalink . '">&#167;</a>';
                    }
                }
                return '<h' . $matches[1] . str_replace('  id="', ' id="', $attrs) . '>' . trim($matches[3]) . $anchor . '</h' . $matches[1] . '>';
            }
            return $matches[0];
        }, $content);
        return ($toc_config['add_toc'] && $toc !== "" ? '<div class="toc-block" id="toc-block:' . $config->toc_id . '">' . ($toc_config['toc_title'] !== "" ? '<h3 class="toc-header">' . $toc_config['toc_title'] . '</h3>' : "") . '<div class="toc-content">' . $toc . str_repeat('</li></ol>', $repeat) . '</div></div>' : "") . $content;
    }
    Config::set('toc_id', $config->toc_id + 1);
    return $content;
}

// Stop `do_TOC` from parsing our page headlines in index page
// The TOC markup should be visible only in single page mode
// Keep the manual article and page excerpt(s) clean!
if(Mecha::walk(glob(POST . DS . '*', GLOB_NOSORT | GLOB_ONLYDIR))->has(POST . DS . $config->page_type)) {
    // Register the `do_TOC` filter ...
    Filter::add('shield:lot', function($data) use($config) {
        if( ! isset($data[$config->page_type]->fields->disable_toc)) {
            return $data;
        }
        if( ! $data[$config->page_type]->fields->disable_toc) {
            if(isset($data[$config->page_type]->content)) {
                $data[$config->page_type]->content = do_TOC($data[$config->page_type]->content);
            }
        }
        return $data;
    });
    // Include the table of content's CSS
    Weapon::add('shell_after', function() {
        echo Asset::stylesheet(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'toc.css');
    });
}