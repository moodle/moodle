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
 * TinyMCE tests.
 *
 * @package   editor_tinymce
 * @category  phpunit
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * TinyMCE tests.
 *
 * @package   editor_tinymce
 * @category  phpunit
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_tinymce_testcase extends advanced_testcase {

    public function test_toolbar_parsing() {
        global $CFG;
        require_once("$CFG->dirroot/lib/editorlib.php");
        require_once("$CFG->dirroot/lib/editor/tinymce/lib.php");

        $result = tinymce_texteditor::parse_toolbar_setting("bold,italic\npreview");
        $this->assertSame(array('bold,italic', 'preview'), $result);

        $result = tinymce_texteditor::parse_toolbar_setting("| bold,|italic*blink\rpreview\n\n| \n paste STYLE | ");
        $this->assertSame(array('bold,|,italic,blink', 'preview', 'paste,style'), $result);

        $result = tinymce_texteditor::parse_toolbar_setting("| \n\n| \n \r");
        $this->assertSame(array(), $result);

        $result = tinymce_texteditor::parse_toolbar_setting("one\ntwo\n\nthree\nfour\nfive\nsix\nseven\neight\nnine\nten");
        $this->assertSame(array('one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'), $result);
    }
}
