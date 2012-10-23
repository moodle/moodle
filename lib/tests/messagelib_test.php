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
 * Tests for messagelib.php.
 *
 * @package    core_message
 * @copyright  2012 The Open Universtiy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class messagelib_testcase extends advanced_testcase {

    public function test_message_get_providers_for_user() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $quiz = $generator->create_module('quiz', array('course' => $course->id));
        $user = $generator->create_user();

        $coursecontext = context_course::instance($course->id);
        $quizcontext = context_module::instance($quiz->cmid);
        $frontpagecontext = context_course::instance(SITEID);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // The user is a student in a course, and has the capability for quiz
        // confirmation emails in one quiz in that course.
        role_assign($studentrole->id, $user->id, $coursecontext->id);
        assign_capability('mod/quiz:emailconfirmsubmission', CAP_ALLOW, $studentrole->id, $quizcontext->id);

        // Give this message type to the front page role.
        assign_capability('mod/quiz:emailwarnoverdue', CAP_ALLOW, $CFG->defaultfrontpageroleid, $frontpagecontext->id);

        $providers = message_get_providers_for_user($user->id);
        $this->assertTrue($this->message_type_present('mod_forum', 'posts', $providers));
        $this->assertTrue($this->message_type_present('mod_quiz', 'confirmation', $providers));
        $this->assertTrue($this->message_type_present('mod_quiz', 'attempt_overdue', $providers));
        $this->assertFalse($this->message_type_present('mod_quiz', 'submission', $providers));

        // A user is a student in a different course, they should not get confirmation.
        $course2 = $generator->create_course(array('category' => $cat->id));
        $user2 = $generator->create_user();
        $coursecontext2 = context_course::instance($course2->id);
        role_assign($studentrole->id, $user2->id, $coursecontext2->id);
        accesslib_clear_all_caches_for_unit_testing();
        $providers = message_get_providers_for_user($user2->id);
        $this->assertTrue($this->message_type_present('mod_forum', 'posts', $providers));
        $this->assertFalse($this->message_type_present('mod_quiz', 'confirmation', $providers));

        // Now remove the frontpage role id, and attempt_overdue message should go away.
        unset_config('defaultfrontpageroleid');
        accesslib_clear_all_caches_for_unit_testing();

        $providers = message_get_providers_for_user($user->id);
        $this->assertTrue($this->message_type_present('mod_quiz', 'confirmation', $providers));
        $this->assertFalse($this->message_type_present('mod_quiz', 'attempt_overdue', $providers));
        $this->assertFalse($this->message_type_present('mod_quiz', 'submission', $providers));
    }

    /**
     * Is a particular message type in the list of message types.
     * @param string $name a message name.
     * @param array $providers as returned by message_get_providers_for_user.
     * @return bool whether the message type is present.
     */
    protected function message_type_present($component, $name, $providers) {
        foreach ($providers as $provider) {
            if ($provider->component == $component && $provider->name == $name) {
                return true;
            }
        }
        return false;
    }
}
