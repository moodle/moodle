<?php

/**
 * HTML parser implementation. It only implements links.
 *
 * @author Josep Arús
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_wiki
 */
include_once("nwiki.php");

class html_parser extends nwiki_parser {
    protected $blockrules = array();

    protected $section_editing = true;

    public function __construct() {
        parent::__construct();
        $this->tagrules = array('link' => $this->tagrules['link'], 'url' => $this->tagrules['url']);

        // Headers are considered tags here.
        $this->tagrules['header'] = array('expression' => "/<\s*h([1-$this->maxheaderdepth])\s*>(.+?)<\/h[1-$this->maxheaderdepth]>/is"
        );
    }

    protected function before_parsing() {
        parent::before_parsing();

        $this->rules($this->string);
    }

    /**
     * Header tag rule
     * @param array $match Header regex match
     * @return string
     */
    protected function header_tag_rule($match) {
        return $this->generate_header($match[2], $match[1]);
    }

    /**
     * Section editing: Special for HTML Parser (It parses <h1></h1>)
     */

    public function get_section($header, $text, $clean = false) {
        if ($clean) {
            $text = preg_replace('/\r\n/', "\n", $text);
            $text = preg_replace('/\r/', "\n", $text);
            $text .= "\n\n";
        }

        $h1 = array("<\s*h1\s*>", "<\/h1>");

        preg_match("/(.*?)({$h1[0]}\s*\Q$header\E\s*{$h1[1]}.*?)((?:\n{$h1[0]}.*)|$)/is", $text, $match);

        if (!empty($match)) {
            return array($match[1], $match[2], $match[3]);
        } else {
            return false;
        }
    }

    protected function get_repeated_sections(&$text, $repeated = array()) {
        $this->repeated_sections = $repeated;
        return preg_replace_callback($this->tagrules['header'], array($this, 'get_repeated_sections_callback'), $text);
    }

    protected function get_repeated_sections_callback($match) {
        $text = trim($match[2]);

        if (in_array($text, $this->repeated_sections)) {
            $this->returnvalues['repeated_sections'][] = $text;
            return parser_utils::h('p', $text);
        } else {
            $this->repeated_sections[] = $text;
        }

        return $match[0];
    }
}
