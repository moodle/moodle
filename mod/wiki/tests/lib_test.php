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
 * Unit tests for mod_wiki lib
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
namespace mod_wiki;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Unit tests for mod_wiki lib
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
final class lib_test extends \advanced_testcase {

    /**
     * Test wiki_view.
     *
     * @return void
     */
    public function test_wiki_view(): void {
        global $CFG;

        $CFG->enablecompletion = COMPLETION_ENABLED;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => COMPLETION_ENABLED));
        $options = array('completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => COMPLETION_VIEW_REQUIRED);
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id), $options);
        $context = \context_module::instance($wiki->cmid);
        $cm = get_coursemodule_from_instance('wiki', $wiki->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        wiki_view($wiki, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/wiki/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new \completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }

    /**
     * Test wiki_page_view.
     *
     * @return void
     */
    public function test_wiki_page_view(): void {
        global $CFG;

        $CFG->enablecompletion = COMPLETION_ENABLED;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => COMPLETION_ENABLED));
        $options = array('completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => COMPLETION_VIEW_REQUIRED);
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id), $options);
        $context = \context_module::instance($wiki->cmid);
        $cm = get_coursemodule_from_instance('wiki', $wiki->id);
        $firstpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_first_page($wiki);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        wiki_page_view($wiki, $firstpage, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $pageurl = new \moodle_url('/mod/wiki/view.php', array('pageid' => $firstpage->id));
        $this->assertEquals($pageurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new \completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }

    /**
     * Test wiki_user_can_edit without groups.
     *
     * @return void
     */
    public function test_wiki_user_can_edit(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $indwiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id, 'wikimode' => 'individual'));
        $colwiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id, 'wikimode' => 'collaborative'));

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Simulate collaborative subwiki.
        $swcol = new \stdClass();
        $swcol->id = -1;
        $swcol->wikiid = $colwiki->id;
        $swcol->groupid = 0;
        $swcol->userid = 0;

        // Simulate individual subwikis (1 per user).
        $swindstudent = clone($swcol);
        $swindstudent->wikiid = $indwiki->id;
        $swindstudent->userid = $student->id;

        $swindteacher = clone($swindstudent);
        $swindteacher->userid = $teacher->id;

        $this->setUser($student);

        // Check that the student can edit the collaborative subwiki.
        $this->assertTrue(wiki_user_can_edit($swcol));

        // Check that the student can edit his individual subwiki.
        $this->assertTrue(wiki_user_can_edit($swindstudent));

        // Check that the student cannot edit teacher's individual subwiki.
        $this->assertFalse(wiki_user_can_edit($swindteacher));

        // Now test as a teacher.
        $this->setUser($teacher);

        // Check that the teacher can edit the collaborative subwiki.
        $this->assertTrue(wiki_user_can_edit($swcol));

        // Check that the teacher can edit his individual subwiki.
        $this->assertTrue(wiki_user_can_edit($swindteacher));

        // Check that the teacher can edit student's individual subwiki.
        $this->assertTrue(wiki_user_can_edit($swindstudent));

    }

    /**
     * Test wiki_user_can_edit using collaborative wikis with groups.
     *
     * @return void
     */
    public function test_wiki_user_can_edit_with_groups_collaborative(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $wikisepcol = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id,
                                                        'groupmode' => SEPARATEGROUPS, 'wikimode' => 'collaborative'));
        $wikiviscol = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id,
                                                        'groupmode' => VISIBLEGROUPS, 'wikimode' => 'collaborative'));

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $student2 = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Create groups.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2->id, 'groupid' => $group1->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2->id, 'groupid' => $group2->id));

        // Simulate all the possible subwikis.
        // Subwikis in collaborative wikis: 1 subwiki per group + 1 subwiki for all participants.
        $swsepcolg1 = new \stdClass();
        $swsepcolg1->id = -1;
        $swsepcolg1->wikiid = $wikisepcol->id;
        $swsepcolg1->groupid = $group1->id;
        $swsepcolg1->userid = 0;

        $swsepcolg2 = clone($swsepcolg1);
        $swsepcolg2->groupid = $group2->id;

        $swsepcolallparts = clone($swsepcolg1); // All participants.
        $swsepcolallparts->groupid = 0;

        $swviscolg1 = clone($swsepcolg1);
        $swviscolg1->wikiid = $wikiviscol->id;

        $swviscolg2 = clone($swviscolg1);
        $swviscolg2->groupid = $group2->id;

        $swviscolallparts = clone($swviscolg1); // All participants.
        $swviscolallparts->groupid = 0;

        $this->setUser($student);

        // Check that the student can edit his group's subwiki both in separate and visible groups.
        $this->assertTrue(wiki_user_can_edit($swsepcolg1));
        $this->assertTrue(wiki_user_can_edit($swviscolg1));

        // Check that the student cannot edit subwiki from group 2 both in separate and visible groups.
        $this->assertFalse(wiki_user_can_edit($swsepcolg2));
        $this->assertFalse(wiki_user_can_edit($swviscolg2));

        // Now test as student 2.
        $this->setUser($student2);

        // Check that the student 2 can edit subwikis from both groups both in separate and visible groups.
        $this->assertTrue(wiki_user_can_edit($swsepcolg1));
        $this->assertTrue(wiki_user_can_edit($swviscolg1));
        $this->assertTrue(wiki_user_can_edit($swsepcolg2));
        $this->assertTrue(wiki_user_can_edit($swviscolg2));

        // Check that the student 2 cannot edit subwikis from all participants.
        $this->assertFalse(wiki_user_can_edit($swsepcolallparts));
        $this->assertFalse(wiki_user_can_edit($swviscolallparts));

        // Now test it as a teacher.
        $this->setUser($teacher);

        // Check that teacher can edit all subwikis.
        $this->assertTrue(wiki_user_can_edit($swsepcolg1));
        $this->assertTrue(wiki_user_can_edit($swviscolg1));
        $this->assertTrue(wiki_user_can_edit($swsepcolg2));
        $this->assertTrue(wiki_user_can_edit($swviscolg2));
        $this->assertTrue(wiki_user_can_edit($swsepcolallparts));
        $this->assertTrue(wiki_user_can_edit($swviscolallparts));
    }

    /**
     * Test wiki_user_can_edit using individual wikis with groups.
     *
     * @return void
     */
    public function test_wiki_user_can_edit_with_groups_individual(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $wikisepind = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id,
                                                        'groupmode' => SEPARATEGROUPS, 'wikimode' => 'individual'));
        $wikivisind = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id,
                                                        'groupmode' => VISIBLEGROUPS, 'wikimode' => 'individual'));

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $student2 = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Create groups.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2->id, 'groupid' => $group1->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2->id, 'groupid' => $group2->id));

        // Simulate all the possible subwikis.
        // Subwikis in collaborative wikis: 1 subwiki per group + 1 subwiki for all participants.
        $swsepindg1s1 = new \stdClass();
        $swsepindg1s1->id = -1;
        $swsepindg1s1->wikiid = $wikisepind->id;
        $swsepindg1s1->groupid = $group1->id;
        $swsepindg1s1->userid = $student->id;

        $swsepindg1s2 = clone($swsepindg1s1);
        $swsepindg1s2->userid = $student2->id;

        $swsepindg2s2 = clone($swsepindg1s2);
        $swsepindg2s2->groupid = $group2->id;

        $swsepindteacher = clone($swsepindg1s1);
        $swsepindteacher->userid = $teacher->id;
        $swsepindteacher->groupid = 0;

        $swvisindg1s1 = clone($swsepindg1s1);
        $swvisindg1s1->wikiid = $wikivisind->id;

        $swvisindg1s2 = clone($swvisindg1s1);
        $swvisindg1s2->userid = $student2->id;

        $swvisindg2s2 = clone($swvisindg1s2);
        $swvisindg2s2->groupid = $group2->id;

        $swvisindteacher = clone($swvisindg1s1);
        $swvisindteacher->userid = $teacher->id;
        $swvisindteacher->groupid = 0;

        $this->setUser($student);

        // Check that the student can edit his subwiki both in separate and visible groups.
        $this->assertTrue(wiki_user_can_edit($swsepindg1s1));
        $this->assertTrue(wiki_user_can_edit($swvisindg1s1));

        // Check that the student cannot edit subwikis from another user even if he belongs to his group.
        $this->assertFalse(wiki_user_can_edit($swsepindg1s2));
        $this->assertFalse(wiki_user_can_edit($swvisindg1s2));

        // Now test as student 2.
        $this->setUser($student2);

        // Check that the student 2 can edit his subwikis from both groups both in separate and visible groups.
        $this->assertTrue(wiki_user_can_edit($swsepindg1s2));
        $this->assertTrue(wiki_user_can_edit($swvisindg1s2));
        $this->assertTrue(wiki_user_can_edit($swsepindg2s2));
        $this->assertTrue(wiki_user_can_edit($swvisindg2s2));

        // Now test it as a teacher.
        $this->setUser($teacher);

        // Check that teacher can edit all subwikis.
        $this->assertTrue(wiki_user_can_edit($swsepindg1s1));
        $this->assertTrue(wiki_user_can_edit($swsepindg1s2));
        $this->assertTrue(wiki_user_can_edit($swsepindg2s2));
        $this->assertTrue(wiki_user_can_edit($swsepindteacher));
        $this->assertTrue(wiki_user_can_edit($swvisindg1s1));
        $this->assertTrue(wiki_user_can_edit($swvisindg1s2));
        $this->assertTrue(wiki_user_can_edit($swvisindg2s2));
        $this->assertTrue(wiki_user_can_edit($swvisindteacher));
    }

    /**
     * Test wiki_get_visible_subwikis without groups.
     *
     * @return void
     */
    public function test_wiki_get_visible_subwikis_without_groups(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $indwiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id, 'wikimode' => 'individual'));
        $colwiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id, 'wikimode' => 'collaborative'));

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        $this->setUser($student);

        // Check that not passing a wiki returns empty array.
        $result = wiki_get_visible_subwikis(null);
        $this->assertEquals(array(), $result);

        // Check that the student can get the only subwiki from the collaborative wiki.
        $expectedsubwikis = array();
        $expectedsubwiki = new \stdClass();
        $expectedsubwiki->id = -1; // We haven't created any page so the subwiki hasn't been created.
        $expectedsubwiki->wikiid = $colwiki->id;
        $expectedsubwiki->groupid = 0;
        $expectedsubwiki->userid = 0;
        $expectedsubwikis[] = $expectedsubwiki;

        $result = wiki_get_visible_subwikis($colwiki);
        $this->assertEquals($expectedsubwikis, $result);

        // Create a page now so the subwiki is created.
        $colfirstpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_first_page($colwiki);

        // Call the function again, now we expect to have a subwiki ID.
        $expectedsubwikis[0]->id = $colfirstpage->subwikiid;
        $result = wiki_get_visible_subwikis($colwiki);
        $this->assertEquals($expectedsubwikis, $result);

        // Check that the teacher can see it too.
        $this->setUser($teacher);
        $result = wiki_get_visible_subwikis($colwiki);
        $this->assertEquals($expectedsubwikis, $result);

        // Check that the student can only see his subwiki in the individual wiki.
        $this->setUser($student);
        $expectedsubwikis[0]->id = -1;
        $expectedsubwikis[0]->wikiid = $indwiki->id;
        $expectedsubwikis[0]->userid = $student->id;
        $result = wiki_get_visible_subwikis($indwiki);
        $this->assertEquals($expectedsubwikis, $result);

        // Check that the teacher can see his subwiki and the student subwiki in the individual wiki.
        $this->setUser($teacher);
        $teachersubwiki = new \stdClass();
        $teachersubwiki->id = -1;
        $teachersubwiki->wikiid = $indwiki->id;
        $teachersubwiki->groupid = 0;
        $teachersubwiki->userid = $teacher->id;
        $expectedsubwikis[] = $teachersubwiki;

        $result = wiki_get_visible_subwikis($indwiki);
        $this->assertEqualsCanonicalizing($expectedsubwikis, $result); // Compare without order.
    }

    /**
     * Test wiki_get_visible_subwikis using collaborative wikis with groups.
     *
     * @return void
     */
    public function test_wiki_get_visible_subwikis_with_groups_collaborative(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $wikisepcol = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id,
                                                        'groupmode' => SEPARATEGROUPS, 'wikimode' => 'collaborative'));
        $wikiviscol = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id,
                                                        'groupmode' => VISIBLEGROUPS, 'wikimode' => 'collaborative'));

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $student2 = self::getDataGenerator()->create_user();
        $student3 = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Create groups.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2->id, 'groupid' => $group1->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2->id, 'groupid' => $group2->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student3->id, 'groupid' => $group2->id));

        $this->setUser($student);

        // Create all the possible subwikis. We haven't created any page so ids will be -1.
        // Subwikis in collaborative wikis: 1 subwiki per group + 1 subwiki for all participants.
        $swsepcolg1 = new \stdClass();
        $swsepcolg1->id = -1;
        $swsepcolg1->wikiid = $wikisepcol->id;
        $swsepcolg1->groupid = $group1->id;
        $swsepcolg1->userid = 0;

        $swsepcolg2 = clone($swsepcolg1);
        $swsepcolg2->groupid = $group2->id;

        $swsepcolallparts = clone($swsepcolg1); // All participants.
        $swsepcolallparts->groupid = 0;

        $swviscolg1 = clone($swsepcolg1);
        $swviscolg1->wikiid = $wikiviscol->id;

        $swviscolg2 = clone($swviscolg1);
        $swviscolg2->groupid = $group2->id;

        $swviscolallparts = clone($swviscolg1); // All participants.
        $swviscolallparts->groupid = 0;

        // Check that the student can get only the subwiki from his group in collaborative wiki with separate groups.
        $expectedsubwikis = array($swsepcolg1);
        $result = wiki_get_visible_subwikis($wikisepcol);
        $this->assertEquals($expectedsubwikis, $result);

        // Check that he can get subwikis from both groups in collaborative wiki with visible groups, and also all participants.
        $expectedsubwikis = array($swviscolallparts, $swviscolg1, $swviscolg2);
        $result = wiki_get_visible_subwikis($wikiviscol);
        $this->assertEqualsCanonicalizing($expectedsubwikis, $result);

        // Now test it as a teacher. No need to check visible groups wikis because the result is the same as student.
        $this->setUser($teacher);

        // Check that he can get the subwikis from all the groups in collaborative wiki with separate groups.
        $expectedsubwikis = array($swsepcolg1, $swsepcolg2, $swsepcolallparts);
        $result = wiki_get_visible_subwikis($wikisepcol);
        $this->assertEqualsCanonicalizing($expectedsubwikis, $result);
    }

    /**
     * Test wiki_get_visible_subwikis using individual wikis with groups.
     *
     * @return void
     */
    public function test_wiki_get_visible_subwikis_with_groups_individual(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $wikisepind = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id,
                                                        'groupmode' => SEPARATEGROUPS, 'wikimode' => 'individual'));
        $wikivisind = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id,
                                                        'groupmode' => VISIBLEGROUPS, 'wikimode' => 'individual'));

        // Create users.
        $student = self::getDataGenerator()->create_user();
        $student2 = self::getDataGenerator()->create_user();
        $student3 = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        // Create groups.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2->id, 'groupid' => $group1->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student2->id, 'groupid' => $group2->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $student3->id, 'groupid' => $group2->id));

        $this->setUser($student);

        // Create all the possible subwikis to be returned. We haven't created any page so ids will be -1.
        // Subwikis in individual wikis: 1 subwiki per user and group. If user doesn't belong to any group then groupid is 0.
        $swsepindg1s1 = new \stdClass();
        $swsepindg1s1->id = -1;
        $swsepindg1s1->wikiid = $wikisepind->id;
        $swsepindg1s1->groupid = $group1->id;
        $swsepindg1s1->userid = $student->id;

        $swsepindg1s2 = clone($swsepindg1s1);
        $swsepindg1s2->userid = $student2->id;

        $swsepindg2s2 = clone($swsepindg1s2);
        $swsepindg2s2->groupid = $group2->id;

        $swsepindg2s3 = clone($swsepindg1s1);
        $swsepindg2s3->userid = $student3->id;
        $swsepindg2s3->groupid = $group2->id;

        $swsepindteacher = clone($swsepindg1s1);
        $swsepindteacher->userid = $teacher->id;
        $swsepindteacher->groupid = 0;

        $swvisindg1s1 = clone($swsepindg1s1);
        $swvisindg1s1->wikiid = $wikivisind->id;

        $swvisindg1s2 = clone($swvisindg1s1);
        $swvisindg1s2->userid = $student2->id;

        $swvisindg2s2 = clone($swvisindg1s2);
        $swvisindg2s2->groupid = $group2->id;

        $swvisindg2s3 = clone($swvisindg1s1);
        $swvisindg2s3->userid = $student3->id;
        $swvisindg2s3->groupid = $group2->id;

        $swvisindteacher = clone($swvisindg1s1);
        $swvisindteacher->userid = $teacher->id;
        $swvisindteacher->groupid = 0;

        // Check that student can get the subwikis from his group in individual wiki with separate groups.
        $expectedsubwikis = array($swsepindg1s1, $swsepindg1s2);
        $result = wiki_get_visible_subwikis($wikisepind);
        $this->assertEqualsCanonicalizing($expectedsubwikis, $result);

        // Check that he can get subwikis from all users and groups in individual wiki with visible groups.
        $expectedsubwikis = array($swvisindg1s1, $swvisindg1s2, $swvisindg2s2, $swvisindg2s3, $swvisindteacher);
        $result = wiki_get_visible_subwikis($wikivisind);
        $this->assertEqualsCanonicalizing($expectedsubwikis, $result);

        // Now test it as a teacher. No need to check visible groups wikis because the result is the same as student.
        $this->setUser($teacher);

        // Check that teacher can get the subwikis from all the groups in individual wiki with separate groups.
        $expectedsubwikis = array($swsepindg1s1, $swsepindg1s2, $swsepindg2s2, $swsepindg2s3, $swsepindteacher);
        $result = wiki_get_visible_subwikis($wikisepind);
        $this->assertEqualsCanonicalizing($expectedsubwikis, $result);
    }

    public function test_mod_wiki_get_tagged_pages(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $wikigenerator = $this->getDataGenerator()->get_plugin_generator('mod_wiki');
        $course3 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course1 = $this->getDataGenerator()->create_course();
        $wiki1 = $this->getDataGenerator()->create_module('wiki', array('course' => $course1->id));
        $wiki2 = $this->getDataGenerator()->create_module('wiki', array('course' => $course2->id));
        $wiki3 = $this->getDataGenerator()->create_module('wiki', array('course' => $course3->id));
        $page11 = $wikigenerator->create_content($wiki1, array('tags' => array('Cats', 'Dogs')));
        $page12 = $wikigenerator->create_content($wiki1, array('tags' => array('Cats', 'mice')));
        $page13 = $wikigenerator->create_content($wiki1, array('tags' => array('Cats')));
        $page14 = $wikigenerator->create_content($wiki1);
        $page15 = $wikigenerator->create_content($wiki1, array('tags' => array('Cats')));
        $page21 = $wikigenerator->create_content($wiki2, array('tags' => array('Cats')));
        $page22 = $wikigenerator->create_content($wiki2, array('tags' => array('Cats', 'Dogs')));
        $page23 = $wikigenerator->create_content($wiki2, array('tags' => array('mice', 'Cats')));
        $page31 = $wikigenerator->create_content($wiki3, array('tags' => array('mice', 'Cats')));

        $tag = \core_tag_tag::get_by_name(0, 'Cats');

        // Admin can see everything.
        $res = mod_wiki_get_tagged_pages($tag, /*$exclusivemode = */false,
                /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$page = */0);
        $this->assertMatchesRegularExpression('/'.$page11->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page12->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page13->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page14->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page15->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page21->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page22->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page23->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page31->title.'/', $res->content);
        $this->assertEmpty($res->prevpageurl);
        $this->assertNotEmpty($res->nextpageurl);
        $res = mod_wiki_get_tagged_pages($tag, /*$exclusivemode = */false,
                /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$page = */1);
        $this->assertDoesNotMatchRegularExpression('/'.$page11->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page12->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page13->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page14->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page15->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page21->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page22->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page23->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page31->title.'/', $res->content);
        $this->assertNotEmpty($res->prevpageurl);
        $this->assertEmpty($res->nextpageurl);

        // Create and enrol a user.
        $student = self::getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentrole->id, 'manual');
        $this->setUser($student);
        \core_tag_index_builder::reset_caches();

        // User can not see pages in course 3 because he is not enrolled.
        $res = mod_wiki_get_tagged_pages($tag, /*$exclusivemode = */false,
                /*$fromctx = */0, /*$ctx = */0, /*$rec = */1, /*$page = */1);
        $this->assertMatchesRegularExpression('/'.$page22->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page23->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page31->title.'/', $res->content);

        // User can search wiki pages inside a course.
        $coursecontext = \context_course::instance($course1->id);
        $res = mod_wiki_get_tagged_pages($tag, /*$exclusivemode = */false,
                /*$fromctx = */0, /*$ctx = */$coursecontext->id, /*$rec = */1, /*$page = */0);
        $this->assertMatchesRegularExpression('/'.$page11->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page12->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page13->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page14->title.'/', $res->content);
        $this->assertMatchesRegularExpression('/'.$page15->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page21->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page22->title.'/', $res->content);
        $this->assertDoesNotMatchRegularExpression('/'.$page23->title.'/', $res->content);
        $this->assertEmpty($res->nextpageurl);
    }

    public function test_wiki_core_calendar_provide_event_action(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $wiki->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_wiki_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_wiki_core_calendar_provide_event_action_for_non_user(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $wiki->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_wiki_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_wiki_core_calendar_provide_event_action_for_user(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id));
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $wiki->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_wiki_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_wiki_core_calendar_provide_event_action_already_completed(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('wiki', $wiki->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $wiki->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_wiki_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_wiki_core_calendar_provide_event_action_already_completed_for_user(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id),
                array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Create 2 students and enrol them into the course.
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('wiki', $wiki->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $wiki->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the $student1.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm, $student1->id);

        // Now log in as $student2.
        $this->setUser($student2);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for $student1.
        $actionevent = mod_wiki_core_calendar_provide_event_action($event, $factory, $student1->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new \stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'wiki';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return \calendar_event::create($event);
    }
}
