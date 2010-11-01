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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the wiki parser
 *
 * @package mod-wiki-2.0
 * @copyrigth 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyrigth 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Kenneth Riba
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/mod/wiki/parser/parser.php');

class wikiparser_test extends UnitTestCase {

    private $test_directory;

    function setUp() {
        global $CFG;
        $this->test_directory = $CFG->dirroot . '/mod/wiki/simpletest/';
    }

    function testCreoleMarkup() {
        $this->assertTestFiles('creole');
    }

    function testNwikiMarkup() {
        $this->assertTestFiles('nwiki');
    }

    function testHtmlMarkup() {
        $this->assertTestFiles('html');
    }

    private function assertTestFile($num, $markup) {
        if(!file_exists($this->test_directory."input/$markup/$num") || !file_exists($this->test_directory."output/$markup/$num")) {
            return false;
        }
        $input = file_get_contents($this->test_directory."input/$markup/$num");
        $output = file_get_contents($this->test_directory."output/$markup/$num");

        $result = wiki_parser_proxy::parse($input, $markup, array('pretty_print' => true));

        //removes line breaks to avoid line break encoding causing tests to fail.
        $result['parsed_text'] = preg_replace('~[\r\n]~', '', $result['parsed_text']);
        $output                = preg_replace('~[\r\n]~', '', $output);

        $this->assertEqual($result['parsed_text'], $output);
        return true;
    }

    private function assertTestFiles($markup) {
        $i = 1;
        while($this->assertTestFile($i, $markup)) {
            $i++;
        }
    }
}
