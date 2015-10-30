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
 * This file contains tests for the question_bank class.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(dirname(__FILE__) . '/../lib.php');


/**
 *Unit tests for the {@link question_bank} class.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_bank_test extends advanced_testcase {

    public function test_sort_qtype_array() {
        $config = new stdClass();
        $config->multichoice_sortorder = '1';
        $config->calculated_sortorder = '2';
        $qtypes = array(
            'frog' => 'toad',
            'calculated' => 'newt',
            'multichoice' => 'eft',
        );
        $this->assertEquals(question_bank::sort_qtype_array($qtypes, $config), array(
            'multichoice' => 'eft',
            'calculated' => 'newt',
            'frog' => 'toad',
        ));
    }

    public function test_fraction_options() {
        $fractions = question_bank::fraction_options();
        $this->assertSame(get_string('none'), reset($fractions));
        $this->assertSame('0.0', key($fractions));
        $this->assertSame('5%', end($fractions));
        $this->assertSame('0.05', key($fractions));
        array_shift($fractions);
        array_pop($fractions);
        array_pop($fractions);
        $this->assertSame('100%', reset($fractions));
        $this->assertSame('1.0', key($fractions));
        $this->assertSame('11.11111%', end($fractions));
        $this->assertSame('0.1111111', key($fractions));
    }

    public function test_fraction_options_full() {
        $fractions = question_bank::fraction_options_full();
        $this->assertSame(get_string('none'), reset($fractions));
        $this->assertSame('0.0', key($fractions));
        $this->assertSame('-100%', end($fractions));
        $this->assertSame('-1.0', key($fractions));
        array_shift($fractions);
        array_pop($fractions);
        array_pop($fractions);
        $this->assertSame('100%', reset($fractions));
        $this->assertSame('1.0', key($fractions));
        $this->assertSame('-83.33333%', end($fractions));
        $this->assertSame('-0.8333333', key($fractions));
    }
}
