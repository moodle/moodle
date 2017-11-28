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
 * Unit tests for core time splitting methods.
 *
 * @package   core
 * @category  analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../analytics/tests/fixtures/test_target_shortname.php');
require_once(__DIR__ . '/../../lib/enrollib.php');

/**
 * Unit tests for core time splitting methods.
 *
 * @package   core
 * @category  analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_analytics_time_splittings_testcase extends advanced_testcase {

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() {

        $this->resetAfterTest(true);

        // Generate training data.
        $params = array(
            'startdate' => mktime(8, 15, 32, 10, 24, 2015),
            'enddate' => mktime(12, 12, 31, 10, 24, 2016),
        );
        $course = $this->getDataGenerator()->create_course($params);
        $this->analysable = new \core_analytics\course($course);
    }

    /**
     * test_ranges
     *
     * @return void
     */
    public function test_valid_ranges() {

        // All core time splitting methods.
        $timesplittings = array(
            '\core\analytics\time_splitting\deciles',
            '\core\analytics\time_splitting\deciles_accum',
            '\core\analytics\time_splitting\no_splitting',
            '\core\analytics\time_splitting\quarters',
            '\core\analytics\time_splitting\quarters_accum',
            '\core\analytics\time_splitting\single_range'
        );

        // Check that defined ranges are valid (tested through validate_ranges).
        foreach ($timesplittings as $timesplitting) {
            $instance = new $timesplitting();
            $instance->set_analysable($this->analysable);
        }
    }

    /**
     * test_range_dates
     *
     * @return void
     */
    public function test_range_dates() {

        $nov2015 = mktime(0, 0, 0, 11, 24, 2015);
        $aug2016 = mktime(0, 0, 0, 8, 29, 2016);

        // Equal parts.
        $quarters = new \core\analytics\time_splitting\quarters();
        $quarters->set_analysable($this->analysable);
        $ranges = $quarters->get_all_ranges();
        $this->assertCount(4, $ranges);

        $this->assertGreaterThan($ranges[0]['start'], $ranges[1]['start']);
        $this->assertGreaterThan($ranges[0]['end'], $ranges[1]['start']);
        $this->assertGreaterThan($ranges[0]['end'], $ranges[1]['end']);

        $this->assertGreaterThan($ranges[1]['start'], $ranges[2]['start']);
        $this->assertGreaterThan($ranges[1]['end'], $ranges[2]['start']);
        $this->assertGreaterThan($ranges[1]['end'], $ranges[2]['end']);

        $this->assertGreaterThan($ranges[2]['start'], $ranges[3]['start']);
        $this->assertGreaterThan($ranges[2]['end'], $ranges[3]['end']);
        $this->assertGreaterThan($ranges[2]['end'], $ranges[3]['start']);

        // First range.
        $this->assertLessThan($nov2015, $ranges[0]['start']);
        $this->assertGreaterThan($nov2015, $ranges[0]['end']);

        // Last range.
        $this->assertLessThan($aug2016, $ranges[3]['start']);
        $this->assertGreaterThan($aug2016, $ranges[3]['end']);

        // Accumulative.
        $accum = new \core\analytics\time_splitting\quarters_accum();
        $accum->set_analysable($this->analysable);
        $ranges = $accum->get_all_ranges();
        $this->assertCount(4, $ranges);

        $this->assertEquals($ranges[0]['start'], $ranges[1]['start']);
        $this->assertEquals($ranges[1]['start'], $ranges[2]['start']);
        $this->assertEquals($ranges[2]['start'], $ranges[3]['start']);

        $this->assertGreaterThan($ranges[0]['end'], $ranges[1]['end']);
        $this->assertGreaterThan($ranges[1]['end'], $ranges[2]['end']);
        $this->assertGreaterThan($ranges[2]['end'], $ranges[3]['end']);

        // Present in all ranges.
        $this->assertLessThan($nov2015, $ranges[0]['start']);
        $this->assertGreaterThan($nov2015, $ranges[0]['end']);
        $this->assertGreaterThan($nov2015, $ranges[1]['end']);
        $this->assertGreaterThan($nov2015, $ranges[2]['end']);
        $this->assertGreaterThan($nov2015, $ranges[3]['end']);

        // Only in the last range.
        $this->assertLessThan($aug2016, $ranges[0]['end']);
        $this->assertLessThan($aug2016, $ranges[1]['end']);
        $this->assertLessThan($aug2016, $ranges[2]['end']);
        $this->assertLessThan($aug2016, $ranges[3]['start']);
        $this->assertGreaterThan($aug2016, $ranges[3]['end']);
    }

    /**
     * test_ready_predict
     *
     * @return void
     */
    public function test_ready_predict() {

        $quarters = new \core\analytics\time_splitting\quarters();
        $nosplitting = new \core\analytics\time_splitting\no_splitting();
        $singlerange = new \core\analytics\time_splitting\single_range();

        $range = array(
            'start' => time() - 100,
            'end' => time() - 20,
        );
        $range['time'] = $range['end'];
        $this->assertTrue($quarters->ready_to_predict($range));
        $this->assertTrue($nosplitting->ready_to_predict($range));

        // Single range time is 0.
        $range['time'] = 0;
        $this->assertTrue($singlerange->ready_to_predict($range));

        $range = array(
            'start' => time() + 20,
            'end' => time() + 100,
        );
        $range['time'] = $range['end'];
        $this->assertFalse($quarters->ready_to_predict($range));
        $this->assertTrue($nosplitting->ready_to_predict($range));

        // Single range time is 0.
        $range['time'] = 0;
        $this->assertTrue($singlerange->ready_to_predict($range));
    }
}
