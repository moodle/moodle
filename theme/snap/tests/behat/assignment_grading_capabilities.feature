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
# Tests for visibility of grading activities only if user have grading capabilities.
#
# @package    theme_snap
# @copyright  Copyright (c) 2018 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_grading
Feature: When the moodle theme is set to Snap, grading activities are shown only if user have grading capabilities.

  Background:
    Given I log in as "admin"
    And I navigate to "Users > Permissions > Define roles" in current page administration
    And I click on "Add a new role" "button"
    And I set the field with xpath "//select[@id = 'id_resettype']" to "Teacher"
    And I click on "Continue" "button"
    And I set the following fields to these values:
      | Short name | nograder |
      | Custom full name | No grader |
      | mod/assign:grade | 0 |
    And I click on "Create this role" "button"
    Then the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
      | Course 2 | C2 | topics |
    And the following "course enrolments" exist:
      | user        | course | role     |
      | teacher1    | C1     | teacher  |
      | teacher2    | C1     | nograder |
      | student1    | C1     | student  |
      | teacher1    | C2     | teacher  |
      | teacher2    | C2     | teacher  |
      | student1    | C2     | student  |
    And the following "activities" exist:
      | activity | course | idnumber | name             | intro             | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes | submissiondrafts |
      | assign   | C1     | assign1  | Assignment One   | Test assignment 1 | 1                             | 1                              | 0                                  | 0                |
      | assign   | C2     | assign2  | Assignment Two   | Test assignment 2 | 1                             | 1                              | 0                                  | 0                |
    Then I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "//a[@class='mod-link']//p[text()='Assignment One']" "xpath_element"
    And I reload the page
    When I press "Add submission"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    Then I log out
    Given I log in as "student1"
    And I am on "Course 2" course homepage
    And I click on "//a[@class='mod-link']//p[text()='Assignment Two']" "xpath_element"
    And I reload the page
    When I press "Add submission"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    Then I log out

  @javascript
  Scenario: User sees empty grading section
    Given I log in as "teacher1"
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I should see "Assignment One"
    And I should see "Assignment Two"
    Then I log out
    Given I log in as "teacher2"
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I should not see "Assignment One"
    And I should see "Assignment Two"
