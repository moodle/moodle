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
 * Search engine base unit tests.
 *
 * @package     core_search
 * @copyright   2017 Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/search/tests/fixtures/mock_search_area.php');

/**
 * Search engine base unit tests.
 *
 * @package     core_search
 * @copyright   2017 Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_base_activity_testcase extends advanced_testcase {
    /**
     * @var \core_search::manager
     */
    protected $search = null;

    /**
     * @var Instace of core_search_generator.
     */
    protected $generator = null;

    /**
     * @var Instace of testable_engine.
     */
    protected $engine = null;

    /** @var context[] Array of test contexts */
    protected $contexts;

    /** @var stdClass[] Array of test forum objects */
    protected $forums;

    public function setUp(): void {
        global $DB;
        $this->resetAfterTest();
        set_config('enableglobalsearch', true);

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = testable_core_search::instance();

        $this->generator = self::getDataGenerator()->get_plugin_generator('core_search');
        $this->generator->setup();

        $this->setAdminUser();

        // Create course and 2 forums.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $this->contexts['c1'] = \context_course::instance($course->id);
        $this->forums[1] = $generator->create_module('forum', ['course' => $course->id, 'name' => 'Forum 1',
                'intro' => '<p>Intro 1</p>', 'introformat' => FORMAT_HTML]);
        $this->contexts['f1'] = \context_module::instance($this->forums[1]->cmid);
        $this->forums[2] = $generator->create_module('forum', ['course' => $course->id, 'name' => 'Forum 2',
                'intro' => '<p>Intro 2</p>', 'introformat' => FORMAT_HTML]);
        $this->contexts['f2'] = \context_module::instance($this->forums[2]->cmid);

        // Create another 2 courses (in same category and in a new category) with one forum each.
        $this->contexts['cc1']  = \context_coursecat::instance($course->category);
        $course2 = $generator->create_course();
        $this->contexts['c2'] = \context_course::instance($course2->id);
        $this->forums[3] = $generator->create_module('forum', ['course' => $course2->id, 'name' => 'Forum 3',
                'intro' => '<p>Intro 3</p>', 'introformat' => FORMAT_HTML]);
        $this->contexts['f3'] = \context_module::instance($this->forums[3]->cmid);
        $cat2 = $generator->create_category();
        $this->contexts['cc2'] = \context_coursecat::instance($cat2->id);
        $course3 = $generator->create_course(['category' => $cat2->id]);
        $this->contexts['c3'] = \context_course::instance($course3->id);
        $this->forums[4] = $generator->create_module('forum', ['course' => $course3->id, 'name' => 'Forum 4',
                'intro' => '<p>Intro 4</p>', 'introformat' => FORMAT_HTML]);
        $this->contexts['f4'] = \context_module::instance($this->forums[4]->cmid);

        // Hack about with the time modified values.
        foreach ($this->forums as $index => $forum) {
            $DB->set_field('forum', 'timemodified', $index, ['id' => $forum->id]);
        }
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
     * Test base activity get search fileareas
     */
    public function test_get_search_fileareas_base() {

        $builder = $this->getMockBuilder('\core_search\base_activity');
        $builder->disableOriginalConstructor();
        $stub = $builder->getMockForAbstractClass();

        $result = $stub->get_search_fileareas();

        $this->assertEquals(array('intro'), $result);
    }

    /**
     * Test base attach files
     */
    public function test_attach_files_base() {
        $filearea = 'intro';
        $component = 'mod_forum';
        $module = 'forum';

        $course = self::getDataGenerator()->create_course();
        $activity = self::getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = \context_module::instance($activity->cmid);
        $contextid = $context->id;

        // Create file to add.
        $fs = get_file_storage();
        $filerecord = array(
                'contextid' => $contextid,
                'component' => $component,
                'filearea' => $filearea,
                'itemid' => 0,
                'filepath' => '/',
                'filename' => 'testfile.txt');
        $content = 'All the news that\'s fit to print';
        $file = $fs->create_file_from_string($filerecord, $content);

        // Construct the search document.
        $rec = new \stdClass();
        $rec->courseid = $course->id;
        $area = new core_mocksearch\search\mock_search_area();
        $record = $this->generator->create_record($rec);

        $document = $area->get_document($record);
        $document->set('itemid', $activity->id);

        // Create a mock from the abstract class,
        // with required methods stubbed.
        $builder = $this->getMockBuilder('\core_search\base_activity');
        $builder->disableOriginalConstructor();
        $builder->onlyMethods(array('get_module_name', 'get_component_name'));
        $stub = $builder->getMockForAbstractClass();
        $stub->method('get_module_name')->willReturn($module);
        $stub->method('get_component_name')->willReturn($component);

        // Attach file to our test document.
        $stub->attach_files($document);

        // Verify file is attached.
        $files = $document->get_files();
        $file = array_values($files)[0];

        $this->assertEquals(1, count($files));
        $this->assertEquals($content, $file->get_content());
    }

    /**
     * Tests getting the recordset.
     */
    public function test_get_document_recordset() {
        global $USER, $DB;

        // Get all the forums to index (no restriction).
        $area = new mod_forum\search\activity();
        $results = self::recordset_to_indexed_array($area->get_document_recordset());

        // Should return all forums.
        $this->assertCount(4, $results);

        // Each result should basically have the contents of the forum table. We'll just check
        // the key fields for the first one and then the other ones by id only.
        $this->assertEquals($this->forums[1]->id, $results[0]->id);
        $this->assertEquals(1, $results[0]->timemodified);
        $this->assertEquals($this->forums[1]->course, $results[0]->course);
        $this->assertEquals('Forum 1', $results[0]->name);
        $this->assertEquals('<p>Intro 1</p>', $results[0]->intro);
        $this->assertEquals(FORMAT_HTML, $results[0]->introformat);

        $allids = self::records_to_ids($this->forums);
        $this->assertEquals($allids, self::records_to_ids($results));

        // Repeat with a time restriction.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(3));
        $this->assertEquals([$this->forums[3]->id, $this->forums[4]->id],
                self::records_to_ids($results));

        // Now use context restrictions. First, the whole site (no change).
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, context_system::instance()));
        $this->assertEquals($allids, self::records_to_ids($results));

        // Course 1 only.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $this->contexts['c1']));
        $this->assertEquals([$this->forums[1]->id, $this->forums[2]->id],
                self::records_to_ids($results));

        // Course 2 only.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $this->contexts['c2']));
        $this->assertEquals([$this->forums[3]->id], self::records_to_ids($results));

        // Specific forum only.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $this->contexts['f4']));
        $this->assertEquals([$this->forums[4]->id], self::records_to_ids($results));

        // Category 1 context (courses 1 and 2).
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $this->contexts['cc1']));
        $this->assertEquals([$this->forums[1]->id, $this->forums[2]->id, $this->forums[3]->id],
                self::records_to_ids($results));

        // Category 2 context (course 3).
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                0, $this->contexts['cc2']));
        $this->assertEquals([$this->forums[4]->id], self::records_to_ids($results));

        // Combine context restriction (category 1) with timemodified.
        $results = self::recordset_to_indexed_array($area->get_document_recordset(
                2, $this->contexts['cc1']));
        $this->assertEquals([$this->forums[2]->id, $this->forums[3]->id],
                self::records_to_ids($results));

        // Find an arbitrary block on the system to get a block context.
        $blockid = array_values($DB->get_records('block_instances', null, 'id', 'id', 0, 1))[0]->id;
        $blockcontext = context_block::instance($blockid);

        // Block context (cannot return anything, so always null).
        $this->assertNull($area->get_document_recordset(0, $blockcontext));

        // User context (cannot return anything, so always null).
        $usercontext = context_user::instance($USER->id);
        $this->assertNull($area->get_document_recordset(0, $usercontext));
    }

    /**
     * Utility function to convert recordset to array for testing.
     *
     * @param moodle_recordset $rs Recordset to convert
     * @return array Array indexed by number (0, 1, 2, ...)
     */
    protected static function recordset_to_indexed_array(moodle_recordset $rs) {
        $results = [];
        foreach ($rs as $rec) {
            $results[] = $rec;
        }
        $rs->close();
        return $results;
    }

    /**
     * Utility function to convert records to array of IDs.
     *
     * @param array $recs Records which should have an 'id' field
     * @return array Array of ids
     */
    protected static function records_to_ids(array $recs) {
        $ids = [];
        foreach ($recs as $rec) {
            $ids[] = $rec->id;
        }
        return $ids;
    }

    /**
     * Tests the get_doc_url function.
     */
    public function test_get_doc_url() {
        $area = new mod_forum\search\activity();
        $results = self::recordset_to_indexed_array($area->get_document_recordset());

        for ($i = 0; $i < 4; $i++) {
            $this->assertEquals(new moodle_url('/mod/forum/view.php',
                    ['id' => $this->forums[$i + 1]->cmid]),
                    $area->get_doc_url($area->get_document($results[$i])));
        }
    }

    /**
     * Tests the check_access function.
     */
    public function test_check_access() {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        // Create a test user who can access courses 1 and 2 (everything except forum 4).
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $this->forums[1]->course, 'student');
        $generator->enrol_user($user->id, $this->forums[3]->course, 'student');
        $this->setUser($user);

        // Delete forum 2 and set forum 3 hidden.
        course_delete_module($this->forums[2]->cmid);
        set_coursemodule_visible($this->forums[3]->cmid, 0);

        // Call check access on all the first three.
        $area = new mod_forum\search\activity();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $area->check_access(
                $this->forums[1]->id));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $area->check_access(
                $this->forums[2]->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $area->check_access(
                $this->forums[3]->id));

        // Note: Do not check forum 4 which is in a course the user can't access; this will return
        // ACCESS_GRANTED, but it does not matter because the search engine will not have included
        // that context in the list to search. (This is because the $cm->uservisible access flag
        // is only valid if the user is known to be able to access the course.)
    }

    /**
     * Tests the module version of get_contexts_to_reindex, which is supposed to return all the
     * activity contexts in order of date added.
     */
    public function test_get_contexts_to_reindex() {
        global $DB;

        $this->resetAfterTest();

        // Set up a course with two URLs and a Page.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['fullname' => 'TCourse']);
        $url1 = $generator->create_module('url', ['course' => $course->id, 'name' => 'TURL1']);
        $url2 = $generator->create_module('url', ['course' => $course->id, 'name' => 'TURL2']);
        $page = $generator->create_module('page', ['course' => $course->id, 'name' => 'TPage1']);

        // Hack the items so they have different added times.
        $now = time();
        $DB->set_field('course_modules', 'added', $now - 3, ['id' => $url2->cmid]);
        $DB->set_field('course_modules', 'added', $now - 2, ['id' => $url1->cmid]);
        $DB->set_field('course_modules', 'added', $now - 1, ['id' => $page->cmid]);

        // Check the URL contexts are in date order.
        $urlarea = new \mod_url\search\activity();
        $contexts = iterator_to_array($urlarea->get_contexts_to_reindex(), false);
        $this->assertEquals([\context_module::instance($url1->cmid),
                \context_module::instance($url2->cmid)], $contexts);

        // Check the Page contexts.
        $pagearea = new \mod_page\search\activity();
        $contexts = iterator_to_array($pagearea->get_contexts_to_reindex(), false);
        $this->assertEquals([\context_module::instance($page->cmid)], $contexts);

        // Check another module area that has no instances.
        $glossaryarea = new \mod_glossary\search\activity();
        $contexts = iterator_to_array($glossaryarea->get_contexts_to_reindex(), false);
        $this->assertEquals([], $contexts);
    }

    /**
     * Test document icon.
     */
    public function test_get_doc_icon() {
        $baseactivity = $this->getMockBuilder('\core_search\base_activity')
            ->disableOriginalConstructor()
            ->onlyMethods(array('get_module_name'))
            ->getMockForAbstractClass();

        $baseactivity->method('get_module_name')->willReturn('test_activity');

        $document = $this->getMockBuilder('\core_search\document')
            ->disableOriginalConstructor()
            ->getMock();

        $result = $baseactivity->get_doc_icon($document);

        $this->assertEquals('monologo', $result->get_name());
        $this->assertEquals('test_activity', $result->get_component());
    }
}
