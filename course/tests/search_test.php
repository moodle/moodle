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
 * Course global search unit tests.
 *
 * @package     core
 * @category    phpunit
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

/**
 * Provides the unit tests for course global search.
 *
 * @package     core
 * @category    phpunit
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_search_testcase extends advanced_testcase {

    /**
     * @var string Area id
     */
    protected $mycoursesareaid = null;

    /**
     * @var string Area id for sections
     */
    protected $sectionareaid = null;

    /**
     * @var string Area id for custom fields.
     */
    protected $customfieldareaid = null;

    public function setUp() {
        $this->resetAfterTest(true);
        set_config('enableglobalsearch', true);

        $this->mycoursesareaid = \core_search\manager::generate_areaid('core_course', 'mycourse');
        $this->sectionareaid = \core_search\manager::generate_areaid('core_course', 'section');
        $this->customfieldareaid = \core_search\manager::generate_areaid('core_course', 'customfield');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = testable_core_search::instance();
    }

    /**
     * Indexing my courses contents.
     *
     * @return void
     */
    public function test_mycourses_indexing() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->mycoursesareaid);
        $this->assertInstanceOf('\core_course\search\mycourse', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 'student');

        $record = new stdClass();
        $record->course = $course1->id;

        // All records.
        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $this->assertTrue($recordset->valid());
        $nrecords = 0;
        foreach ($recordset as $record) {
            $this->assertInstanceOf('stdClass', $record);
            $doc = $searcharea->get_document($record);
            $this->assertInstanceOf('\core_search\document', $doc);
            $nrecords++;
        }
        // If there would be an error/failure in the foreach above the recordset would be closed on shutdown.
        $recordset->close();
        $this->assertEquals(3, $nrecords);

        // The +2 is to prevent race conditions.
        $recordset = $searcharea->get_recordset_by_timestamp(time() + 2);

        // No new records.
        $this->assertFalse($recordset->valid());
        $recordset->close();
    }

    /**
     * Tests course indexing support for contexts.
     */
    public function test_mycourses_indexing_contexts() {
        global $DB, $USER, $SITE;

        $searcharea = \core_search\manager::get_search_area($this->mycoursesareaid);

        // Create some courses in categories, and a forum.
        $generator = $this->getDataGenerator();
        $cat1 = $generator->create_category();
        $course1 = $generator->create_course(['category' => $cat1->id]);
        $cat2 = $generator->create_category(['parent' => $cat1->id]);
        $course2 = $generator->create_course(['category' => $cat2->id]);
        $cat3 = $generator->create_category();
        $course3 = $generator->create_course(['category' => $cat3->id]);
        $forum = $generator->create_module('forum', ['course' => $course1->id]);
        $DB->set_field('course', 'timemodified', 0, ['id' => $SITE->id]);
        $DB->set_field('course', 'timemodified', 1, ['id' => $course1->id]);
        $DB->set_field('course', 'timemodified', 2, ['id' => $course2->id]);
        $DB->set_field('course', 'timemodified', 3, ['id' => $course3->id]);

        // Find the first block to use for a block context.
        $blockid = array_values($DB->get_records('block_instances', null, 'id', 'id', 0, 1))[0]->id;
        $blockcontext = context_block::instance($blockid);

        // Check with block context - should be null.
        $this->assertNull($searcharea->get_document_recordset(0, $blockcontext));

        // Check with user context - should be null.
        $this->setAdminUser();
        $usercontext = context_user::instance($USER->id);
        $this->assertNull($searcharea->get_document_recordset(0, $usercontext));

        // Check with module context - should be null.
        $modcontext = context_module::instance($forum->cmid);
        $this->assertNull($searcharea->get_document_recordset(0, $modcontext));

        // Check with course context - should return specified course if timestamp allows.
        $coursecontext = context_course::instance($course3->id);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(3, $coursecontext));
        $this->assertEquals([$course3->id], $results);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(4, $coursecontext));
        $this->assertEquals([], $results);

        // Check with category context - should return course in categories and subcategories.
        $catcontext = context_coursecat::instance($cat1->id);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(0, $catcontext));
        $this->assertEquals([$course1->id, $course2->id], $results);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(2, $catcontext));
        $this->assertEquals([$course2->id], $results);

        // Check with system context and null - should return all these courses + site course.
        $systemcontext = context_system::instance();
        $results = self::recordset_to_ids($searcharea->get_document_recordset(0, $systemcontext));
        $this->assertEquals([$SITE->id, $course1->id, $course2->id, $course3->id], $results);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(0, null));
        $this->assertEquals([$SITE->id, $course1->id, $course2->id, $course3->id], $results);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(3, $systemcontext));
        $this->assertEquals([$course3->id], $results);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(3, null));
        $this->assertEquals([$course3->id], $results);
    }

    /**
     * Utility function to convert recordset to array of IDs for testing.
     *
     * @param moodle_recordset $rs Recordset to convert (and close)
     * @return array Array of IDs from records indexed by number (0, 1, 2, ...)
     */
    protected static function recordset_to_ids(moodle_recordset $rs) {
        $results = [];
        foreach ($rs as $rec) {
            $results[] = $rec->id;
        }
        $rs->close();
        return $results;
    }

    /**
     * Document contents.
     *
     * @return void
     */
    public function test_mycourses_document() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->mycoursesareaid);
        $this->assertInstanceOf('\core_course\search\mycourse', $searcharea);

        $user = self::getDataGenerator()->create_user();
        $course = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'teacher');

        $doc = $searcharea->get_document($course);
        $this->assertInstanceOf('\core_search\document', $doc);
        $this->assertEquals($course->id, $doc->get('itemid'));
        $this->assertEquals($this->mycoursesareaid . '-' . $course->id, $doc->get('id'));
        $this->assertEquals($course->id, $doc->get('courseid'));
        $this->assertFalse($doc->is_set('userid'));
        $this->assertEquals(\core_search\manager::NO_OWNER_ID, $doc->get('owneruserid'));
        $this->assertEquals($course->fullname, $doc->get('title'));

        // Not nice. Applying \core_search\document::set line breaks clean up.
        $summary = preg_replace("/\s+/u", " ", content_to_text($course->summary, $course->summaryformat));
        $this->assertEquals($summary, $doc->get('content'));
        $this->assertEquals($course->shortname, $doc->get('description1'));
    }

    /**
     * Document accesses.
     *
     * @return void
     */
    public function test_mycourses_access() {

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->mycoursesareaid);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course(array('visible' => 0));
        $course3 = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, 'student');

        $this->setUser($user1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($course1->id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($course2->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($course3->id));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access(-123));

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($course1->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($course2->id));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($course3->id));
    }

    /**
     * Indexing section contents.
     */
    public function test_section_indexing() {
        global $DB, $USER;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->sectionareaid);
        $this->assertInstanceOf('\core_course\search\section', $searcharea);

        // Create some courses in categories, and a forum.
        $generator = $this->getDataGenerator();
        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category(['parent' => $cat1->id]);
        $course1 = $generator->create_course(['category' => $cat1->id]);
        $course2 = $generator->create_course(['category' => $cat2->id]);
        $forum = $generator->create_module('forum', ['course' => $course1->id]);

        // Edit 2 sections on course 1 and one on course 2.
        $existing = $DB->get_record('course_sections', ['course' => $course1->id, 'section' => 2]);
        $course1section2id = $existing->id;
        $new = clone($existing);
        $new->name = 'Frogs';
        course_update_section($course1->id, $existing, $new);

        $existing = $DB->get_record('course_sections', ['course' => $course1->id, 'section' => 3]);
        $course1section3id = $existing->id;
        $new = clone($existing);
        $new->summary = 'Frogs';
        $new->summaryformat = FORMAT_HTML;
        course_update_section($course1->id, $existing, $new);

        $existing = $DB->get_record('course_sections', ['course' => $course2->id, 'section' => 1]);
        $course2section1id = $existing->id;
        $new = clone($existing);
        $new->summary = 'Frogs';
        $new->summaryformat = FORMAT_HTML;
        course_update_section($course2->id, $existing, $new);

        // Bodge timemodified into a particular order.
        $DB->set_field('course_sections', 'timemodified', 1, ['id' => $course1section3id]);
        $DB->set_field('course_sections', 'timemodified', 2, ['id' => $course1section2id]);
        $DB->set_field('course_sections', 'timemodified', 3, ['id' => $course2section1id]);

        // All records.
        $results = self::recordset_to_ids($searcharea->get_document_recordset(0));
        $this->assertEquals([$course1section3id, $course1section2id, $course2section1id], $results);

        // Records after time 2.
        $results = self::recordset_to_ids($searcharea->get_document_recordset(2));
        $this->assertEquals([$course1section2id, $course2section1id], $results);

        // Records after time 10 (there aren't any).
        $results = self::recordset_to_ids($searcharea->get_document_recordset(10));
        $this->assertEquals([], $results);

        // Find the first block to use for a block context.
        $blockid = array_values($DB->get_records('block_instances', null, 'id', 'id', 0, 1))[0]->id;
        $blockcontext = context_block::instance($blockid);

        // Check with block context - should be null.
        $this->assertNull($searcharea->get_document_recordset(0, $blockcontext));

        // Check with user context - should be null.
        $this->setAdminUser();
        $usercontext = context_user::instance($USER->id);
        $this->assertNull($searcharea->get_document_recordset(0, $usercontext));

        // Check with module context - should be null.
        $modcontext = context_module::instance($forum->cmid);
        $this->assertNull($searcharea->get_document_recordset(0, $modcontext));

        // Check with course context - should return specific course entries.
        $coursecontext = context_course::instance($course1->id);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(0, $coursecontext));
        $this->assertEquals([$course1section3id, $course1section2id], $results);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(2, $coursecontext));
        $this->assertEquals([$course1section2id], $results);

        // Check with category context - should return course in categories and subcategories.
        $catcontext = context_coursecat::instance($cat1->id);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(0, $catcontext));
        $this->assertEquals([$course1section3id, $course1section2id, $course2section1id], $results);
        $catcontext = context_coursecat::instance($cat2->id);
        $results = self::recordset_to_ids($searcharea->get_document_recordset(0, $catcontext));
        $this->assertEquals([$course2section1id], $results);

        // Check with system context - should return everything (same as null, tested first).
        $systemcontext = context_system::instance();
        $results = self::recordset_to_ids($searcharea->get_document_recordset(0, $systemcontext));
        $this->assertEquals([$course1section3id, $course1section2id, $course2section1id], $results);
    }

    /**
     * Document contents for sections.
     */
    public function test_section_document() {
        global $DB;

        $searcharea = \core_search\manager::get_search_area($this->sectionareaid);

        // Create a course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Test with default title.
        $sectionrec = (object)['id' => 123, 'course' => $course->id,
                'section' => 3, 'timemodified' => 456,
                'summary' => 'Kermit', 'summaryformat' => FORMAT_HTML];
        $doc = $searcharea->get_document($sectionrec);
        $this->assertInstanceOf('\core_search\document', $doc);
        $this->assertEquals(123, $doc->get('itemid'));
        $this->assertEquals($this->sectionareaid . '-123', $doc->get('id'));
        $this->assertEquals($course->id, $doc->get('courseid'));
        $this->assertFalse($doc->is_set('userid'));
        $this->assertEquals(\core_search\manager::NO_OWNER_ID, $doc->get('owneruserid'));
        $this->assertEquals('Topic 3', $doc->get('title'));
        $this->assertEquals('Kermit', $doc->get('content'));

        // Test with user-set title.
        $DB->set_field('course_sections', 'name', 'Frogs',
                ['course' => $course->id, 'section' => 3]);
        rebuild_course_cache($course->id, true);
        $doc = $searcharea->get_document($sectionrec);
        $this->assertEquals('Frogs', $doc->get('title'));
    }

    /**
     * Document access for sections.
     */
    public function test_section_access() {
        global $DB;

        $searcharea = \core_search\manager::get_search_area($this->sectionareaid);

        // Create a course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create 2 users - student and manager. Initially, student is not even enrolled.
        $student = $generator->create_user();
        $manager = $generator->create_user();
        $generator->enrol_user($manager->id, $course->id, 'manager');

        // Two sections have content - one is hidden.
        $DB->set_field('course_sections', 'name', 'Frogs',
                ['course' => $course->id, 'section' => 1]);
        $DB->set_field('course_sections', 'name', 'Toads',
                ['course' => $course->id, 'section' => 2]);
        $DB->set_field('course_sections', 'visible', '0',
                ['course' => $course->id, 'section' => 2]);

        // Make the modified time be in order of sections.
        $DB->execute('UPDATE {course_sections} SET timemodified = section');

        // Get the two document objects.
        $rs = $searcharea->get_document_recordset();
        $documents = [];
        $index = 0;
        foreach ($rs as $rec) {
            $documents[$index++] = $searcharea->get_document($rec);
        }
        $this->assertCount(2, $documents);

        // Log in as admin and check access.
        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED,
                $searcharea->check_access($documents[0]->get('itemid')));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED,
                $searcharea->check_access($documents[1]->get('itemid')));

        // Log in as manager and check access.
        $this->setUser($manager);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED,
                $searcharea->check_access($documents[0]->get('itemid')));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED,
                $searcharea->check_access($documents[1]->get('itemid')));

        // Log in as student and check access - none yet.
        $this->setUser($student);
        $this->assertEquals(\core_search\manager::ACCESS_DENIED,
                $searcharea->check_access($documents[0]->get('itemid')));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED,
                $searcharea->check_access($documents[1]->get('itemid')));

        // Enrol student - now they should get access but not to the hidden one.
        $generator->enrol_user($student->id, $course->id, 'student');
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED,
                $searcharea->check_access($documents[0]->get('itemid')));
        $this->assertEquals(\core_search\manager::ACCESS_DENIED,
                $searcharea->check_access($documents[1]->get('itemid')));

        // Delete the course and check it returns deleted.
        delete_course($course, false);
        $this->assertEquals(\core_search\manager::ACCESS_DELETED,
                $searcharea->check_access($documents[0]->get('itemid')));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED,
                $searcharea->check_access($documents[1]->get('itemid')));
    }

    /**
     * Indexing custom fields contents.
     *
     * @return void
     */
    public function test_customfield_indexing() {
        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->customfieldareaid);
        $this->assertInstanceOf('\core_course\search\customfield', $searcharea);

        // We need to be admin for custom fields creation.
        $this->setAdminUser();

        // Custom fields.
        $fieldcategory = self::getDataGenerator()->create_custom_field_category(['name' => 'Other fields']);
        $customfield = ['shortname' => 'test', 'name' => 'Customfield', 'type' => 'text',
            'categoryid' => $fieldcategory->get('id')];
        $field = self::getDataGenerator()->create_custom_field($customfield);

        $course1data = ['customfields' => [['shortname' => $customfield['shortname'], 'value' => 'Customvalue1']]];
        $course1  = self::getDataGenerator()->create_course($course1data);

        $course2data = ['customfields' => [['shortname' => $customfield['shortname'], 'value' => 'Customvalue2']]];
        $course2 = self::getDataGenerator()->create_course($course2data);

        // All records.
        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $this->assertTrue($recordset->valid());
        $nrecords = 0;
        foreach ($recordset as $record) {
            $this->assertInstanceOf('stdClass', $record);
            $doc = $searcharea->get_document($record);
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
    }

    /**
     * Document contents for custom fields.
     *
     * @return void
     */
    public function test_customfield_document() {
        global $DB;
        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->customfieldareaid);

        // We need to be admin for custom fields creation.
        $this->setAdminUser();

        // Custom fields.
        $fieldcategory = self::getDataGenerator()->create_custom_field_category(['name' => 'Other fields']);
        $customfield = ['shortname' => 'test', 'name' => 'Customfield', 'type' => 'text',
            'categoryid' => $fieldcategory->get('id')];
        $field = self::getDataGenerator()->create_custom_field($customfield);

        $coursedata = ['customfields' => [['shortname' => $customfield['shortname'], 'value' => 'Customvalue1']]];
        $course  = self::getDataGenerator()->create_course($coursedata);

        // Retrieve data we need to compare with document instance.
        $record = $DB->get_record('customfield_data', ['instanceid' => $course->id]);
        $field = \core_customfield\field_controller::create($record->fieldid);
        $data = \core_customfield\data_controller::create(0, $record, $field);

        $doc = $searcharea->get_document($record);
        $this->assertInstanceOf('\core_search\document', $doc);
        $this->assertEquals('Customfield', $doc->get('title'));
        $this->assertEquals('Customvalue1', $doc->get('content'));
        $this->assertEquals($course->id, $doc->get('courseid'));
        $this->assertEquals(\core_search\manager::NO_OWNER_ID, $doc->get('owneruserid'));
        $this->assertEquals($course->id, $doc->get('courseid'));
        $this->assertFalse($doc->is_set('userid'));
    }

    /**
     * Test document icon for mycourse area.
     */
    public function test_get_doc_icon_for_mycourse_area() {
        $searcharea = \core_search\manager::get_search_area($this->mycoursesareaid);

        $document = $this->getMockBuilder('\core_search\document')
            ->disableOriginalConstructor()
            ->getMock();

        $result = $searcharea->get_doc_icon($document);

        $this->assertEquals('i/course', $result->get_name());
        $this->assertEquals('moodle', $result->get_component());
    }

    /**
     * Test document icon for section area.
     */
    public function test_get_doc_icon_for_section_area() {
        $searcharea = \core_search\manager::get_search_area($this->sectionareaid);

        $document = $this->getMockBuilder('\core_search\document')
            ->disableOriginalConstructor()
            ->getMock();

        $result = $searcharea->get_doc_icon($document);

        $this->assertEquals('i/section', $result->get_name());
        $this->assertEquals('moodle', $result->get_component());
    }

    /**
     * Test assigned search categories.
     */
    public function test_get_category_names() {
        $coursessearcharea = \core_search\manager::get_search_area($this->mycoursesareaid);
        $sectionsearcharea = \core_search\manager::get_search_area($this->sectionareaid);

        $this->assertEquals(['core-courses'], $coursessearcharea->get_category_names());
        $this->assertEquals(['core-course-content'], $sectionsearcharea->get_category_names());
    }
}
