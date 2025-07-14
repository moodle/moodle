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

    /** @var int Minimum level of the headers on the page (usually tinymce uses <h1> and atto <h3>)  */
    protected $minheaderlevel = null;

    public function __construct() {
        parent::__construct();
        // The order is important, headers should be parsed before links.
        $this->tagrules = array(
            // Headers are considered tags here.
            'header' => array(
                'expression' => "/<\s*h([1-6])\s*>(.+?)<\/h[1-6]>/is"
            ),
            'link' => $this->tagrules['link'],
            'url' => $this->tagrules['url']
        );
    }

    /**
     * Find minimum header level used on the page (<h1>, <h3>, ...)
     *
     * @param string $text
     * @return int
     */
    protected function find_min_header_level($text) {
        preg_match_all($this->tagrules['header']['expression'], $text, $matches);
        return !empty($matches[1]) ? min($matches[1]) : 1;
    }

    protected function before_parsing() {
        parent::before_parsing();

        $this->minheaderlevel = $this->find_min_header_level($this->string);

        // Protect all explicit links from further wiki parsing. The link text may contain another URL which would get
        // converted into another link via {@see nwiki_parser::$tagrules} 'url' element.
        if (preg_match_all('/<a\s[^>]+?>(.*?)<\/a>/is', $this->string, $matches)) {
            foreach (array_unique($matches[0]) as $match) {
                $this->string = str_replace($match, $this->protect($match), $this->string);
            }
        }

        $this->rules($this->string);
    }

    /**
     * Header tag rule
     * @param array $match Header regex match
     * @return string
     */
    protected function header_tag_rule($match) {
        return $this->generate_header($match[2], (int)$match[1] - $this->minheaderlevel + 1);
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

        $minheaderlevel = $this->find_min_header_level($text);

        $h1 = array("<\s*h{$minheaderlevel}\s*>", "<\/h{$minheaderlevel}>");

        $regex = "/(.*?)({$h1[0]}\s*".preg_quote($header, '/')."\s*{$h1[1]}.*?)((?:{$h1[0]}.*)|$)/is";
        preg_match($regex, $text, $match);

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
