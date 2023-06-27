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

/**
 * Library of functions and constants for module wiki
 *
 * It contains the great majority of functions defined by Moodle
 * that are mandatory to develop a module.
 *
 * @package mod_wiki
 * @copyright 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Kenneth Riba
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generic parser implementation
 *
 * @author Josep Arús
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_wiki
 */
class wiki_parser_proxy {
    private static $parsers = array();
    private static $basepath = "";

    public static function parse(&$string, $type, $options = array()) {

        if (empty(self::$basepath)) {
            global $CFG;
            self::$basepath = $CFG->dirroot . '/mod/wiki/parser/';
        }

        $type = strtolower($type);
        self::$parsers[$type] = null; // Reset the current parser because it may have other options.
        if (self::create_parser_instance($type)) {
            return self::$parsers[$type]->parse($string, $options);
        } else {
            return false;
        }
    }

    public static function get_token($name, $type) {
        if (self::create_parser_instance($type)) {
            return self::$parsers[$type]->get_token($name);
        } else {
            return false;
        }
    }

    public static function get_section(&$string, $type, $section, $allcontent = false) {
        if (self::create_parser_instance($type)) {
            $content = self::$parsers[$type]->get_section($section, $string, true);

            if ($allcontent) {
                return $content;
            } else {
                return is_array($content) ? $content[1] : null;
            }
        } else {
            return false;
        }
    }

    private static function create_parser_instance($type) {
        if (empty(self::$parsers[$type])) {
            include_once(self::$basepath . "markups/$type.php");
            $class = strtolower($type) . "_parser";
            if (class_exists($class)) {
                self::$parsers[$type] = new $class;
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}

require_once('utils.php');

abstract class generic_parser {
    protected $string;

    protected $blockrules = array();
    protected $tagrules = array();

    private $rulestack = array();

    protected $parserstatus = 'Before';

    /**
     * Dynamic return values
     */

    protected $returnvalues = array();

    private $nowikiindex = array();

    protected $nowikitoken = "%!";

    public function __construct() {
    }

    /**
     * Parse function
     */
    public function parse(&$string, $options = array()) {
        if (!is_string($string)) {
            return false;
        }

        $this->string =& $string;

        $this->set_options(is_array($options) ? $options : array());

        $this->initialize_nowiki_index();

        if (method_exists($this, 'before_parsing')) {
            $this->before_parsing();
        }

        $this->parserstatus = 'Parsing';

        foreach ($this->blockrules as $name => $block) {
            $this->process_block_rule($name, $block);
        }

        $this->commit_nowiki_index();

        $this->parserstatus = 'After';

        if (method_exists($this, 'after_parsing')) {
            $this->after_parsing();
        }

        return array('parsed_text' => $this->string) + $this->returnvalues;
    }

    /**
     * Initialize options
     */
    protected function set_options($options) {
    }

    /**
     * Block processing function & callbacks
     */
    protected function process_block_rule($name, $block) {
        $this->rulestack[] = array('callback' => method_exists($this, $name . "_block_rule") ? $name . "_block_rule" : null,
            'rule' => $block);

        $this->string = preg_replace_callback($block['expression'], array($this, 'block_callback'), $this->string);

        array_pop($this->rulestack);
    }

    private function block_callback($match) {
        $rule = end($this->rulestack);
        if (!empty($rule['callback'])) {
            $stuff = $this->{$rule['callback']}($match);
        } else {
            $stuff = $match[1];
        }

        if (is_array($stuff) && $rule['rule']['tag']) {
            $this->rules($stuff[0], $rule['rule']['tags']);
            $stuff = "\n" . parser_utils::h($rule['rule']['tag'], $stuff[0], $stuff[1]) . "\n";
        } else {
            if (!isset($rule['rule']['tags'])) {
                $rule['rule']['tags'] = null;
            }
            $this->rules($stuff, $rule['rule']['tags']);
            if (isset($rule['rule']['tag']) && is_string($rule['rule']['tag'])) {
                $stuff = "\n" . parser_utils::h($rule['rule']['tag'], $stuff) . "\n";
            }
        }

        return $stuff;
    }

    /**
     * Rules processing function & callback
     */

    protected final function rules(&$text, $rules = null) {
        if ($rules === null) {
            $rules = array('except' => array());
        } else if (is_array($rules) && count($rules) > 1) {
            $rules = array('only' => $rules);
        }

        if (isset($rules['only']) && is_array($rules['only'])) {
            $rules = $rules['only'];
            foreach ($rules as $r) {
                if (!empty($this->tagrules[$r])) {
                    $this->process_tag_rule($r, $this->tagrules[$r], $text);
                }
            }
        } else if (isset($rules['except']) && is_array($rules['except'])) {
            $rules = $rules['except'];
            foreach ($this->tagrules as $r => $tr) {
                if (!in_array($r, $rules)) {
                    $this->process_tag_rule($r, $tr, $text);
                }
            }
        }
    }

    private function process_tag_rule($name, $rule, &$text) {
        if (method_exists($this, $name . "_tag_rule")) {
            $this->rulestack[] = array('callback' => $name . "_tag_rule", 'rule' => $rule);
            $text = preg_replace_callback($rule['expression'], array($this, 'tag_callback'), $text);
            array_pop($this->rulestack);
        } else {
            if (isset($rule['simple'])) {
                $replace = "<{$rule['tag']} />";
            } else {
                $replace = parser_utils::h($rule['tag'], "$1");
            }

            $text = preg_replace($rule['expression'], $replace, $text);
        }
    }

    private function tag_callback($match) {
        $rule = end($this->rulestack);
        $stuff = $this->{$rule['callback']}($match);

        if (is_array($stuff)) {
            return parser_utils::h($rule['rule']['tag'], $stuff[0], $stuff[1]);
        } else {
            return $stuff;
        }
    }

    /**
     * Special nowiki parser index
     */

    private function initialize_nowiki_index() {
        $token = "\Q" . $this->nowikitoken . "\E";
        $this->string = preg_replace_callback("/" . $token . "\d+" . $token . "/",
            array($this, "initialize_nowiki_index_callback"), $this->string);
    }

    private function initialize_nowiki_index_callback($match) {
        return $this->protect($match[0]);
    }

    protected function protect($text) {
        $this->nowikiindex[] = $text;

        return $this->nowikitoken . (count($this->nowikiindex) - 1) . $this->nowikitoken;
    }

    private function commit_nowiki_index() {
        $token = "\Q" . $this->nowikitoken . "\E";
        $this->string = preg_replace_callback("/" . $token . "(\d+)" . $token . "/",
            array($this, "commit_nowiki_index_callback"), $this->string);
    }

    private function commit_nowiki_index_callback($match) {
        return $this->nowikiindex[intval($match[1])];
    }

    /**
     * Get token of the parsable element $name.
     */
    public function get_token($name) {
        foreach (array_merge($this->blockrules, $this->tagrules) as $n => $v) {
            if ($name == $n && isset($v['token'])) {
                return $v['token'] ? $v['token'] : false;
            }
        }

        return false;
    }
}
