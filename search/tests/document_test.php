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
 * Search document unit tests.
 *
 * @package     core_search
 * @category    phpunit
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/testable_core_search.php');
require_once(__DIR__ . '/fixtures/mock_search_area.php');

/**
 * Unit tests for search document.
 *
 * @package     core_search
 * @category    phpunit
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_document_testcase extends advanced_testcase {

    /**
     * @var Instace of core_search_generator.
     */
    protected $generator = null;

    public function setUp() {
        $this->resetAfterTest();
        set_config('enableglobalsearch', true);

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = testable_core_search::instance();

        $this->generator = self::getDataGenerator()->get_plugin_generator('core_search');
        $this->generator->setup();
    }

    /**
     * Adding this test here as get_areas_user_accesses process is the same, results just depend on the context level.
     *
     * @return void
     */
    public function test_search_user_accesses() {
        global $DB, $PAGE;

        $area = new \core_mocksearch\search\mock_search_area();
        $renderer = $PAGE->get_renderer('core_search');
        $engine = new \mock_search\engine();

        $course = $this->getDataGenerator()->create_course(array('fullname' => 'Course & Title'));
        $coursectx = context_course::instance($course->id);
        $user = $this->getDataGenerator()->create_user(array('firstname' => 'User', 'lastname' => 'Escape & Name'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'teacher');

        // Make a record to enter in the search area.
        $record = new \stdClass();
        $record->title = 'Escape & Title';
        $record->content = 'Escape & Content';
        $record->description1 = 'Escape & Description1';
        $record->description2 = 'Escape & Description2';
        $record->userid = $user->id;
        $record->courseid = $course->id;
        $record = $this->generator->create_record($record);

        // Convert to a 'doc data' type format.
        $docdata = $area->convert_record_to_doc_array($record);

        // First see that the docuemnt has the right information, unescaped.
        $doc = $engine->to_document($area, $docdata);
        $this->assertEquals('Escape & Title', $doc->get('title'));
        $this->assertEquals('Escape & Content', $doc->get('content'));
        $this->assertEquals('Escape & Description1', $doc->get('description1'));
        $this->assertEquals('Escape & Description2', $doc->get('description2'));
        $this->assertEquals('User Escape & Name', $doc->get('userfullname'));
        $this->assertEquals('Course & Title', $doc->get('coursefullname'));

        // Export for template, and see if it is escaped.
        $export = $doc->export_for_template($renderer);
        $this->assertEquals('Escape &amp; Title', $export['title']);
        $this->assertEquals('Escape &amp; Content', $export['content']);
        $this->assertEquals('Escape &amp; Description1', $export['description1']);
        $this->assertEquals('Escape &amp; Description2', $export['description2']);
        $this->assertEquals('User Escape &amp; Name', $export['userfullname']);
        $this->assertEquals('Course &amp; Title', $export['coursefullname']);
    }

    public function tearDown() {
        // For unit tests before PHP 7, teardown is called even on skip. So only do our teardown if we did setup.
        if ($this->generator) {
            // Moodle DML freaks out if we don't teardown the temp table after each run.
            $this->generator->teardown();
            $this->generator = null;
        }
    }
}
