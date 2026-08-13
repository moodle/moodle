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

namespace qbank_deletequestion;

use core\url;
use core_question\local\bank\question_edit_contexts;
use core_question\local\bank\view;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for delete_action
 *
 * @package   qbank_deletequestion
 * @copyright 2026 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(delete_action::class)]
final class delete_action_test extends \advanced_testcase {
    /**
     * Set up a user, course, question and qbank view for testing.
     *
     * @return array
     */
    public function create_user_and_question(): array {
        $user = $this->getDataGenerator()->create_user();
        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $qbank = self::getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        [, $qbankcm] = get_course_and_cm_from_cmid($qbank->cmid);
        $context = \context_module::instance($qbank->cmid);
        $qgenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcategory = $qgenerator->create_question_category(['contextid' => $context->id]);
        $question = $qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id,
            'name' => 'Question 1']);
        $qbankview = new view(
            new question_edit_contexts($context),
            new url('/mod/qbank/view.php', ['id' => $qbank->id]),
            $course,
            $qbankcm,
        );
        return [$user, $course, $question, $qbankview];
    }

    /**
     * Menu action is a delete link with modal attributes.
     */
    public function test_get_menu_action(): void {
        $this->resetAfterTest();
        [$user, $course, $question, $qbankview] = $this->create_user_and_question();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');
        $this->setUser($user);
        $deleteaction = new delete_action($qbankview);
        $link = $deleteaction->get_action_menu_link($question);
        $this->assertInstanceOf(\action_menu_link_secondary::class, $link);
        $this->assertInstanceOf(\core\url::class, $link->url);
        $this->assertEquals('Delete', $link->text);
        $this->assertEquals('modal', $link->attributes['data-confirmation']);
    }

    /**
     * Menu action is null if user does not have permission to use it.
     */
    public function test_get_menu_action_no_permission(): void {
        $this->resetAfterTest();
        [$user, $course, $question, $qbankview] = $this->create_user_and_question();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');
        $this->setUser($user);
        $deleteaction = new delete_action($qbankview);
        $link = $deleteaction->get_action_menu_link($question);
        $this->assertNull($link);
    }
}
