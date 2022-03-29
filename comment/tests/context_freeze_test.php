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

namespace core_comment;

use comment;
use comment_exception;
use core_comment_external;

/**
 * Tests for comments when the context is frozen.
 *
 * @package    core_comment
 * @copyright  2019 University of Nottingham
 * @author     Neill Magill <neill.magill@nottingham.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class context_freeze_test extends \advanced_testcase {
    /**
     * Creates a comment by a student.
     *
     * Returns:
     * - The comment object
     * - The sudent that wrote the comment
     * - The arguments used to create the comment
     *
     * @param \stdClass $course Moodle course from the datagenerator
     * @return array
     */
    protected function create_student_comment_and_freeze_course($course): array {
        set_config('contextlocking', 1);

        $context = \context_course::instance($course->id);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $args = new \stdClass;
        $args->context = $context;
        $args->course = $course;
        $args->area = 'page_comments';
        $args->itemid = 0;
        $args->component = 'block_comments';
        $args->linktext = get_string('showcomments');
        $args->notoggle = true;
        $args->autostart = true;
        $args->displaycancel = false;

        // Create a comment by the student.
        $this->setUser($student);
        $comment = new comment($args);
        $newcomment = $comment->add('New comment');

        // Freeze the context.
        $this->setAdminUser();
        $context->set_locked(true);

        return [$newcomment, $student, $args];
    }

    /**
     * Test that a student cannot delete their own comments in frozen contexts via the external service.
     */
    public function test_delete_student_external() {
        global $CFG;
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        list($newcomment, $student, $args) = $this->create_student_comment_and_freeze_course($course);

        // Check that a student cannot delete their own comment.
        $this->setUser($student);
        $studentcomment = new comment($args);
        $this->assertFalse($studentcomment->can_delete($newcomment->id));
        $this->assertFalse($studentcomment->can_post());
        $this->expectException(comment_exception::class);
        $this->expectExceptionMessage(get_string('nopermissiontodelentry', 'error'));
        core_comment_external::delete_comments([$newcomment->id]);
    }

    /**
     * Test that a student cannot delete their own comments in frozen contexts.
     */
    public function test_delete_student() {
        global $CFG;
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        list($newcomment, $student, $args) = $this->create_student_comment_and_freeze_course($course);

        // Check that a student cannot delete their own comment.
        $this->setUser($student);
        $studentcomment = new comment($args);
        $this->assertFalse($studentcomment->can_delete($newcomment->id));
        $this->assertFalse($studentcomment->can_post());
        $this->expectException(comment_exception::class);
        $this->expectExceptionMessage(get_string('nopermissiontocomment', 'error'));
        $studentcomment->delete($newcomment->id);
    }

    /**
     * Test that an admin cannot delete comments in frozen contexts via the external service.
     */
    public function test_delete_admin_external() {
        global $CFG;
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        list($newcomment, $student, $args) = $this->create_student_comment_and_freeze_course($course);

        // Check that the admin user cannot delete the comment.
        $admincomment = new comment($args);
        $this->assertFalse($admincomment->can_delete($newcomment->id));
        $this->assertFalse($admincomment->can_post());
        $this->expectException(comment_exception::class);
        $this->expectExceptionMessage(get_string('nopermissiontodelentry', 'error'));
        core_comment_external::delete_comments([$newcomment->id]);
    }

    /**
     * Test that an admin cannot delete comments in frozen contexts.
     */
    public function test_delete_admin() {
        global $CFG;
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        list($newcomment, $student, $args) = $this->create_student_comment_and_freeze_course($course);

        // Check that the admin user cannot delete the comment.
        $admincomment = new comment($args);
        $this->assertFalse($admincomment->can_delete($newcomment->id));
        $this->assertFalse($admincomment->can_post());
        $this->expectException(comment_exception::class);
        $this->expectExceptionMessage(get_string('nopermissiontocomment', 'error'));
        $admincomment->delete($newcomment->id);
    }
}
