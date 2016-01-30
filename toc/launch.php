<?php

// Load the configuration data
$c_toc = $config->states->{'plugin_' . md5(File::B(__DIR__))};

Config::set('toc_id', 1);

function do_toc($content, $results = array()) {
    // Disabled
    $results = Mecha::O($results);
    if(isset($results->fields->disable_toc) && $results->fields->disable_toc !== false) {
        return $content;
    }
    // No headline(s)
    if(strpos($content, '</h') === false) {
        return $content;
    }
    global $speak, $c_toc;
    $config = Config::get();
    $id = $c_toc->id;
    $id_back = $c_toc->id_back;
    $regex = '#<h([1-6])(>|\s+.*?>)(.*?)<\/h\1>#si';
    $counter = $repeat = $depth = 0;
    $toc = "";
    if(strpos($content, '{{toc}}') === false) $content = '{{toc}}' . "\n\n" . $content;
    if(strpos($id, '%s') === false) $id = '%s';
    if(strpos($id_back, '%s') === false) $id_back = '%s';
    if(preg_match_all($regex, $content, $matches)) {
        if($c_toc->add_toc) {
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
                    $title = Text::parse($matches[3][$i], '->text', str_replace('<a>', "", WISE_CELL_I));
                    $toc .= '<li id="' . sprintf($id_back, $config->toc_id . '-' . ($i + 1)) . '">';
                    if(preg_match('#(?:^|\s)id="(.*?)"#', $matches[2][$i], $id_o)) {
                        $toc .= '<a href="#' . $id_o[1] . '">' . $title . '</a>';
                    } else {
                        $toc .= '<a href="#' . sprintf($id, Text::parse($title, '->slug')) . '">' . $title . '</a>';
                    }
                    $toc .= ' <span class="toc-mark"></span>';
                }
            }
        }
        $content = preg_replace_callback($regex, function($matches) use($config, $speak, $c_toc, $id, $id_back, &$counter) {
            if( ! Text::check('"not-toc-stage"', '"not-toc-stage ', ' not-toc-stage"', ' not-toc-stage ')->in($matches[2])) {
                $counter++;
                $matches[2] = rtrim($matches[2], '>');
                if(strpos(' ' . $matches[2], ' class="') === false) {
                    $attrs = ' class="toc-stage" ' . trim($matches[2]);
                } else {
                    $attrs = ' ' . trim(str_replace('class="', 'class="toc-stage ', $matches[2]));
                }
                if(strpos(' ' . $matches[2], ' id="') === false) {
                    $attrs .= ' id="' . sprintf($id, Text::parse($matches[3], '->slug')) . '"';
                }
                if($c_toc->add_toc) {
                    $anchor = ' <a class="toc-back" href="#' . sprintf($id_back, $config->toc_id . '-' . $counter) . '"' . ($c_toc->title_back !== "" ? ' title="' . $c_toc->title_back . '"' : "") . '></a>';
                } else {
                    if(strpos(' ' . $matches[2], ' id="') === false) {
                        $anchor = ' <a class="toc-point" href="#' . sprintf($id, Text::parse($matches[3], '->slug')) . '" title="' . $speak->permalink . '"></a>';
                    } else {
                        preg_match('#(?:^|\s)id="(.*?)"#i', $matches[2], $id_o);
                        $anchor = ' <a class="toc-point" href="#' . $id_o[1] . '" title="' . $speak->permalink . '"></a>';
                    }
                }
                return '<h' . $matches[1] . str_replace('  id="', ' id="', $attrs) . '>' . trim($matches[3]) . $anchor . '</h' . $matches[1] . '>';
            }
            return $matches[0];
        }, $content);
        $toc = $c_toc->add_toc && $toc !== "" ? '<div class="toc-block" id="toc-block:' . $config->toc_id . '">' . ($c_toc->title !== "" ? '<h3 class="toc-header">' . $c_toc->title . '</h3>' : "") . '<div class="toc-content">' . $toc . str_repeat('</li></ol>', $repeat) . '</div></div>' : "";
        return preg_replace('#(?<!`)\{\{toc\}\}(?!`)#', $toc, $content);
    }
    Config::set('toc_id', $config->toc_id + 1);
    return $content;
}

if($config->is->post) {
    // Apply `do_toc` filter
    Filter::add($config->page_type . ':content', 'do_toc', 10);
    // Apply skin to page
    Weapon::add('shell_after', function() {
        echo Asset::stylesheet(__DIR__ . DS . 'assets' . DS . 'shell' . DS . 'toc.css');
    });
}