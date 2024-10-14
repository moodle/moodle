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
namespace mod_bigbluebuttonbn;

use advanced_testcase;
use context_course;
use context_module;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

/**
 * Provides the unit tests for forum search.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\search\tags
 * @covers \mod_bigbluebuttonbn\search\activity
 */
final class search_test extends advanced_testcase {
    use testcase_helper_trait;
    /**
     * @var string Area id
     */
    protected $bbbactivtyarea = null;

    public function setUp(): void {
        parent::setUp();
        set_config('enableglobalsearch', true);
        $this->bbbactivtyarea = \core_search\manager::get_search_area('mod_bigbluebuttonbn-activity');
    }

    /**
     * Test for indexing
     *
     * @return void
     */
    public function test_indexing(): void {
        $this->resetAfterTest();

        // Setup test data.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $bbactivity1 = $generator->create_module('bigbluebuttonbn', ['course' => $course->id, 'name' => 'BBB 1']);
        $bbactivity2 = $generator->create_module('bigbluebuttonbn', ['course' => $course->id, 'name' => 'BBB 2']);

        // Get all surveys for indexing - note that there are special entries in the table with
        // course zero which should not be returned.
        $rs = $this->bbbactivtyarea->get_document_recordset();
        $this->assertEquals(2, iterator_count($rs));
        $rs->close();

        // Test specific context and course context.
        $rs = $this->bbbactivtyarea->get_document_recordset(0, context_module::instance($bbactivity1->cmid));
        $this->assertEquals(1, iterator_count($rs));
        $rs->close();
        $rs = $this->bbbactivtyarea->get_document_recordset(0, context_course::instance($course->id));
        $documents = iterator_to_array($rs);
        $this->assertEquals(2, count($documents));
        $rs->close();
    }
}
