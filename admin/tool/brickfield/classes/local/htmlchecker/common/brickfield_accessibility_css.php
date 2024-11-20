<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tool_brickfield\local\htmlchecker\common;

/**
 * Parse content to check CSS validity.
 *
 * This class first parses all the CSS in the document and prepares an index of CSS styles to be used by accessibility tests
 * to determine color and positioning.
 *
 * First, in loadCSS we get all the inline and linked style sheet information and merge it into a large CSS file string.
 *
 * Second, in setStyles we use XPath queries to find all the DOM elements which are effected by CSS styles and then
 * build up an index in style_index of all the CSS styles keyed by an attriute we attach to all DOM objects to lookup
 * the style quickly.
 *
 * Most of the second step is to get around the problem where XPath DOMNodeList objects are only marginally referential
 * to the original elements and cannot be altered directly.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class brickfield_accessibility_css {

    /** @var object The DOMDocument object of the current document */
    public $dom;

    /** @var string The URI of the current document */
    public $uri;

    /** @var string The type of request (inherited from the main htmlchecker object) */
    public $type;

    /** @var array An array of all the CSS elements and attributes */
    public $css;

    /** @var string Additional CSS information (usually for CMS mode requests) */
    public $cssstring;

    /** @var bool Whether or not we are running in CMS mode */
    public $cmsmode;

    /** @var array An array of all the strings which means the current style inherts from above */
    public $inheritancestrings = ['inherit', 'currentColor'];

    /** @var array An array of all the styles keyed by the new attribute brickfield_accessibility_style_index */
    public $styleindex = [];

    /** @var int The next index ID to be applied to a node to lookup later in style_index */
    public $nextindex = 0;

    /** @var array A list of all the elements which support deprecated styles such as 'background' or 'bgcolor' */
    public $deprecatedstyleelements = ['body', 'table', 'tr', 'td', 'th'];

    /** @var array */
    public array $path = [];

    /** @var array To store additional CSS files to load. */
    public array $css_files = [];

    /**
     * Class constructor. We are just building and importing variables here and then loading the CSS
     * @param \DOMDocument $dom The DOMDocument object
     * @param string $uri The URI of the request
     * @param string $type The type of request
     * @param array $path
     * @param bool $cmsmode Whether we are running in CMS mode
     * @param array $cssfiles An array of additional CSS files to load
     */
    public function __construct(\DOMDocument &$dom, string $uri, string $type, array $path, bool $cmsmode = false,
                                array $cssfiles = []) {
        $this->dom =& $dom;
        $this->type = $type;
        $this->uri = $uri;
        $this->path = $path;
        $this->cmsmode = $cmsmode;
        $this->css_files = $cssfiles;
    }

    /**
     * Loads all the CSS files from the document using LINK elements or @import commands
     */
    private function load_css() {
        if (count($this->css_files) > 0) {
            $css = $this->css_files;
        } else {
            $css = [];
            $headerstyles = $this->dom->getElementsByTagName('style');
            foreach ($headerstyles as $headerstyle) {
                if ($headerstyle->nodeValue) {
                    $this->cssstring .= $headerstyle->nodeValue;
                }
            }
            $stylesheets = $this->dom->getElementsByTagName('link');

            foreach ($stylesheets as $style) {
                if ($style->hasAttribute('rel') &&
                    (strtolower($style->getAttribute('rel')) == 'stylesheet') &&
                    ($style->getAttribute('media') != 'print')) {
                        $css[] = $style->getAttribute('href');
                }
            }
        }
        foreach ($css as $sheet) {
            $this->load_uri($sheet);
        }
        $this->load_imported_files();
        $this->cssstring = str_replace(':link', '', $this->cssstring);
        $this->format_css();
    }

    /**
     * Imports files from the CSS file using @import commands
     */
    private function load_imported_files() {
        $matches = [];
        preg_match_all('/@import (.*?);/i', $this->cssstring, $matches);
        if (count($matches[1]) == 0) {
            return null;
        }
        foreach ($matches[1] as $match) {
            $this->load_uri(trim(str_replace('url', '', $match), '"\')('));
        }
        preg_replace('/@import (.*?);/i', '', $this->cssstring);
    }

    /**
     * Returns a specificity count to the given selector.
     * Higher specificity means it overrides other styles.
     * @param string $selector The CSS Selector
     * @return int $specifity
     */
    public function get_specificity(string $selector): int {
        $selector = $this->parse_selector($selector);
        if ($selector[0][0] == ' ') {
            unset($selector[0][0]);
        }
        $selector = $selector[0];
        $specificity = 0;
        foreach ($selector as $part) {
            switch(substr(str_replace('*', '', $part), 0, 1)) {
                case '.':
                    $specificity += 10;
                case '#':
                    $specificity += 100;
                case ':':
                    $specificity++;
                default:
                    $specificity++;
            }
            if (strpos($part, '[id=') != false) {
                $specificity += 100;
            }
        }
        return $specificity;
    }

    /**
     * Interface method for tests to call to lookup the style information for a given DOMNode
     * @param \stdClass $element A DOMElement/DOMNode object
     * @return array An array of style information (can be empty)
     */
    public function get_style($element): array {
        // To prevent having to parse CSS unless the info is needed,
        // we check here if CSS has been set, and if not, run off the parsing now.
        if (!is_a($element, 'DOMElement')) {
            return [];
        }
        $style = $this->get_node_style($element);
        if (isset($style['background-color']) || isset($style['color'])) {
            $style = $this->walkup_tree_for_inheritance($element, $style);
        }
        if ($element->hasAttribute('style')) {
            $inlinestyles = explode(';', $element->getAttribute('style'));
            foreach ($inlinestyles as $inlinestyle) {
                $s = explode(':', $inlinestyle);

                if (isset($s[1])) {    // Edit:  Make sure the style attribute doesn't have a trailing.
                    $style[trim($s[0])] = trim(strtolower($s[1]));
                }
            }
        }
        if ($element->tagName === 'strong') {
            $style['font-weight'] = 'bold';
        }
        if ($element->tagName === 'em') {
            $style['font-style'] = 'italic';
        }
        if (!is_array($style)) {
            return [];
        }
        return $style;
    }

    /**
     * Adds a selector to the CSS index
     * @param string $key The CSS selector
     * @param string $codestr The CSS Style code string
     * @return null
     */
    private function add_selector(string $key, string $codestr) {
        if (strpos($key, '@import') !== false) {
            return null;
        }
        $key = strtolower($key);
        $codestr = strtolower($codestr);
        if (!isset($this->css[$key])) {
            $this->css[$key] = array();
        }
        $codes = explode(';', $codestr);
        if (count($codes) > 0) {
            foreach ($codes as $code) {
                $code = trim($code);
                $explode = explode(':', $code, 2);
                if (count($explode) > 1) {
                    list($codekey, $codevalue) = $explode;
                    if (strlen($codekey) > 0) {
                        $this->css[$key][trim($codekey)] = trim($codevalue);
                    }
                }
            }
        }
    }

    /**
     * Returns the style from the CSS index for a given element by first
     * looking into its tag bucket then iterating over every item for an
     * element that matches
     * @param \stdClass $element
     * @return array An array of all the style elements that _directly_ apply to that element (ignoring inheritance)
     */
    private function get_node_style($element): array {
        $style = [];

        if ($element->hasAttribute('brickfield_accessibility_style_index')) {
            $style = $this->styleindex[$element->getAttribute('brickfield_accessibility_style_index')];
        }
        // To support the deprecated 'bgcolor' attribute.
        if ($element->hasAttribute('bgcolor') &&  in_array($element->tagName, $this->deprecatedstyleelements)) {
            $style['background-color'] = $element->getAttribute('bgcolor');
        }
        if ($element->hasAttribute('style')) {
            $inlinestyles = explode(';', $element->getAttribute('style'));
            foreach ($inlinestyles as $inlinestyle) {
                $s = explode(':', $inlinestyle);
                if (isset($s[1])) {    // Edit:  Make sure the style attribute doesn't have a trailing.
                    $style[trim($s[0])] = trim(strtolower($s[1]));
                }
            }
        }

        return $style;
    }

    /**
     * A helper function to walk up the DOM tree to the end to build an array of styles.
     * @param \stdClass $element The DOMNode object to walk up from
     * @param array $style The current style built for the node
     * @return array The array of the DOM element, altered if it was overruled through css inheritance
     */
    private function walkup_tree_for_inheritance($element, array $style): array {
        while (property_exists($element->parentNode, 'tagName')) {
            $parentstyle = $this->get_node_style($element->parentNode);
            if (is_array($parentstyle)) {
                foreach ($parentstyle as $k => $v) {
                    if (!isset($style[$k])) {
                        $style[$k] = $v;
                    }

                    if ((!isset($style['background-color'])) || strtolower($style['background-color']) == strtolower("#FFFFFF")) {
                        if ($k == 'background-color') {
                            $style['background-color'] = $v;
                        }
                    }

                    if ((!isset($style['color'])) || strtolower($style['color']) == strtolower("#000000")) {
                        if ($k == 'color') {
                            $style['color'] = $v;
                        }
                    }
                }
            }
            $element = $element->parentNode;
        }
        return $style;
    }

    /**
     * Loads a CSS file from a URI
     * @param string $rel The URI of the CSS file
     */
    private function load_uri(string $rel) {
        if ($this->type == 'file') {
            $uri = substr($this->uri, 0, strrpos($this->uri, '/')) .'/'.$rel;
        } else {
            $bfao = new \tool_brickfield\local\htmlchecker\brickfield_accessibility();
            $uri = $bfao->get_absolute_path($this->uri, $rel);
        }
        $this->cssstring .= @file_get_contents($uri);

    }

    /**
     * Formats the CSS to be ready to import into an array of styles
     * @return bool Whether there were elements imported or not
     */
    private function format_css(): bool {
        // Remove comments.
        $str = preg_replace("/\/\*(.*)?\*\//Usi", "", $this->cssstring);
        // Parse this csscode.
        $parts = explode("}", $str);
        if (count($parts) > 0) {
            foreach ($parts as $part) {
                if (strpos($part, '{') !== false) {
                    list($keystr, $codestr) = explode("{", $part);
                    $keys = explode(", ", trim($keystr));
                    if (count($keys) > 0) {
                        foreach ($keys as $key) {
                            if (strlen($key) > 0) {
                                $key = str_replace("\n", "", $key);
                                $key = str_replace("\\", "", $key);
                                $this->add_selector($key, trim($codestr));
                            }
                        }
                    }
                }
            }
        }
        return (count($this->css) > 0);
    }

    /**
     * Converts a CSS selector to an Xpath query
     * @param string $selector The selector to convert
     * @return string An Xpath query string
     */
    private function get_xpath(string $selector): string {
        $query = $this->parse_selector($selector);

        $xpath = '//';
        foreach ($query[0] as $k => $q) {
            if ($q == ' ' && $k) {
                $xpath .= '//';
            } else if ($q == '>' && $k) {
                $xpath .= '/';
            } else if (substr($q, 0, 1) == '#') {
                $xpath .= '[ @id = "' . str_replace('#', '', $q) . '" ]';
            } else if (substr($q, 0, 1) == '.') {
                $xpath .= '[ @class = "' . str_replace('.', '', $q) . '" ]';
            } else if (substr($q, 0, 1) == '[') {
                $xpath .= str_replace('[id', '[ @ id', $q);
            } else {
                $xpath .= trim($q);
            }
        }
        return str_replace('//[', '//*[', str_replace('//[ @', '//*[ @', $xpath));
    }

    /**
     * Checks that a string is really a regular character
     * @param string $char The character
     * @return bool Whether the string is a character
     */
    private function is_char(string $char): bool {
        return extension_loaded('mbstring') ? mb_eregi('\w', $char) : preg_match('@\w@', $char);
    }

    /**
     * Parses a CSS selector into an array of rules.
     * @param string $query The CSS Selector query
     * @return array An array of the CSS Selector parsed into rule segments
     */
    private function parse_selector(string $query): array {
        // Clean spaces.
        $query = trim(preg_replace('@\s+@', ' ', preg_replace('@\s*(>|\\+|~)\s*@', '\\1', $query)));
        $queries = [[]];
        if (!$query) {
            return $queries;
        }
        $return =& $queries[0];
        $specialchars = ['>', ' '];
        $specialcharsmapping = [];
        $strlen = mb_strlen($query);
        $classchars = ['.', '-'];
        $pseudochars = ['-'];
        $tagchars = ['*', '|', '-'];
        // Split multibyte string
        // http://code.google.com/p/phpquery/issues/detail?id=76.
        $newquery = [];
        for ($i = 0; $i < $strlen; $i++) {
            $newquery[] = mb_substr($query, $i, 1);
        }
        $query = $newquery;
        // It works, but i dont like it...
        $i = 0;
        while ($i < $strlen) {
            $c = $query[$i];
            $tmp = '';
            // TAG.
            if ($this->is_char($c) || in_array($c, $tagchars)) {
                while (isset($query[$i]) && ($this->is_char($query[$i]) || in_array($query[$i], $tagchars))) {
                    $tmp .= $query[$i];
                    $i++;
                }
                $return[] = $tmp;
                // IDs.
            } else if ( $c == '#') {
                $i++;
                while (isset($query[$i]) && ($this->is_char($query[$i]) || $query[$i] == '-')) {
                    $tmp .= $query[$i];
                    $i++;
                }
                $return[] = '#'.$tmp;
                // SPECIAL CHARS.
            } else if (in_array($c, $specialchars)) {
                $return[] = $c;
                $i++;
                // MAPPED SPECIAL CHARS.
            } else if ( isset($specialcharsmapping[$c])) {
                $return[] = $specialcharsmapping[$c];
                $i++;
                // COMMA.
            } else if ( $c == ',') {
                $queries[] = [];
                $return =& $queries[count($queries) - 1];
                $i++;
                while (isset($query[$i]) && $query[$i] == ' ') {
                    $i++;
                }
                // CLASSES.
            } else if ($c == '.') {
                while (isset($query[$i]) && ($this->is_char($query[$i]) || in_array($query[$i], $classchars))) {
                    $tmp .= $query[$i];
                    $i++;
                }
                $return[] = $tmp;
                // General Sibling Selector.
            } else if ($c == '~') {
                $spaceallowed = true;
                $tmp .= $query[$i++];
                while (isset($query[$i])
                    && ($this->is_char($query[$i])
                        || in_array($query[$i], $classchars)
                        || $query[$i] == '*'
                        || ($query[$i] == ' ' && $spaceallowed)
                    )) {
                    if ($query[$i] != ' ') {
                        $spaceallowed = false;
                    }
                    $tmp .= $query[$i];
                    $i++;
                }
                $return[] = $tmp;
                // Adjacent sibling selectors.
            } else if ($c == '+') {
                $spaceallowed = true;
                $tmp .= $query[$i++];
                while (isset($query[$i])
                    && ($this->is_char($query[$i])
                        || in_array($query[$i], $classchars)
                        || $query[$i] == '*'
                        || ($spaceallowed && $query[$i] == ' ')
                    )) {
                    if ($query[$i] != ' ') {
                        $spaceallowed = false;
                    }
                    $tmp .= $query[$i];
                    $i++;
                }
                $return[] = $tmp;
                // ATTRS.
            } else if ($c == '[') {
                $stack = 1;
                $tmp .= $c;
                while (isset($query[++$i])) {
                    $tmp .= $query[$i];
                    if ( $query[$i] == '[') {
                        $stack++;
                    } else if ( $query[$i] == ']') {
                        $stack--;
                        if (!$stack) {
                            break;
                        }
                    }
                }
                $return[] = $tmp;
                $i++;
                // PSEUDO CLASSES.
            } else if ($c == ':') {
                $stack = 1;
                $tmp .= $query[$i++];
                while (isset($query[$i]) && ($this->is_char($query[$i]) || in_array($query[$i], $pseudochars))) {
                    $tmp .= $query[$i];
                    $i++;
                }
                // With arguments?
                if (isset($query[$i]) && $query[$i] == '(') {
                    $tmp .= $query[$i];
                    $stack = 1;
                    while (isset($query[++$i])) {
                        $tmp .= $query[$i];
                        if ( $query[$i] == '(') {
                            $stack++;
                        } else if ( $query[$i] == ')') {
                            $stack--;
                            if (!$stack) {
                                break;
                            }
                        }
                    }
                    $return[] = $tmp;
                    $i++;
                } else {
                    $return[] = $tmp;
                }
            } else {
                $i++;
            }
        }
        foreach ($queries as $k => $q) {
            if (isset($q[0])) {
                if (isset($q[0][0]) && $q[0][0] == ':') {
                    array_unshift($queries[$k], '*');
                }
                if ($q[0] != '>') {
                    array_unshift($queries[$k], ' ');
                }
            }
        }
        return $queries;
    }
}
