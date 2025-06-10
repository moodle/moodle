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
# Tests for Ally filter assignment additional files javascript.
#
# @package   filter_ally
# @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@filter @filter_ally
Feature: In an assignment, rich content should have annotations.

  Background:
    Given the ally filter is enabled

  @javascript
  Scenario: Assignment main page rich content is annotated.
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | teacher1 | C1     | teacher |
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment with single html element |
      | Description     | <p>Submit your papers here<br> <i>so they are</i> <br> <strong>graded</strong> </p> |
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment with multiple html elements |
      | Description     | <p>Submit your papers here</p> <br> <i>so they are</i> <br> <strong>graded</strong> |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment with single html element"
    Then "assign" "intro" content is annotated on "div" tag
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment with multiple html elements"
    Then "assign" "intro" content is annotated on "div" tag
    And I log out

