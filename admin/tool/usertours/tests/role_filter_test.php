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
 * Tests for role filter.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for role filter.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_usertours_role_filter_testcase extends advanced_testcase {

    /**
     * @var $course Test course
     */
    protected $course;

    /**
     * @var $student Test student
     */
    protected $student;

    /**
     * @var $teacher Test teacher
     */
    protected $teacher;

    /**
     * @var $editingteacher Test editor
     */
    protected $editingteacher;

    /**
     * @var $roles List of all roles
     */
    protected $roles;

    public function setUp() {
        global $DB;

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $this->course = $generator->create_course();
        $this->roles = $DB->get_records_menu('role', [], null, 'shortname, id');
        $this->testroles = ['student', 'teacher', 'editingteacher'];

        foreach ($this->testroles as $role) {
            $user = $this->$role = $generator->create_user();
            $generator->enrol_user($user->id, $this->course->id, $this->roles[$role]);
        }
    }

    /**
     * Test the filter_matches function when any is set.
     */
    public function test_filter_matches_any() {
        $context = \context_course::instance($this->course->id);

        // Note: No need to persist this tour.
        $tour = new \tool_usertours\tour();
        $tour->set_filter_values('role', []);

        // Note: The role filter does not use the context.
        foreach ($this->testroles as $role) {
            $this->setUser($this->$role);
            $this->assertTrue(\tool_usertours\local\filter\role::filter_matches($tour, $context));
        }

        // The admin should always be able to view too.
        $this->setAdminUser();
        $this->assertTrue(\tool_usertours\local\filter\role::filter_matches($tour, $context));
    }

    /**
     * Test the filter_matches function when one role is set.
     */
    public function test_filter_matches_single_role() {
        $context = \context_course::instance($this->course->id);

        $roles = [
            $this->roles['student'],
        ];

        // Note: No need to persist this tour.
        $tour = new \tool_usertours\tour();
        $tour->set_filter_values('role', $roles);

        // Note: The role filter does not use the context.
        foreach ($this->testroles as $role) {
            $this->setUser($this->$role);
            if ($role === 'student') {
                $this->assertTrue(\tool_usertours\local\filter\role::filter_matches($tour, $context));
            } else {
                $this->assertFalse(\tool_usertours\local\filter\role::filter_matches($tour, $context));
            }
        }

        // The admin should always be able to view too.
        $this->setAdminUser();
        $this->assertTrue(\tool_usertours\local\filter\role::filter_matches($tour, $context));
    }

    /**
     * Test the filter_matches function when multiple roles are set.
     */
    public function test_filter_matches_multiple_role() {
        $context = \context_course::instance($this->course->id);

        $roles = [
            $this->roles['teacher'],
            $this->roles['editingteacher'],
        ];

        // Note: No need to persist this tour.
        $tour = new \tool_usertours\tour();
        $tour->set_filter_values('role', $roles);

        // Note: The role filter does not use the context.
        foreach ($this->testroles as $role) {
            $this->setUser($this->$role);
            if ($role === 'student') {
                $this->assertFalse(\tool_usertours\local\filter\role::filter_matches($tour, $context));
            } else {
                $this->assertTrue(\tool_usertours\local\filter\role::filter_matches($tour, $context));
            }
        }

        // The admin should always be able to view too.
        $this->setAdminUser();
        $this->assertTrue(\tool_usertours\local\filter\role::filter_matches($tour, $context));
    }

    /**
     * Test the filter_matches function when one user has multiple roles.
     */
    public function test_filter_matches_multiple_role_one_user() {
        $context = \context_course::instance($this->course->id);

        $roles = [
            $this->roles['student'],
        ];

        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->roles['teacher']);

        // Note: No need to persist this tour.
        $tour = new \tool_usertours\tour();
        $tour->set_filter_values('role', $roles);


        // Note: The role filter does not use the context.
        foreach ($this->testroles as $role) {
            $this->setUser($this->$role);
            if ($role === 'student') {
                $this->assertTrue(\tool_usertours\local\filter\role::filter_matches($tour, $context));
            } else {
                $this->assertFalse(\tool_usertours\local\filter\role::filter_matches($tour, $context));
            }
        }

        // The admin should always be able to view too.
        $this->setAdminUser();
        $this->assertTrue(\tool_usertours\local\filter\role::filter_matches($tour, $context));
    }
}
