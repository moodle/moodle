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

namespace core_analytics;

use test_timesplitting_seconds;
use test_timesplitting_upcoming_seconds;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_timesplitting_seconds.php');
require_once(__DIR__ . '/fixtures/test_timesplitting_upcoming_seconds.php');
require_once(__DIR__ . '/../../lib/enrollib.php');

/**
 * Unit tests for core time splitting methods.
 *
 * @package   core
 * @category  test
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class time_splittings_test extends \advanced_testcase {

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void {

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

        $pastweek = new \core\analytics\time_splitting\past_week();
        $pastweek->set_analysable($this->analysable);
        $this->assertCount(1, $pastweek->get_distinct_ranges());

        $ranges = $pastweek->get_all_ranges();
        $this->assertEquals(52, count($ranges));
        $this->assertEquals($this->course->startdate, $ranges[0]['start']);
        $this->assertNotEquals($this->course->startdate, $ranges[0]['time']);

        // The analysable is finished so all ranges are available for training.
        $this->assertCount(count($ranges), $pastweek->get_training_ranges());

        $ranges = $pastweek->get_most_recent_prediction_range();
        $range = reset($ranges);
        $this->assertEquals(51, key($ranges));

        // We now use an ongoing course not yet ready to generate predictions.

        $threedaysago = new \DateTime('-3 days');
        $params = array(
            'startdate' => $threedaysago->getTimestamp(),
        );
        $ongoingcourse = $this->getDataGenerator()->create_course($params);
        $ongoinganalysable = new \core_analytics\course($ongoingcourse);

        $pastweek = new \core\analytics\time_splitting\past_week();
        $pastweek->set_analysable($ongoinganalysable);
        $ranges = $pastweek->get_all_ranges();
        $this->assertEquals(0, count($ranges));
        $this->assertCount(0, $pastweek->get_training_ranges());

        // We now use a ready-to-predict ongoing course.

        $onemonthago = new \DateTime('-30 days');
        $params = array(
            'startdate' => $onemonthago->getTimestamp(),
        );
        $ongoingcourse = $this->getDataGenerator()->create_course($params);
        $ongoinganalysable = new \core_analytics\course($ongoingcourse);

        $pastweek = new \core\analytics\time_splitting\past_week();
        $pastweek->set_analysable($ongoinganalysable);
        $this->assertCount(1, $pastweek->get_distinct_ranges());

        $ranges = $pastweek->get_all_ranges();
        $this->assertEquals(4, count($ranges));
        $this->assertCount(4, $pastweek->get_training_ranges());

        $ranges = $pastweek->get_most_recent_prediction_range();
        $range = reset($ranges);
        $this->assertEquals(3, key($ranges));
        $this->assertEqualsWithDelta(time(), $range['time'], 1);
        // 1 second delta for the start just in case a second passes between the set_analysable call
        // and this checking below.
        $time = new \DateTime();
        $time->sub($pastweek->periodicity());
        $this->assertEqualsWithDelta($time->getTimestamp(), $range['start'], 1.0);
        $this->assertEqualsWithDelta(time(), $range['end'], 1);

        $starttime = time();

        $upcomingweek = new \core\analytics\time_splitting\upcoming_week();
        $upcomingweek->set_analysable($ongoinganalysable);
        $this->assertCount(1, $upcomingweek->get_distinct_ranges());

        $ranges = $upcomingweek->get_all_ranges();
        $this->assertEquals(1, count($ranges));
        $range = reset($ranges);
        $this->assertEqualsWithDelta(time(), $range['time'], 1);
        $this->assertEqualsWithDelta(time(), $range['start'], 1);
        $this->assertGreaterThan(time(), $range['end']);

        $this->assertCount(0, $upcomingweek->get_training_ranges());

        $ranges = $upcomingweek->get_most_recent_prediction_range();
        $range = reset($ranges);
        $this->assertEquals(0, key($ranges));
        $this->assertEqualsWithDelta(time(), $range['time'], 1);
        $this->assertEqualsWithDelta(time(), $range['start'], 1);
        $this->assertGreaterThanOrEqual($starttime, $range['time']);
        $this->assertGreaterThanOrEqual($starttime, $range['start']);
        $this->assertGreaterThan(time(), $range['end']);

        $this->assertNotEmpty($upcomingweek->get_range_by_index(0));
        $this->assertFalse($upcomingweek->get_range_by_index(1));

        // We now check how new ranges get added as time passes.

        $fewsecsago = new \DateTime('-5 seconds');
        $params = array(
            'startdate' => $fewsecsago->getTimestamp(),
            'enddate' => (new \DateTimeImmutable('+1 year'))->getTimestamp(),
        );
        $course = $this->getDataGenerator()->create_course($params);
        $analysable = new \core_analytics\course($course);

        $seconds = new test_timesplitting_seconds();
        $seconds->set_analysable($analysable);

        // Store the ranges we just obtained.
        $ranges = $seconds->get_all_ranges();
        $nranges = count($ranges);
        $ntrainingranges = count($seconds->get_training_ranges());
        $mostrecentrange = $seconds->get_most_recent_prediction_range();
        $mostrecentrange = reset($mostrecentrange);

        // We wait for the next range to be added.
        sleep(1);

        // We set the analysable again so the time ranges are recalculated.
        $seconds->set_analysable($analysable);

        $newranges = $seconds->get_all_ranges();
        $nnewranges = count($newranges);
        $nnewtrainingranges = $seconds->get_training_ranges();
        $newmostrecentrange = $seconds->get_most_recent_prediction_range();
        $newmostrecentrange = reset($newmostrecentrange);
        $this->assertGreaterThan($nranges, $nnewranges);
        $this->assertGreaterThan($ntrainingranges, $nnewtrainingranges);
        $this->assertGreaterThan($mostrecentrange['time'], $newmostrecentrange['time']);

        // All the ranges but the last one should return the same values.
        array_pop($ranges);
        array_pop($newranges);
        foreach ($ranges as $key => $range) {
            $this->assertEquals($newranges[$key]['start'], $range['start']);
            $this->assertEquals($newranges[$key]['end'], $range['end']);
            $this->assertEquals($newranges[$key]['time'], $range['time']);
        }

        // Fake model id, we can use any int, we will need to reference it later.
        $modelid = 1505347200;

        $upcomingseconds = new test_timesplitting_upcoming_seconds();
        $upcomingseconds->set_modelid($modelid);
        $upcomingseconds->set_analysable($analysable);

        // Store the ranges we just obtained.
        $ranges = $upcomingseconds->get_all_ranges();
        $nranges = count($ranges);
        $ntrainingranges = count($upcomingseconds->get_training_ranges());
        $mostrecentrange = $upcomingseconds->get_most_recent_prediction_range();
        $mostrecentrange = reset($mostrecentrange);

        // Mimic the modelfirstanalyses caching in \core_analytics\analysis.
        $this->mock_cache_first_analysis_caching($modelid, $analysable->get_id(), end($ranges));

        // We wait for the next range to be added.
        sleep(1);

        // We set the analysable again so the time ranges are recalculated.
        $upcomingseconds->set_analysable($analysable);

        $newranges = $upcomingseconds->get_all_ranges();
        $nnewranges = count($newranges);
        $nnewtrainingranges = $upcomingseconds->get_training_ranges();
        $newmostrecentrange = $upcomingseconds->get_most_recent_prediction_range();
        $newmostrecentrange = reset($newmostrecentrange);
        $this->assertGreaterThan($nranges, $nnewranges);
        $this->assertGreaterThan($ntrainingranges, $nnewtrainingranges);
        $this->assertGreaterThan($mostrecentrange['time'], $newmostrecentrange['time']);

        // All the ranges but the last one should return the same values.
        array_pop($ranges);
        array_pop($newranges);
        foreach ($ranges as $key => $range) {
            $this->assertEquals($newranges[$key]['start'], $range['start']);
            $this->assertEquals($newranges[$key]['end'], $range['end']);
            $this->assertEquals($newranges[$key]['time'], $range['time']);
        }
    }

    /**
     * Mocks core_analytics\analysis caching of the first time analysables were analysed.
     *
     * @param  int $modelid
     * @param  int $analysableid
     * @param  array $range
     * @return null
     */
    private function mock_cache_first_analysis_caching($modelid, $analysableid, $range) {
        $cache = \cache::make('core', 'modelfirstanalyses');
        $cache->set($modelid . '_' . $analysableid, $range['time']);
    }
}
