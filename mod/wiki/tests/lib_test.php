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
 * @since      Moodle 3.0.3
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');

/**
 * Unit tests for mod_wiki lib
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0.3
 */
class mod_wiki_lib_testcase extends advanced_testcase {

    /**
     * Test wiki_user_can_edit without groups.
     *
     * @return void
     */
    public function test_wiki_user_can_edit() {
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
        $swcol = new stdClass();
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
    public function test_wiki_user_can_edit_with_groups_collaborative() {
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
        $swsepcolg1 = new stdClass();
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
    public function test_wiki_user_can_edit_with_groups_individual() {
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
        $swsepindg1s1 = new stdClass();
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
}
