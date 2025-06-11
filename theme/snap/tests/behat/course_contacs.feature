# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Tests to assure teacher doesn't see a message link for self
#
# @package    theme_snap
# @autor      Oscar Nadjar
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: A teacher should not see the link to message him/herself

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | enablecompletion |
      | Course 1 | C1        | 0        | topics | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | theme_snap_coursefootertoggle | 1 |

  Scenario: As a teacher i should not see a link to message myself
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I should see "Course Contacts"
    And I should see "Teacher 1" in the "#snap-course-footer li" "css_element"
    And I should see "Teacher 2" in the "#snap-course-footer li:nth-child(3)" "css_element"
    And I should not see "message" in the "#snap-course-footer li" "css_element"
    And I should see "message" in the "#snap-course-footer li:nth-child(3)" "css_element"
    Then I log out
    And I log in as "teacher2"
    And I am on the course main page for "C1"
    And I should see "Course Contacts"
    And I should see "Teacher 1" in the "#snap-course-footer li" "css_element"
    And I should see "Teacher 2" in the "#snap-course-footer li:nth-child(3)" "css_element"
    And I should see "message" in the "#snap-course-footer li" "css_element"
    And I should not see "message" in the "#snap-course-footer li:nth-child(3)" "css_element"

  Scenario: As a student i should see a link to message any teacher
    Given I log in as "student1"
    And I am on the course main page for "C1"
    And I should see "Course Contacts"
    And I should see "Teacher 1" in the "#snap-course-footer li" "css_element"
    And I should see "Teacher 2" in the "#snap-course-footer li:nth-child(3)" "css_element"
    And I should see "message" in the "#snap-course-footer li" "css_element"
    And I should see "message" in the "#snap-course-footer li:nth-child(3)" "css_element"
