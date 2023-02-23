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
 * Unit test for mod_survey searching.
 *
 * This is needed because the activity.php class overrides default behaviour.
 *
 * @package mod_survey
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_survey\search;

/**
 * Unit test for mod_survey searching.
 *
 * This is needed because the activity.php class overrides default behaviour.
 *
 * @package mod_survey
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_test extends \advanced_testcase {

    /**
     * Test survey_view
     * @return void
     */
    public function test_survey_indexing() {
        global $CFG;

        $this->resetAfterTest();

        require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
        \testable_core_search::instance();
        $area = \core_search\manager::get_search_area('mod_survey-activity');

        // Setup test data.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $survey1 = $generator->create_module('survey', ['course' => $course->id]);
        $survey2 = $generator->create_module('survey', ['course' => $course->id]);

        // Get all surveys for indexing - note that there are special entries in the table with
        // course zero which should not be returned.
        $rs = $area->get_document_recordset();
        $this->assertEquals(2, iterator_count($rs));
        $rs->close();

        // Test specific context and course context.
        $rs = $area->get_document_recordset(0, \context_module::instance($survey1->cmid));
        $this->assertEquals(1, iterator_count($rs));
        $rs->close();
        $rs = $area->get_document_recordset(0, \context_course::instance($course->id));
        $this->assertEquals(2, iterator_count($rs));
        $rs->close();
    }
}
