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

namespace core_search;

use advanced_testcase;
use context_course;
use core_mocksearch\search\mock_search_area;
use mock_search\engine;
use testable_core_search;
use stdClass;

/**
 * Unit tests for search document.
 *
 * @package     core_search
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_search\document
 */
class document_test extends \advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
        require_once($CFG->dirroot . '/search/tests/fixtures/mock_search_area.php');
    }

    /**
     * @var Instace of core_search_generator.
     */
    protected $generator = null;

    public function setUp(): void {
        $this->resetAfterTest();
        set_config('enableglobalsearch', true);

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = \testable_core_search::instance();

        $this->generator = self::getDataGenerator()->get_plugin_generator('core_search');
        $this->generator->setup();
    }

    /**
     * Adding this test here as get_areas_user_accesses process is the same, results just depend on the context level.
     *
     * @covers ::export_for_template
     * @return void
     */
    public function test_search_user_accesses() {
        global $PAGE;

        $area = new mock_search_area();
        $renderer = $PAGE->get_renderer('core_search');
        $engine = new engine();

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Course & Title']);
        $user = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => 'Escape & Name']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'teacher');
        $this->setAdminUser();

        // Make a record to enter in the search area.
        $record = new stdClass();
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

    /**
     * Test we can set and get document icon.
     *
     * @covers ::set_doc_icon
     */
    public function test_get_and_set_doc_icon() {
        $document = $this->getMockBuilder('\core_search\document')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->assertNull($document->get_doc_icon());

        $docicon = new \core_search\document_icon('test_name', 'test_component');
        $document->set_doc_icon($docicon);

        $this->assertEquals($docicon, $document->get_doc_icon());
    }

    public function tearDown(): void {
        // For unit tests before PHP 7, teardown is called even on skip. So only do our teardown if we did setup.
        if ($this->generator) {
            // Moodle DML freaks out if we don't teardown the temp table after each run.
            $this->generator->teardown();
            $this->generator = null;
        }
    }

    /**
     * Test the document author visibility depending on the user capabilities.
     *
     * @covers ::export_for_template
     * @dataProvider document_author_visibility_provider
     * @param string $rolename the role name
     * @param array $capexceptions the capabilities exceptions
     * @param bool $expected the expected author visibility
     * @param bool $owndocument if the resulting document belongs to the current user
     */
    public function test_document_author_visibility(
        string $rolename = 'editingteacher',
        array $capexceptions = [],
        bool $expected = true,
        bool $owndocument = false
    ) {
        global $DB, $PAGE;

        $area = new mock_search_area();
        $renderer = $PAGE->get_renderer('core_search');
        $engine = new engine();

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Course & Title']);
        $context = context_course::instance($course->id);

        $roleid = $DB->get_field('role', 'id', ['shortname' => $rolename]);
        foreach ($capexceptions as $capability) {
            assign_capability($capability, CAP_PROHIBIT, $roleid, $context->id);
        }

        $user = $this->getDataGenerator()->create_user(['firstname' => 'Test', 'lastname' => 'User']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $rolename);
        $this->setUser($user);

        if ($owndocument) {
            $author = $user;
        } else {
            $author = $this->getDataGenerator()->create_user(['firstname' => 'User', 'lastname' => 'Escape & Name']);
            $this->getDataGenerator()->enrol_user($author->id, $course->id, 'student');
        }

        // Make a record to enter in the search area.
        $record = new stdClass();
        $record->title = 'Escape & Title';
        $record->content = 'Escape & Content';
        $record->description1 = 'Escape & Description1';
        $record->description2 = 'Escape & Description2';
        $record->userid = $author->id;
        $record->courseid = $course->id;
        $record->contextid = $context->id;
        $record = $this->generator->create_record($record);

        // Convert to a 'doc data' type format.
        $docdata = $area->convert_record_to_doc_array($record);

        // First see that the document has the user information.
        $doc = $engine->to_document($area, $docdata);
        $this->assertEquals(fullname($author), $doc->get('userfullname'));

        // Export for template, and see if it the user information is exported.
        $export = $doc->export_for_template($renderer);

        if ($expected) {
            $authorname = htmlentities(fullname($author));
            $this->assertEquals($authorname, $export['userfullname']);
        } else {
            $this->assertArrayNotHasKey('userfullname', $export);
        }
    }

    /**
     * Data provider for test_document_author_visibility().
     *
     * @return array
     */
    public function document_author_visibility_provider(): array {
        return [
            'Teacher' => [
                'rolename' => 'editingteacher',
                'capexceptions' => [],
                'expected' => true,
                'owndocument' => false,
            ],
            'Non editing teacher' => [
                'rolename' => 'teacher',
                'capexceptions' => [],
                'expected' => true,
                'owndocument' => false,
            ],
            'Student' => [
                'rolename' => 'student',
                'capexceptions' => [],
                'expected' => true,
                'owndocument' => false,
            ],
            // Adding capability exceptions.
            'Student without view profiles' => [
                'rolename' => 'student',
                'capexceptions' => ['moodle/user:viewdetails'],
                'expected' => false,
                'owndocument' => false,
            ],
            'Student without view participants' => [
                'rolename' => 'student',
                'capexceptions' => ['moodle/course:viewparticipants'],
                'expected' => false,
                'owndocument' => false,
            ],
            'Student without view participants or profiles' => [
                'rolename' => 'student',
                'capexceptions' => ['moodle/user:viewdetails', 'moodle/course:viewparticipants'],
                'expected' => false,
                'owndocument' => false,
            ],
            // Users should be able to see its own documents.
            'Student author without view profiles' => [
                'rolename' => 'student',
                'capexceptions' => ['moodle/user:viewdetails'],
                'expected' => true,
                'owndocument' => true,
            ],
            'Student author without view participants' => [
                'rolename' => 'student',
                'capexceptions' => ['moodle/course:viewparticipants'],
                'expected' => true,
                'owndocument' => true,
            ],
            'Student author without view participants or profiles' => [
                'rolename' => 'student',
                'capexceptions' => ['moodle/user:viewdetails', 'moodle/course:viewparticipants'],
                'expected' => true,
                'owndocument' => true,
            ],

        ];
    }
}
