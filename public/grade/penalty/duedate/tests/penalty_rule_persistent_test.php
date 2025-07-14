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

namespace gradepenalty_duedate;

use context_course;
use context_system;
use gradepenalty_duedate\tests\penalty_testcase;

/**
 * Test penalty rule persistent.
 *
 * @package   gradepenalty_duedate
 * @copyright 2024 Catalyst IT Australia Pty Ltd
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \gradepenalty_duedate\penalty_rule
 */
final class penalty_rule_persistent_test extends penalty_testcase {
    /**
     * Test get rules.
     */
    public function test_get_rules(): void {
        $this->resetAfterTest();
        $this->create_sample_rules();

        $course = $this->getDataGenerator()->create_course();

        $systemcontextid = context_system::instance()->id;
        $coursecontextid = context_course::instance($course->id)->id;

        // Check system context penalty rules.
        $rules = penalty_rule::get_rules($systemcontextid);
        $this->assertCount(5, $rules);
        $this->assertEquals(10, $rules[0]->get('penalty'));
        $this->assertEquals(20, $rules[1]->get('penalty'));
        $this->assertEquals(30, $rules[2]->get('penalty'));
        $this->assertEquals(40, $rules[3]->get('penalty'));
        $this->assertEquals(50, $rules[4]->get('penalty'));

        // Check course context penalty rules.
        $rules = penalty_rule::get_records(['contextid' => $coursecontextid]);
        $this->assertCount(0, $rules);

        // Verify the rules are inherited.
        $rules = penalty_rule::get_rules($coursecontextid);
        $this->assertCount(5, $rules);
        $this->assertEquals(10, $rules[0]->get('penalty'));
        $this->assertEquals(20, $rules[1]->get('penalty'));
        $this->assertEquals(30, $rules[2]->get('penalty'));
        $this->assertEquals(40, $rules[3]->get('penalty'));
        $this->assertEquals(50, $rules[4]->get('penalty'));
    }

    /**
     * Test reset rules.
     */
    public function test_reset_rules(): void {
        $this->resetAfterTest();
        $this->create_sample_rules();
        $systemcontextid = context_system::instance()->id;
        penalty_rule::reset_rules($systemcontextid);
        $rules = penalty_rule::get_rules($systemcontextid);
        // Default 0% rule.
        $this->assertCount(1, $rules);
        $this->assertEquals(0, $rules[0]->get('penalty'));
    }

    /**
     * Test check if rules are overridden.
     */
    public function test_is_overridden(): void {
        $this->resetAfterTest();
        // System context penalty rules are never considered to be overridden.
        $systemcontextid = context_system::instance()->id;
        $this->create_sample_rules();
        $this->assertFalse(penalty_rule::is_overridden($systemcontextid));

        $course = $this->getDataGenerator()->create_course();
        $coursecontextid = context_course::instance($course->id)->id;

        // Verify the course with no rules is not considered overridden.
        $this->assertFalse(penalty_rule::is_overridden($coursecontextid));

        // Add penalty rules to the course context and verify they are considered overridden.
        $this->create_sample_rules($coursecontextid);
        $this->assertTrue(penalty_rule::is_overridden($coursecontextid));
    }

    /**
     * Test check if rules are inherited.
     */
    public function test_is_inherited(): void {
        $this->resetAfterTest();
        // System context.
        $systemcontextid = context_system::instance()->id;
        $this->create_sample_rules();
        $this->assertFalse(penalty_rule::is_inherited($systemcontextid));

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontextid = context_course::instance($course->id)->id;

        // There is no rules created at course context, so they are inherited rules.
        $this->assertTrue(penalty_rule::is_inherited($coursecontextid));

        // Create sample rules at course context, they are not considered inherited.
        $this->create_sample_rules($coursecontextid);
        $this->assertFalse(penalty_rule::is_inherited($coursecontextid));

        // Remove the rules from the parent context.
        penalty_rule::reset_rules($systemcontextid);
        $this->assertFalse(penalty_rule::is_inherited($coursecontextid));
    }
}
