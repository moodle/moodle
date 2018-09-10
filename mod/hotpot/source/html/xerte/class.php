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
 * Class to represent the source of a HotPot quiz
 * Source type: html_xerte
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/source/html/class.php');

/**
 * hotpot_source_html_xerte
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_source_html_xerte extends hotpot_source_html {
    // properties of the icon for this source file type
    var $icon = 'mod/hotpot/file/html/xerte/icon.gif';

    // xmlized content of template.xml
    var $template_xml = null;

    /**
     * is_quizfile
     * returns hotpot_file object if $filename is a quiz file, or false otherwise
     *
     * @return xxx
     */
    static public function is_quizfile($sourcefile) {
        if (! preg_match('/\.html?$/', $sourcefile->get_filename())) {
            // not an html file
            return false;
        }
        if (! $content = self::get_content($sourcefile)) {
            // empty or non-existant file
            return false;
        }
        if (! preg_match('/<script[^>]*src\s*=\s*"[^"]*rloObject.js"[^>]*>/', $content)) {
            return false;
        }
        if (! preg_match("/myRLO = new rloObject\('\d*','\d*','[^']*.rl[ot]'[^)]*\)/", $content)) {
            return false;
        }
        return true;
    }

    /**
     * get_template_xml
     *
     * @return xxx
     */
    function get_template_xml() {
        if (is_null($this->template_xml)) {
            $this->template_xml = $this->get_sibling_filecontents('template.xml', true);
        }
        return $this->template_xml;
    }

    /**
     * get_template_value
     *
     * @param xxx $tags
     * @param xxx $default (optional, default=null)
     * @return xxx
     */
    function get_template_value($tags, $default=null) {
        $value = $this->get_template_xml();
        foreach($tags as $tag) {
            if (! is_array($value)) {
                return $default;
            }
            if(! array_key_exists($tag, $value)) {
                return $default;
            }
            $value = $value[$tag];
        }
        return $value;
    }

    /**
     * get_name
     *
     * @return xxx
     */
    function get_name() {
        if ($name = $this->get_template_value(array('learningObject', '@', 'name'))) {
            return $name;
        }
        return parent::get_name();
    }

    /**
     * get_displayMode
     *
     * @return xxx
     */
    function get_displayMode() {
        return $this->get_template_value(array('learningObject', '@', 'displayMode'), 'default');
    }

    /**
     * get_entrytext - returns the introduction text for a quiz
     *
     * @return xxx
     */
    function get_entrytext() {
        return '';
    }
} // end class
