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

/**
 * Unit tests for the analysis class.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class analysis_test extends \advanced_testcase {

    /**
     * Test fill_firstanalyses_cache.
     * @return null
     */
    public function test_fill_firstanalyses_cache(): void {
        require_once(self::get_fixture_path(__NAMESPACE__, 'test_timesplitting_upcoming_seconds.php'));
        $this->resetAfterTest();

        $modelid = 1;

        $params = ['startdate' => (new \DateTimeImmutable('-5 seconds'))->getTimestamp()];
        $course1 = $this->getDataGenerator()->create_course($params);
        $course2 = $this->getDataGenerator()->create_course($params);
        $analysable1 = new \core_analytics\course($course1);

        $afewsecsago = time() - 5;
        $earliest = $afewsecsago - 1;

        $this->insert_used($modelid, $course1->id, 'training', $afewsecsago);

        // Course2 processed after course1.
        $this->insert_used($modelid, $course2->id, 'training', $afewsecsago + 1);

        // After the first process involving course1.
        $this->insert_used($modelid, $course1->id, 'prediction', $afewsecsago + 5);

        $firstanalyses = \core_analytics\analysis::fill_firstanalyses_cache($modelid);
        $this->assertCount(2, $firstanalyses);
        $this->assertEquals($afewsecsago, $firstanalyses[$modelid . '_' . $course1->id]);
        $this->assertEquals($afewsecsago + 1, $firstanalyses[$modelid . '_' . $course2->id]);

        // The cached elements get refreshed.
        $this->insert_used($modelid, $course1->id, 'prediction', $earliest);
        $firstanalyses = \core_analytics\analysis::fill_firstanalyses_cache($modelid, $course1->id);
        $this->assertCount(1, $firstanalyses);
        $this->assertEquals($earliest, $firstanalyses[$modelid . '_' . $course1->id]);

        // Upcoming periodic time-splitting methods can read and process the cached data.
        $seconds = new \test_timesplitting_upcoming_seconds();
        $seconds->set_modelid($modelid);
        $seconds->set_analysable($analysable1);

        // The generated ranges should start from the cached firstanalysis value, which is $earliest.
        $ranges = $seconds->get_all_ranges();
        $this->assertGreaterThanOrEqual(7, count($ranges));
        $firstrange = reset($ranges);
        $this->assertEquals($earliest, $firstrange['time']);
    }

    private function insert_used($modelid, $analysableid, $action, $timestamp) {
        global $DB;

        $obj = new \stdClass();
        $obj->modelid = $modelid;
        $obj->action = $action;
        $obj->analysableid = $analysableid;
        $obj->firstanalysis = $timestamp;
        $obj->timeanalysed = $timestamp;
        $obj->id = $DB->insert_record('analytics_used_analysables', $obj);
    }
}
