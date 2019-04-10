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

require_once(__DIR__ . '/../../analytics/tests/fixtures/test_timesplitting_seconds.php');
require_once(__DIR__ . '/../../analytics/tests/fixtures/test_timesplitting_weekly.php');
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
        $this->course = $this->getDataGenerator()->create_course($params);
        $this->analysable = new \core_analytics\course($this->course);
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
            '\core\analytics\time_splitting\single_range',
            '\core\analytics\time_splitting\upcoming_week',
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
        $this->assertCount(4, $quarters->get_training_ranges());
        $this->assertCount(4, $quarters->get_distinct_ranges());

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
        $this->assertCount(4, $accum->get_training_ranges());
        $this->assertCount(4, $accum->get_distinct_ranges());

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

    /**
     * test_periodic
     *
     * @return void
     */
    public function test_periodic() {

        // Using a finished course.

        $weekly = new test_timesplitting_weekly();
        $weekly->set_analysable($this->analysable);
        $this->assertCount(1, $weekly->get_distinct_ranges());

        $ranges = $weekly->get_all_ranges();
        $this->assertEquals(52, count($ranges));
        $this->assertEquals($this->course->startdate, $ranges[0]['start']);
        $this->assertNotEquals($this->course->startdate, $ranges[0]['time']);

        // The analysable is finished so all ranges are available for training.
        $this->assertCount(count($ranges), $weekly->get_training_ranges());

        $ranges = $weekly->get_most_recent_prediction_range();
        $range = reset($ranges);
        $this->assertEquals(51, key($ranges));

        $upcomingweek = new \core\analytics\time_splitting\upcoming_week();
        $upcomingweek->set_analysable($this->analysable);
        $this->assertCount(1, $upcomingweek->get_distinct_ranges());

        $ranges = $upcomingweek->get_all_ranges();
        $this->assertEquals(53, count($ranges));
        $this->assertEquals($this->course->startdate, $ranges[0]['start']);
        $this->assertEquals($this->course->startdate, $ranges[0]['time']);

        $this->assertCount(count($ranges), $upcomingweek->get_training_ranges());

        $ranges = $upcomingweek->get_most_recent_prediction_range();
        $range = reset($ranges);
        $this->assertEquals(52, key($ranges));

        // We now use an ongoing course.

        $onemonthago = new DateTime('-30 days');
        $params = array(
            'startdate' => $onemonthago->getTimestamp(),
        );
        $ongoingcourse = $this->getDataGenerator()->create_course($params);
        $ongoinganalysable = new \core_analytics\course($ongoingcourse);

        $weekly = new test_timesplitting_weekly();
        $weekly->set_analysable($ongoinganalysable);
        $this->assertCount(1, $weekly->get_distinct_ranges());

        $ranges = $weekly->get_all_ranges();
        $this->assertEquals(4, count($ranges));
        $this->assertCount(4, $weekly->get_training_ranges());

        $ranges = $weekly->get_most_recent_prediction_range();
        $range = reset($ranges);
        $this->assertEquals(3, key($ranges));
        $this->assertLessThan(time(), $range['time']);
        $this->assertLessThan(time(), $range['start']);
        $this->assertLessThan(time(), $range['end']);

        $upcomingweek = new \core\analytics\time_splitting\upcoming_week();
        $upcomingweek->set_analysable($ongoinganalysable);
        $this->assertCount(1, $upcomingweek->get_distinct_ranges());

        $ranges = $upcomingweek->get_all_ranges();
        $this->assertEquals(5, count($ranges));
        $this->assertCount(4, $upcomingweek->get_training_ranges());

        $ranges = $upcomingweek->get_most_recent_prediction_range();
        $range = reset($ranges);
        $this->assertEquals(4, key($ranges));
        $this->assertLessThan(time(), $range['time']);
        $this->assertLessThan(time(), $range['start']);
        $this->assertGreaterThan(time(), $range['end']);

        // We now check how new ranges get added as time passes.

        $fewsecsago = new DateTime('-5 seconds');
        $params = array(
            'startdate' => $fewsecsago->getTimestamp(),
            'enddate' => (new DateTimeImmutable('+1 year'))->getTimestamp(),
        );
        $course = $this->getDataGenerator()->create_course($params);
        $analysable = new \core_analytics\course($course);

        $seconds = new test_timesplitting_seconds();
        $seconds->set_analysable($analysable);

        // Store the ranges we just obtained.
        $nranges = count($seconds->get_all_ranges());
        $ntrainingranges = count($seconds->get_training_ranges());
        $mostrecentrange = $seconds->get_most_recent_prediction_range();
        $mostrecentrange = reset($mostrecentrange);

        // We wait for the next range to be added.
        usleep(1000000);

        $seconds->set_analysable($analysable);
        $nnewranges = $seconds->get_all_ranges();
        $nnewtrainingranges = $seconds->get_training_ranges();
        $newmostrecentrange = $seconds->get_most_recent_prediction_range();
        $newmostrecentrange = reset($newmostrecentrange);
        $this->assertGreaterThan($nranges, $nnewranges);
        $this->assertGreaterThan($ntrainingranges, $nnewtrainingranges);
        $this->assertGreaterThan($mostrecentrange['time'], $newmostrecentrange['time']);
    }
}
