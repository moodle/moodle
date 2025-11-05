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
 * Wiki global search unit tests.
 *
 * @package     mod_wiki
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_wiki\search;

use core_courseformat\formatactions;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

/**
 * Provides the unit tests for wiki global search.
 *
 * @package     mod_wiki
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class search_test extends \advanced_testcase {

    /**
     * @var string Area id
     */
    protected $wikicollabpageareaid = null;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->setAdminUser();
        set_config('enableglobalsearch', true);

        $this->wikicollabpageareaid = \core_search\manager::generate_areaid('mod_wiki', 'collaborative_page');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = \testable_core_search::instance();
    }

    /**
     * Availability.
     *
     * @return void
     */
    public function test_search_enabled(): void {
        $searcharea = \core_search\manager::get_search_area($this->wikicollabpageareaid);
        list($componentname, $varname) = $searcharea->get_config_var_name();

        // Enabled by default once global search is enabled.
        $this->assertTrue($searcharea->is_enabled());

        set_config($varname . '_enabled', 0, $componentname);
        $this->assertFalse($searcharea->is_enabled());

        set_config($varname . '_enabled', 1, $componentname);
        $this->assertTrue($searcharea->is_enabled());
    }

    /**
     * Indexing collaborative page contents.
     *
     * @return void
     */
    public function test_collaborative_page_indexing(): void {
        global $DB;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->wikicollabpageareaid);
        $this->assertInstanceOf('\mod_wiki\search\collaborative_page', $searcharea);

        $wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');
        $course1 = self::getDataGenerator()->create_course();

        $collabwiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course1->id));
        $cpage1 = $wikigenerator->create_first_page($collabwiki);
        $cpage2 = $wikigenerator->create_content($collabwiki);
        $cpage3 = $wikigenerator->create_content($collabwiki);

        $indwiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course1->id, 'wikimode' => 'individual'));
        $ipage1 = $wikigenerator->create_first_page($indwiki);
        $ipage2 = $wikigenerator->create_content($indwiki);
        $ipage3 = $wikigenerator->create_content($indwiki);

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
            $this->assertEquals($dbreads, $DB->perf_get_reads());
            $this->assertInstanceOf('\core_search\document', $doc);
            $nrecords++;
        }
        // If there would be an error/failure in the foreach above the recordset would be closed on shutdown.
        $recordset->close();

        // We expect 3 (not 6) pages.
        $this->assertEquals(3, $nrecords);

        // The +2 is to prevent race conditions.
        $recordset = $searcharea->get_recordset_by_timestamp(time() + 2);

        // No new records.
        $this->assertFalse($recordset->valid());
        $recordset->close();

        // Add another wiki with one page.
        $collabwiki2 = $this->getDataGenerator()->create_module('wiki', ['course' => $course1->id]);
        $wikigenerator->create_first_page($collabwiki2);

        // Test indexing contexts.
        $rs = $searcharea->get_document_recordset(0, \context_module::instance($collabwiki->cmid));
        $this->assertEquals(3, iterator_count($rs));
        $rs->close();
        $rs = $searcharea->get_document_recordset(0, \context_module::instance($collabwiki2->cmid));
        $this->assertEquals(1, iterator_count($rs));
        $rs->close();
        $rs = $searcharea->get_document_recordset(0, \context_course::instance($course1->id));
        $this->assertEquals(4, iterator_count($rs));
        $rs->close();
    }

    /**
     * Group support for wiki entries.
     */
    public function test_collaborative_page_group_support(): void {
        // Get the search area and test generators.
        $searcharea = \core_search\manager::get_search_area($this->wikicollabpageareaid);
        $generator = $this->getDataGenerator();
        $wikigenerator = $generator->get_plugin_generator('mod_wiki');

        // Create a course, a user, and two groups.
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'teacher');
        $group1 = $generator->create_group(['courseid' => $course->id]);
        $group2 = $generator->create_group(['courseid' => $course->id]);

        // Separate groups wiki.
        $wiki = self::getDataGenerator()->create_module('wiki', ['course' => $course->id,
                'groupmode' => SEPARATEGROUPS]);

        // Create page with each group and one for all groups.
        $wikigenerator->create_page($wiki, ['title' => 'G1', 'group' => $group1->id]);
        $wikigenerator->create_page($wiki, ['title' => 'G2', 'group' => $group2->id]);
        $wikigenerator->create_page($wiki, ['title' => 'ALLGROUPS']);

        // Do the indexing of all 3 pages.
        $rs = $searcharea->get_recordset_by_timestamp(0);
        $results = [];
        foreach ($rs as $rec) {
            $results[$rec->title] = $rec;
        }
        $rs->close();
        $this->assertCount(3, $results);

        // Check each document has the correct groupid.
        $doc = $searcharea->get_document($results['G1']);
        $this->assertTrue($doc->is_set('groupid'));
        $this->assertEquals($group1->id, $doc->get('groupid'));
        $doc = $searcharea->get_document($results['G2']);
        $this->assertTrue($doc->is_set('groupid'));
        $this->assertEquals($group2->id, $doc->get('groupid'));
        $doc = $searcharea->get_document($results['ALLGROUPS']);
        $this->assertFalse($doc->is_set('groupid'));

        // While we're here, also test that the search area requests restriction by group.
        $modinfo = get_fast_modinfo($course);
        $this->assertTrue($searcharea->restrict_cm_access_by_group($modinfo->get_cm($wiki->cmid)));

        // In visible groups mode, it won't request restriction by group.
        formatactions::cm($course->id)->set_groupmode($wiki->cmid, VISIBLEGROUPS);
        $modinfo = get_fast_modinfo($course);
        $this->assertFalse($searcharea->restrict_cm_access_by_group($modinfo->get_cm($wiki->cmid)));
    }

    /**
     * Check collaborative_page check access.
     *
     * @return void
     */
    public function test_collaborative_page_check_access(): void {
        global $DB;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->wikicollabpageareaid);
        $this->assertInstanceOf('\mod_wiki\search\collaborative_page', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $course1 = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');

        $wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');

        $collabwiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course1->id));
        $cpage1 = $wikigenerator->create_first_page($collabwiki);

        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($cpage1->id));

        $this->setUser($user1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($cpage1->id));

        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access($cpage1->id + 10));
    }
}
