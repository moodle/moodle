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

namespace core_course;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest.php');
require_once($CFG->dirroot . '/course/format/lib.php');

/**
 * Course format function unit tests
 *
 * @package    core_course
 * @copyright  2021 Catalyst IT Pty Ltd
 * @author     Jason den Dulk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_format_function_test extends \basic_testcase {

    /**
     * Tests clean_param_if_not_null function
     * @covers ::clean_param_if_not_null
     */
    public function test_clean_param_if_not_null() {
        $this->assertNull(clean_param_if_not_null(null));
        $n = '3x';
        $this->assertEquals(clean_param($n, PARAM_INT), clean_param_if_not_null($n, PARAM_INT));
        $this->assertEquals(clean_param($n, PARAM_RAW), clean_param_if_not_null($n, PARAM_RAW));
        $this->assertEquals(clean_param($n, PARAM_ALPHANUM), clean_param_if_not_null($n, PARAM_ALPHANUM));
        $this->assertEquals(clean_param($n, PARAM_ALPHA), clean_param_if_not_null($n, PARAM_ALPHA));
        $s = '<abc>xyz</abc>';
        $this->assertEquals(clean_param($s, PARAM_ALPHANUM), clean_param_if_not_null($s, PARAM_ALPHANUM));
        $this->assertEquals(clean_param($s, PARAM_RAW), clean_param_if_not_null($s, PARAM_RAW));
    }

    /**
     * Tests contract_value function
     * @covers ::contract_value
     */
    public function test_contract_value() {
        $input = [
            'abc' => '<p>All together Now</p>',
            'abcformat' => '1',
            'jolly' => 'Roger'
        ];
        $expected = [
            'abc_editor' => [ 'text' => $input['abc'], 'format' => $input['abcformat'] ],
            'jolly' => $input['jolly'],
        ];
        $defs = [
            'abc_editor' => [],
            'jolly' => [ 'type' => PARAM_ALPHA ],
        ];
        $dest = [];

        foreach ($defs as $name => $def) {
            contract_value($dest, $input, $def, $name);
        }

        $this->assertEquals($expected, $dest);
    }

    /**
     * Tests expand_value function
     * @covers ::expand_value
     */
    public function test_expand_value() {
        $input = [
            'abc_editor' => [ 'text' => '<p>All together Now</p>', 'format' => '1' ],
            'jolly' => 'Roger',
        ];
        $expected = [
            'abc' => $input['abc_editor']['text'],
            'abcformat' => $input['abc_editor']['format'],
            'jolly' => $input['jolly'],
        ];
        $defs = [
            'abc_editor' => [],
            'jolly' => [ 'type' => PARAM_ALPHA ],
        ];
        $dest = [];

        foreach ($defs as $name => $def) {
            expand_value($dest, $input, $def, $name);
        }

        $this->assertEquals($expected, $dest);
    }
}
