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
 * The module forums external functions unit tests
 *
 * @package    mod_forum
 * @category   external
 * @copyright  2012 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

class mod_forum_external_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;

        require_once($CFG->dirroot . '/mod/forum/externallib.php');
    }

    /**
     * Test get forums
     */
    public function test_mod_forum_get_forums_by_courses() {
        global $USER, $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user();

        // Set to the user.
        self::setUser($user);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // First forum.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);

        // Second forum.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course2->id;
        $forum2 = self::getDataGenerator()->create_module('forum', $record);

        // Check the forum was correctly created.
        $this->assertEquals(2, $DB->count_records_select('forum', 'id = :forum1 OR id = :forum2',
                array('forum1' => $forum1->id, 'forum2' => $forum2->id)));

        // Enrol the user in two courses.
        // Enrol them in the first course.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course1->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance1 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance1, $user->id);
        // Now enrol into the second course.
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $user->id);

        // Assign capabilities to view forums for forum 1.
        $cm1 = get_coursemodule_from_id('forum', $forum1->id, 0, false, MUST_EXIST);
        $context1 = context_module::instance($cm1->id);
        $roleid1 = $this->assignUserCapability('mod/forum:viewdiscussion', $context1->id);

        // Assign capabilities to view forums for forum 2.
        $cm2 = get_coursemodule_from_id('forum', $forum2->id, 0, false, MUST_EXIST);
        $context2 = context_module::instance($cm2->id);
        $newrole = create_role('Role 2', 'role2', 'Role 2 description');
        $roleid2 = $this->assignUserCapability('mod/forum:viewdiscussion', $context2->id, $newrole);

        // Create what we expect to be returned when querying the two courses.
        $expectedforums = array();
        $expectedforums[$forum1->id] = (array) $forum1;
        $expectedforums[$forum2->id] = (array) $forum2;

        // Call the external function passing course ids.
        $forums = mod_forum_external::get_forums_by_courses(array($course1->id, $course2->id));
        $this->assertEquals($forums, $expectedforums);
        external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);

        // Call the external function without passing course id.
        $forums = mod_forum_external::get_forums_by_courses();
        $this->assertEquals($forums, $expectedforums);
        external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);

        // Unenrol user from second course and alter expected forums.
        $enrol->unenrol_user($instance2, $user->id);
        unset($expectedforums[$forum2->id]);

        // Call the external function without passing course id.
        $forums = mod_forum_external::get_forums_by_courses();
        $this->assertEquals($forums, $expectedforums);
        external_api::clean_returnvalue(mod_forum_external::get_forums_by_courses_returns(), $forums);

        // Call for the second course we unenrolled the user from, make sure exception thrown.
        $this->setExpectedException('require_login_exception');
        mod_forum_external::get_forums_by_courses(array($course2->id));

        // Call without required capability.
        $this->unassignUserCapability('mod/forum:viewdiscussion', $context->id, $roleid1);
        $this->setExpectedException('required_capability_exception');
        mod_forum_external::get_forums_by_courses(array($course1->id));
    }
}
