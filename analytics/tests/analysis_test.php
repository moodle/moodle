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
 * Unit tests for the analysis class.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for the analysis class.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analytics_analysis_testcase extends advanced_testcase {

    /**
     * Test fill_firstanalyses_cache.
     * @return null
     */
    public function test_fill_firstanalyses_cache() {
        $this->resetAfterTest();

        $this->insert_used(1, 1, 'training', 123);
        $this->insert_used(1, 2, 'training', 124);
        $this->insert_used(1, 1, 'prediction', 125);

        $firstanalyses = \core_analytics\analysis::fill_firstanalyses_cache(1);
        $this->assertCount(2, $firstanalyses);
        $this->assertEquals(123, $firstanalyses['1_1']);
        $this->assertEquals(124, $firstanalyses['1_2']);

        // The cached elements gets refreshed.
        $this->insert_used(1, 1, 'prediction', 122);
        $firstanalyses = \core_analytics\analysis::fill_firstanalyses_cache(1, 1);
        $this->assertCount(1, $firstanalyses);
        $this->assertEquals(122, $firstanalyses['1_1']);
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
