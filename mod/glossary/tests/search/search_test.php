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
 * Glossary search unit tests.
 *
 * @package     mod_glossary
 * @category    test
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_glossary\search;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/mod/glossary/tests/generator/lib.php');

/**
 * Provides the unit tests for glossary search.
 *
 * @package     mod_glossary
 * @category    test
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_test extends \advanced_testcase {

    /**
     * @var string Area id
     */
    protected $entryareaid = null;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        set_config('enableglobalsearch', true);

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = \testable_core_search::instance();

        $this->entryareaid = \core_search\manager::generate_areaid('mod_glossary', 'entry');
    }

    /**
     * Availability.
     *
     * @return void
     */
    public function test_search_enabled(): void {

        $searcharea = \core_search\manager::get_search_area($this->entryareaid);
        list($componentname, $varname) = $searcharea->get_config_var_name();

        // Enabled by default once global search is enabled.
        $this->assertTrue($searcharea->is_enabled());

        set_config($varname . '_enabled', 0, $componentname);
        $this->assertFalse($searcharea->is_enabled());

        set_config($varname . '_enabled', 1, $componentname);
        $this->assertTrue($searcharea->is_enabled());
    }

    /**
     * Indexing contents.
     *
     * @return void
     */
    public function test_entries_indexing(): void {
        global $DB;

        $searcharea = \core_search\manager::get_search_area($this->entryareaid);
        $this->assertInstanceOf('\mod_glossary\search\entry', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 'student');

        $record = new \stdClass();
        $record->course = $course1->id;

        $this->setUser($user1);

        // Approved entries by default glossary.
        $glossary1 = self::getDataGenerator()->create_module('glossary', $record);
        $entry1 = self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary1);
        $entry2 = self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary1);

        // All records.
        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $this->assertTrue($recordset->valid());
        $nrecords = 0;
        foreach ($recordset as $record) {
            $this->assertInstanceOf('stdClass', $record);
            $doc = $searcharea->get_document($record);
            $this->assertInstanceOf('\core_search\document', $doc);

            // Static caches are working.
            $dbreads = $DB->perf_get_reads();
            $doc = $searcharea->get_document($record);

            // The +1 is because we are not caching glossary alias (keywords) as they depend on a single entry.
            $this->assertEquals($dbreads + 1, $DB->perf_get_reads());
            $this->assertInstanceOf('\core_search\document', $doc);
            $nrecords++;
        }
        // If there would be an error/failure in the foreach above the recordset would be closed on shutdown.
        $recordset->close();
        $this->assertEquals(2, $nrecords);

        // The +2 is to prevent race conditions.
        $recordset = $searcharea->get_recordset_by_timestamp(time() + 2);

        // No new records.
        $this->assertFalse($recordset->valid());
        $recordset->close();

        // Create a second glossary with one entry.
        $glossary2 = self::getDataGenerator()->create_module('glossary', ['course' => $course1->id]);
        self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary2);

        // Test indexing with each activity then combined course context.
        $rs = $searcharea->get_document_recordset(0, \context_module::instance($glossary1->cmid));
        $this->assertEquals(2, iterator_count($rs));
        $rs->close();
        $rs = $searcharea->get_document_recordset(0, \context_module::instance($glossary2->cmid));
        $this->assertEquals(1, iterator_count($rs));
        $rs->close();
        $rs = $searcharea->get_document_recordset(0, \context_course::instance($course1->id));
        $this->assertEquals(3, iterator_count($rs));
        $rs->close();
    }

    /**
     * Document contents.
     *
     * @return void
     */
    public function test_entries_document(): void {
        global $DB;

        $searcharea = \core_search\manager::get_search_area($this->entryareaid);

        $user = self::getDataGenerator()->create_user();
        $course1 = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id, 'teacher');

        $record = new \stdClass();
        $record->course = $course1->id;

        $this->setUser($user);
        $glossary = self::getDataGenerator()->create_module('glossary', $record);
        $entry = self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary);
        $entry->course = $glossary->course;

        $doc = $searcharea->get_document($entry);
        $this->assertInstanceOf('\core_search\document', $doc);
        $this->assertEquals($entry->id, $doc->get('itemid'));
        $this->assertEquals($course1->id, $doc->get('courseid'));
        $this->assertEquals($user->id, $doc->get('userid'));
        $this->assertEquals($entry->concept, $doc->get('title'));
        $this->assertEquals($entry->definition, $doc->get('content'));
    }

    /**
     * Document accesses.
     *
     * @return void
     */
    public function test_entries_access(): void {
        global $DB;

        // Returns the instance as long as the component is supported.
        $searcharea = \core_search\manager::get_search_area($this->entryareaid);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 'student');

        $record = new \stdClass();
        $record->course = $course1->id;

        // Approved entries by default glossary, created by teacher.
        $this->setUser($user1);
        $glossary1 = self::getDataGenerator()->create_module('glossary', $record);
        $teacherapproved = self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary1);
        $teachernotapproved = self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary1, array('approved' => false));

        // Entries need to be approved and created by student.
        $glossary2 = self::getDataGenerator()->create_module('glossary', $record);
        $this->setUser($user2);
        $studentapproved = self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary2);
        $studentnotapproved = self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary2, array('approved' => false));

        // Activity hidden to students.
        $this->setUser($user1);
        $glossary3 = self::getDataGenerator()->create_module('glossary', $record);
        $hidden = self::getDataGenerator()->get_plugin_generator('mod_glossary')->create_content($glossary3);
        set_coursemodule_visible($glossary3->cmid, 0);

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($teacherapproved->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($teachernotapproved->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($studentapproved->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($studentnotapproved->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($hidden->id));
    }
}
