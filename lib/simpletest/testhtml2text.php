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
 * Tests our html2text hacks
 *
 * @package    core
 * @subpackage lib
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

class html2text_test extends UnitTestCase {

    public static $includecoverage = array('lib/html2text.php');

    /**
     * ALT as image replacements
     * @return void
     */
    public function test_images() {
        $text = 'xx<img src="gif.gif" alt="some gif" />xx';
        $result = html_to_text($text, null, false, false);
        $this->assertIdentical($result, 'xx[some gif]xx');
    }

    /**
     * no magic quotes messing
     * @return void
     */
    public function test_no_strip_slashes() {
        $text = '\\magic\\quotes\\are\\\\horrible';
        $result = html_to_text($text, null, false, false);
        $this->assertIdentical($result, $text);
    }

    /**
     * textlib integration
     * @return void
     */
    public function test_textlib() {
        $text = '<strong>Žluťoučký koníček</strong>';
        $result = html_to_text($text, null, false, false);
        $this->assertIdentical($result, 'ŽLUŤOUČKÝ KONÍČEK');
    }

    /**
     * protect 0
     * @return void
     */
    public function test_zero() {
        $text = '0';
        $result = html_to_text($text, null, false, false);
        $this->assertIdentical($result, $text);
    }

    /**
     * Various invalid HTML typed by users that ignore html strict
     **/
    public function test_invalid_html() {
        $text = 'Gin & Tonic';
        $result = html_to_text($text, null, false, false);
        $this->assertIdentical($result, $text);

        $text = 'Gin > Tonic';
        $result = html_to_text($text, null, false, false);
        $this->assertIdentical($result, $text);

        $text = 'Gin < Tonic';
        $result = html_to_text($text, null, false, false);
        $this->assertIdentical($result, $text);
    }
}

