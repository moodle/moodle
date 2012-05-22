<?php

/**
 * Generic & abstract parser functions & skeleton. It has some functions & generic stuff.
 *
 * @author Josep Arús
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package wiki
 */

abstract class wiki_markup_parser extends generic_parser {

    protected $pretty_print = false;
    protected $printable = false;

    //page id
    protected $wiki_page_id;

    //sections
    protected $section_edit_text = "[edit]";
    protected $repeated_sections;

    protected $section_editing = true;

    //header & ToC
    protected $toc = array();

    /**
     * function wiki_parser_link_callback($link = "")
     *
     * Returns array('content' => "Inside the link", 'url' => "http://url.com/Wiki/Entry", 'new' => false).
     */
    private $linkgeneratorcallback = array('parser_utils', 'wiki_parser_link_callback');
    private $linkgeneratorcallbackargs = array();

    /**
     * Table generator callback
     */

    private $tablegeneratorcallback = array('parser_utils', 'wiki_parser_table_callback');

    /**
     * Get real path from relative path
     */
    private $realpathcallback = array('parser_utils', 'wiki_parser_real_path');
    private $realpathcallbackargs = array();

    /**
     * Before and after parsing...
     */

    protected function before_parsing() {
        $this->toc = array();

        $this->string = preg_replace('/\r\n/', "\n", $this->string);
        $this->string = preg_replace('/\r/', "\n", $this->string);

        $this->string .= "\n\n";

        if (!$this->printable && $this->section_editing) {
            $this->returnvalues['unparsed_text'] = $this->string;
            $this->string = $this->get_repeated_sections($this->string);
        }
    }

    protected function after_parsing() {
        if (!$this->printable) {
            $this->returnvalues['repeated_sections'] = array_unique($this->returnvalues['repeated_sections']);
        }

        $this->process_toc();

        $this->string = preg_replace("/\n\s/", "\n", $this->string);
        $this->string = preg_replace("/\n{2,}/", "\n", $this->string);
        $this->string = trim($this->string);
        $this->string .= "\n";
    }

    /**
     * Set options
     */

    protected function set_options($options) {
        parent::set_options($options);

        $this->returnvalues['link_count'] = array();
        $this->returnvalues['repeated_sections'] = array();
        $this->returnvalues['toc'] = "";

        foreach ($options as $name => $o) {
            switch ($name) {
            case 'link_callback':
                $callback = explode(':', $o);

                global $CFG;
                require_once($CFG->dirroot . $callback[0]);

                if (function_exists($callback[1])) {
                    $this->linkgeneratorcallback = $callback[1];
                }
                break;
            case 'link_callback_args':
                if (is_array($o)) {
                    $this->linkgeneratorcallbackargs = $o;
                }
                break;
            case 'real_path_callback':
                $callback = explode(':', $o);

                global $CFG;
                require_once($CFG->dirroot . $callback[0]);

                if (function_exists($callback[1])) {
                    $this->realpathcallback = $callback[1];
                }
                break;
            case 'real_path_callback_args':
                if (is_array($o)) {
                    $this->realpathcallbackargs = $o;
                }
                break;
            case 'table_callback':
                $callback = explode(':', $o);

                global $CFG;
                require_once($CFG->dirroot . $callback[0]);

                if (function_exists($callback[1])) {
                    $this->tablegeneratorcallback = $callback[1];
                }
                break;
            case 'pretty_print':
                if ($o) {
                    $this->pretty_print = true;
                }
                break;
            case 'pageid':
                $this->wiki_page_id = $o;
                break;
            case 'printable':
                if ($o) {
                    $this->printable = true;
                }
                break;
            }
        }
    }

    /**
     * Generic block rules
     */

    protected function line_break_block_rule($match) {
        return '<hr />';
    }

    protected function list_block_rule($match) {
        preg_match_all("/^\ *([\*\#]{1,5})\ *((?:[^\n]|\n(?!(?:\ *[\*\#])|\n))+)/im", $match[1], $listitems, PREG_SET_ORDER);

        return $this->process_block_list($listitems) . $match[2];
    }

    protected function nowiki_block_rule($match) {
        return parser_utils::h('pre', $this->protect($match[1]));
    }

    /**
     * Generic tag rules
     */

    protected function nowiki_tag_rule($match) {
        return parser_utils::h('tt', $this->protect($match[1]));
    }

    /**
     * Header generation
     */

    protected function generate_header($text, $level) {
        $text = trim($text);

        if (!$this->pretty_print && $level == 1) {
            $text .= parser_utils::h('a', $this->section_edit_text, array('href' => "edit.php?pageid={$this->wiki_page_id}&section=" . urlencode($text), 'class' => 'wiki_edit_section'));
        }

        if ($level < 4) {
            $this->toc[] = array($level, $text);
            $num = count($this->toc);
            $text = parser_utils::h('a', "", array('name' => "toc-$num")) . $text;
        }

        return parser_utils::h('h' . $level, $text) . "\n\n";
    }

    /**
     * Table of contents processing after parsing
     */
    protected function process_toc() {
        if (empty($this->toc)) {
            return;
        }

        $toc = "";
        $currentsection = array(0, 0, 0);
        $i = 1;
        foreach ($this->toc as & $header) {
            switch ($header[0]) {
            case 1:
                $currentsection = array($currentsection[0] + 1, 0, 0);
                break;
            case 2:
                $currentsection[1]++;
                $currentsection[2] = 0;
                if ($currentsection[0] == 0) {
                    $currentsection[0]++;
                }
                break;
            case 3:
                $currentsection[2]++;
                if ($currentsection[1] == 0) {
                    $currentsection[1]++;
                }
                if ($currentsection[0] == 0) {
                    $currentsection[0]++;
                }
                break;
            default:
                continue;
            }
            $number = "$currentsection[0]";
            if (!empty($currentsection[1])) {
                $number .= ".$currentsection[1]";
                if (!empty($currentsection[2])) {
                    $number .= ".$currentsection[2]";
                }
            }
            $toc .= parser_utils::h('p', $number . ". " . parser_utils::h('a', $header[1], array('href' => "#toc-$i")), array('class' => 'wiki-toc-section-' . $header[0] . " wiki-toc-section"));
            $i++;
        }

        $this->returnvalues['toc'] = "<div class=\"wiki-toc\"><p class=\"wiki-toc-title\">" . get_string('tableofcontents', 'wiki') . "</p>$toc</div>";
    }

    /**
     * List helpers
     */

    private function process_block_list($listitems) {
        $list = array();
        foreach ($listitems as $li) {
            $text = str_replace("\n", "", $li[2]);
            $this->rules($text);

            if ($li[1][0] == '*') {
                $type = 'ul';
            } else {
                $type = 'ol';
            }

            $list[] = array(strlen($li[1]), $text, $type);
        }
        $type = $list[0][2];
        return "<$type>" . "\n" . $this->generate_list($list) . "\n" . "</$type>" . "\n";
    }

    /**
     * List generation function from an array of array(level, text)
     */

    protected function generate_list($listitems) {
        $list = "";
        $current_depth = 1;
        $next_depth = 1;
        $liststack = array();
        for ($lc = 0; $lc < count($listitems) && $next_depth; $lc++) {
            $cli = $listitems[$lc];
            $nli = isset($listitems[$lc + 1]) ? $listitems[$lc + 1] : null;

            $text = $cli[1];

            $current_depth = $next_depth;
            $next_depth = $nli ? $nli[0] : null;

            if ($next_depth == $current_depth || $next_depth == null) {
                $list .= parser_utils::h('li', $text) . "\n";
            } else if ($next_depth > $current_depth) {
                $next_depth = $current_depth + 1;

                $list .= "<li>" . $text . "\n";
                $list .= "<" . $nli[2] . ">" . "\n";
                $liststack[] = $nli[2];
            } else {
                $list .= parser_utils::h('li', $text) . "\n";

                for ($lv = $next_depth; $lv < $current_depth; $lv++) {
                    $type = array_pop($liststack);
                    $list .= "</$type>" . "\n" . "</li>" . "\n";
                }
            }
        }

        for ($lv = 1; $lv < $current_depth; $lv++) {
            $type = array_pop($liststack);
            $list .= "</$type>" . "\n" . "</li>" . "\n";
        }

        return $list;
    }

    /**
     * Table generation functions
     */

    protected function generate_table($table) {
        $table_html = call_user_func_array($this->tablegeneratorcallback, array($table));

        return $table_html;
    }

    protected function format_image($src, $alt, $caption = "", $align = 'left') {
        $src = $this->real_path($src);
        return parser_utils::h('div', parser_utils::h('p', $caption) . '<img src="' . $src . '" alt="' . $alt . '" />', array('class' => 'wiki_image_' . $align));
    }

    protected function real_path($url) {
        $callbackargs = array_merge(array($url), $this->realpathcallbackargs);
        return call_user_func_array($this->realpathcallback, $callbackargs);
    }

    /**
     * Link internal callback
     */

    protected function link($link, $anchor = "") {
        $link = trim($link);
        if (preg_match("/^(https?|s?ftp):\/\/.+$/i", $link)) {
            $link = trim($link, ",.?!");
            return array('content' => $link, 'url' => $link);
        } else {
            $callbackargs = $this->linkgeneratorcallbackargs;
            $callbackargs['anchor'] = $anchor;
            $link = call_user_func_array($this->linkgeneratorcallback, array($link, $callbackargs));

            if (isset($link['link_info'])) {
                $l = $link['link_info']['link'];
                unset($link['link_info']['link']);
                $this->returnvalues['link_count'][$l] = $link['link_info'];
            }
            return $link;
        }
    }

    /**
     * Format links
     */

    protected function format_link($text) {
        $matches = array();
        if (preg_match("/^([^\|]+)\|(.+)$/i", $text, $matches)) {
            $link = $matches[1];
            $content = trim($matches[2]);
            if (preg_match("/(.+)#(.*)/is", $link, $matches)) {
                $link = $this->link($matches[1], $matches[2]);
            } else if ($link[0] == '#') {
                $link = array('url' => "#" . urlencode(substr($link, 1)));
            } else {
                $link = $this->link($link);
            }

            $link['content'] = $content;
        } else {
            $link = $this->link($text);
        }

        if (isset($link['new']) && $link['new']) {
            $options = array('class' => 'wiki_newentry');
        } else {
            $options = array();
        }

        $link['content'] = $this->protect($link['content']);
        $link['url'] = $this->protect($link['url']);

        $options['href'] = $link['url'];

        if ($this->printable) {
            $options['href'] = '#'; //no target for the link
            }
        return array($link['content'], $options);
    }

    /**
     * Section editing
     */

    public function get_section($header, $text, $clean = false) {
        if ($clean) {
            $text = preg_replace('/\r\n/', "\n", $text);
            $text = preg_replace('/\r/', "\n", $text);
            $text .= "\n\n";
        }

        preg_match("/(.*?)(=\ *\Q$header\E\ *=*\n.*?)((?:\n=[^=]+.*)|$)/is", $text, $match);

        if (!empty($match)) {
            return array($match[1], $match[2], $match[3]);
        } else {
            return false;
        }
    }

    protected function get_repeated_sections(&$text, $repeated = array()) {
        $this->repeated_sections = $repeated;
        return preg_replace_callback($this->blockrules['header']['expression'], array($this, 'get_repeated_sections_callback'), $text);
    }

    protected function get_repeated_sections_callback($match) {
        $num = strlen($match[1]);
        $text = trim($match[2]);
        if ($num == 1) {
            if (in_array($text, $this->repeated_sections)) {
                $this->returnvalues['repeated_sections'][] = $text;
                return $text . "\n";
            } else {
                $this->repeated_sections[] = $text;
            }
        }

        return $match[0];
    }

}
