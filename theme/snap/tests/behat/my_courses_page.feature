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
# along with Moodle. If not, see <http://www.gnu.org/licenses/>.
#
# Test for Snap's My courses page
#
# @package    theme_snap
# @autor      Daniel Cifuentes
# @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: Users can access to the My Courses page in Snap.

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | student1  | Student    | 1         | student1@example.com  |
      | teacher1 | Teacher     | 1         | teacher1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1 | C1       | editingteacher  |
    Given the following "activities" exist:
      | activity | course | idnumber | name             | intro             | duedate   |
      | assign   | C1     | assign1  | Test assignment1 | Test assignment 1 | ##today## |
    And the following config values are set as admin:
      | defaulthomepage | 3 |
    And the following "permission overrides" exist:
      | capability            | permission | role  | contextlevel | reference |
      | moodle/course:request | Allow      | user  | System       |           |

  @javascript
  Scenario: User can access to course management options in Snap's My Courses page.
    Given I log in as "admin"
    And I should see "Course overview"
    Then ".block_myoverview" "css_element" should exist
    And ".snap-page-my-courses-options .btn-group" "css_element" should exist
    And I should see "Create course"
    And I should see "Manage courses"
    And I should not see "Request a course"
    When I click on "Create course" "button" in the "page-content" "region"
    And I should see "Add a new course"
    And I click on "#snap-home" "css_element"
    When I click on "Manage courses" "button" in the "page-content" "region"
    And I should see "Manage course categories and courses"
    And I log out
    And I log in as "student1"
    And I should see "Course overview"
    Then ".block_myoverview" "css_element" should exist
    And ".snap-page-my-courses-options .btn-group" "css_element" should exist
    And I should not see "Create course"
    And I should not see "Manage courses"
    And I should see "Request a course"
    When I click on "Request a course" "button" in the "page-content" "region"
    And I should see "Details of the course you are requesting"
    And the following config values are set as admin:
      | enablecourserequests | 0 |
    Then I am on site homepage
    And I follow "My Courses"
    And I should see "Course overview"
    Then ".block_myoverview" "css_element" should exist
    And ".snap-page-my-courses-options .btn-group" "css_element" should not exist

  @javascript
  Scenario: User will see a warning message when the Course overview block is disabled.
    Given the following config values are set as admin:
      | defaulthomepage | 3 |
    And I change window size to "large"
    And I log in as "admin"
    And I click on "#admin-menu-trigger" "css_element"
    And I expand "Site administration" node
    And I follow "Plugins"
    And I follow "Category: Blocks"
    And I follow "Manage blocks"
    And I click on "Disable Course overview" "checkbox" in the "Course overview" "table_row"
    And I follow "My Courses"
    Then ".block_myoverview" "css_element" should not exist
    And I should see "The Course overview block is disabled"
    And I click on "#admin-menu-trigger" "css_element"
    And I expand "Site administration" node
    And I follow "Plugins"
    And I follow "Category: Blocks"
    And I follow "Manage blocks"
    And I click on "Enable Course overview" "checkbox" in the "Course overview" "table_row"
    And I follow "My Courses"
    Then ".block_myoverview" "css_element" should exist
    And I should not see "The Course overview block is disabled"

  @javascript
  Scenario: User can use the year filter in the Course overview block in Snap.
    Given I skip because "Will be reviewed on INT-20992"
    Given I log in as "teacher1"
    And I should see "Course overview"
    And "#yeardropdown" "css_element" should not exist
    And the following "courses" exist:
      | fullname | shortname | category | startdate     | enddate      |
      | Course 2 | C2        | 0        | ##-1 years##  | ##+1 years## |
      | Course 3 | C3        | 0        | ##-1 years##  | ##+2 years## |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1 | C2       | editingteacher  |
      | teacher1 | C3       | editingteacher  |
    Then I am on site homepage
    And I follow "My Courses"
    And I should see "Course overview"
    And "#yeardropdown" "css_element" should exist
    And I click on "#yeardropdown" "css_element"
    And "ul[aria-labelledby='yeardropdown'] :nth-child(1)" "css_element" should exist
    And "ul[aria-labelledby='yeardropdown'] :nth-child(2)" "css_element" should exist
    And "ul[aria-labelledby='yeardropdown'] :nth-child(3)" "css_element" should exist
    And "ul[aria-labelledby='yeardropdown'] :nth-child(4)" "css_element" should not exist
    And I click on "ul[aria-labelledby='yeardropdown'] :nth-child(2)" "css_element"
    And I should see "Course 2"
    And I should not see "Course 3"
    And I click on "#yeardropdown" "css_element"
    And I click on "ul[aria-labelledby='yeardropdown'] :nth-child(3)" "css_element"
    And I should see "Course 3"
    And I should not see "Course 2"
    And I click on "#yeardropdown" "css_element"
    And I click on "ul[aria-labelledby='yeardropdown'] :nth-child(1)" "css_element"
    And I should see "Course 2"
    And I should see "Course 3"

  @javascript
  Scenario: User can use the completion filter in the Course overview block in Snap.
    Given I skip because "Will be reviewed on INT-20992"
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 2 | C2        | 0        | 1                |
      | Course 3 | C3        | 0        | 1                |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C2      | editingteacher  |
      | student1  | C2      | student         |
      | student1  | C3      | student         |
    And the following "activities" exist:
      | activity    | name          | intro                       | course | idnumber   | section |
      | assign      | Assignment 1  | Test assign description 1   | C2     | assign1    | 0       |
    Then I log in as "teacher1"
    And I am on "Course 2" course homepage
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    Then I follow "Edit settings"
    And I expand all fieldsets
    And I set the field "Students must manually mark the activity as done" to "1"
    And I press "Save and return to course"
    Then I log in as "student1"
    And I am on "Course 2" course homepage
    And I click on ".snap-activity.assign .mod-link" "css_element"
    When I press "Mark as done"
    And I wait until "Done" "button" exists
    And I follow "My Courses"
    And I click on "#progressdropdown" "css_element"
    And I follow "Completed"
    And I should see "Course 2"
    And I should not see "Course 1"
    And I should not see "Course 3"
    And I click on "#progressdropdown" "css_element"
    And I follow "Not completed"
    And I should not see "Course 2"
    And I should see "Course 1"
    And I should see "Course 3"
    And I click on "#progressdropdown" "css_element"
    And I click on "ul[aria-labelledby='progressdropdown'] :nth-child(1)" "css_element"
    And I should see "Course 1"
    And I should see "Course 2"
    And I should see "Course 3"