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

namespace quizaccess_seb;

/**
 * PHPUnit tests for link_generator.
 *
 * @package   quizaccess_seb
 * @author    Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class link_generator_test extends \advanced_testcase {

    /**
     * Called before every test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test that a http link is generated correctly.
     */
    public function test_http_link_generated(): void {
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $this->assertEquals(
            "http://www.example.com/moodle/mod/quiz/accessrule/seb/config.php?cmid=$quiz->cmid",
            link_generator::get_link($quiz->cmid, false, false));
    }

    /**
     * Test that a http link is generated correctly.
     */
    public function test_https_link_generated(): void {
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $this->assertEquals(
            "https://www.example.com/moodle/mod/quiz/accessrule/seb/config.php?cmid=$quiz->cmid",
            link_generator::get_link($quiz->cmid, false));
    }

    /**
     * Test that a seb link is generated correctly.
     */
    public function test_seb_link_generated(): void {
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $this->assertEquals(
            "seb://www.example.com/moodle/mod/quiz/accessrule/seb/config.php?cmid=$quiz->cmid",
            link_generator::get_link($quiz->cmid, true, false));
    }

    /**
     * Test that a sebs link is generated correctly.
     */
    public function test_sebs_link_generated(): void {
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $this->assertEquals(
            "sebs://www.example.com/moodle/mod/quiz/accessrule/seb/config.php?cmid=$quiz->cmid",
            link_generator::get_link($quiz->cmid, true));
    }

    /**
     * Test that link_generator can't not be instantiated with fake course module.
     */
    public function test_course_module_does_not_exist(): void {
        $this->expectException(\dml_exception::class);
        $this->expectExceptionMessageMatches("/^Can't find data record in database.*/");
        $generator = link_generator::get_link(123456, false);
    }
}
