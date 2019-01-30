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
 * Unit tests for the indicator API.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_indicator_max.php');
require_once(__DIR__ . '/fixtures/test_indicator_discrete.php');
require_once(__DIR__ . '/fixtures/test_indicator_min.php');

/**
 * Unit tests for the model.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analytics_indicator_testcase extends advanced_testcase {

    /**
     * test_validate_calculated_value
     *
     * @param string $indicatorclass
     * @param array $returnedvalue
     * @dataProvider validate_calculated_value
     * @return null
     */
    public function test_validate_calculated_value($indicatorclass, $returnedvalue) {
        $indicator = new $indicatorclass();
        list($values, $unused) = $indicator->calculate([1], 'notrelevanthere');
        $this->assertEquals($returnedvalue, $values[0]);
    }

    /**
     * Data provider for test_validate_calculated_value
     *
     * @return array
     */
    public function validate_calculated_value() {
        return [
            'max' => ['test_indicator_max', [1]],
            'min' => ['test_indicator_min', [-1]],
            'discrete' => ['test_indicator_discrete', [0, 0, 0, 0, 1]],
        ];
    }

    /**
     * test_validate_calculated_value_exceptions
     *
     * @param string $indicatorclass
     * @param string $willreturn
     * @dataProvider validate_calculated_value_exceptions
     * @expectedException \coding_exception
     * @return null
     */
    public function test_validate_calculated_value_exceptions($indicatorclass, $willreturn) {

        $indicator = new $indicatorclass();
        $indicatormock = $this->getMockBuilder(get_class($indicator))
            ->setMethods(['calculate_sample'])
            ->getMock();
        $indicatormock->method('calculate_sample')->willReturn($willreturn);
        list($values, $unused) = $indicatormock->calculate([1], 'notrelevanthere');

    }

    /**
     * Data provider for test_validate_calculated_value_exceptions
     *
     * @return array
     */
    public function validate_calculated_value_exceptions() {
        return [
            'max' => ['test_indicator_max', 2],
            'min' => ['test_indicator_min', -2],
            'discrete' => ['test_indicator_discrete', 7],
        ];
    }
}
