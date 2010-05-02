<?php

/**
 * HTML parser implementation. It only implements links.
 *
 * @author Josep Arús
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package wiki
 */
include_once("nwiki.php");

class html_parser extends nwiki_parser {
    protected $blockrules = array();

    protected $section_editing = true;

    public function __construct() {
        parent::__construct();
        $this->tagrules = array('link' => $this->tagrules['link'], 'url' => $this->tagrules['url']);

        //headers are considered tags here...
        $h1 = array("<\s*h1\s*>", "<\/h1>");
        $this->tagrules['header1'] = array('expression' => "/{$h1[0]}(.+?){$h1[1]}/is"
        );
    }

    protected function before_parsing() {
        parent::before_parsing();

        $this->rules($this->string);
    }

    /**
     * Header 1 tag rule
     */
    protected function header1_tag_rule($match) {
        return $this->generate_header($match[1], 1);
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
        return preg_replace_callback($this->tagrules['header1'], array($this, 'get_repeated_sections_callback'), $text);
    }

    protected function get_repeated_sections_callback($match) {
        $text = trim($match[1]);

        if (in_array($text, $this->repeated_sections)) {
            $this->returnvalues['repeated_sections'][] = $text;
            return parser_utils::h('p', $text);
        } else {
            $this->repeated_sections[] = $text;
        }

        return $match[0];
    }
}
