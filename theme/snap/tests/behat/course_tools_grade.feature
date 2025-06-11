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
# Tests for availability of course tools section.
#
# @package   theme_snap
# @copyright Copyright (c) 2019 Open LMS
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_grading @theme_snap_course
Feature: When the moodle theme is set to Snap, a course tools section is available and it should display correctly
  the grade information about the student.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | teacher        |
    And the following "activities" exist:
      | activity | course | idnumber | name  | intro                         | assignsubmission_onlinetext_enabled |
      | assign   | C1     | assign1  | A1    | Test assignment description 1 | 1                                   |

  @javascript
  Scenario: Course tools should show a default symbol when the student does not have any grade.
    Given I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "-" in the ".progressbar-text" "css_element"
    And I log out

  @javascript
  Scenario: Course tools should display the student grade with the same amount of decimals as Gradebook.
    Given I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Introduction"
    And I should see "A1"
    And I follow "Not Submitted"
    And I reload the page
    And I click on "//*[contains(text(),'Add submission')]" "xpath_element"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I click on "//*[contains(text(),'Submit assignment')]" "xpath_element"
    And I press "Continue"
    And I log out
    Then I log in as "student2"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Introduction"
    And I should see "A1"
    And I follow "Not Submitted"
    And I reload the page
    And I click on "//*[contains(text(),'Add submission')]" "xpath_element"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I click on "//*[contains(text(),'Submit assignment')]" "xpath_element"
    And I press "Continue"
    And I log out
    Then I log in as "teacher1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I grade the assignment "A1" in course "C1" as follows:
      | username | grade       | feedback                 |
      | student1 | 50.32973    | I'm the teacher feedback |
      | student2 | 50.756      | I'm the teacher feedback |
    And I log out
        # By default, Gradebook displays grades with two decimals numbers.
    Then I log in as "admin"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I follow "Gradebook"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Setup > Course grade settings" in current page administration
    And I set the field "Grade display type" to "Percentage"
    And I click on "Save changes" "button"
    And I log out
    Then I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "50.33%" in the ".progressbar-text" "css_element"
    And I follow "Gradebook"
    And I should see "50.33 %" in the "td.column-percentage" "css_element"
    And I log out
    Then I log in as "student2"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "50.76%" in the ".progressbar-text" "css_element"
    And I follow "Gradebook"
    And I should see "50.76 %" in the "td.column-percentage" "css_element"
    And I log out
    Then I log in as "admin"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I follow "Gradebook"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Setup > Course grade settings" in current page administration
    And I set the field "Overall decimal places" to "0"
    And I click on "Save changes" "button"
    And I log out
    Then I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "50%" in the ".progressbar-text" "css_element"
    And I follow "Gradebook"
    And I should see "50 %" in the "td.column-percentage" "css_element"
    And I log out
    Then I log in as "student2"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "51%" in the ".progressbar-text" "css_element"
    And I follow "Gradebook"
    And I should see "51 %" in the "td.column-percentage" "css_element"
    And I log out
    Then I log in as "admin"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I follow "Gradebook"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Setup > Course grade settings" in current page administration
    And I set the field "Overall decimal places" to "3"
    And I click on "Save changes" "button"
    And I log out
    Then I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "50.330%" in the ".progressbar-text" "css_element"
    And I follow "Gradebook"
    And I should see "50.330 %" in the "td.column-percentage" "css_element"
    And I log out
    Then I log in as "student2"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "50.756%" in the ".progressbar-text" "css_element"
    And I follow "Gradebook"
    And I should see "50.756 %" in the "td.column-percentage" "css_element"
    And I log out
    Then I log in as "admin"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I follow "Gradebook"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Setup > Course grade settings" in current page administration
    And I set the field "Overall decimal places" to "4"
    And I click on "Save changes" "button"
    And I log out
    Then I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "50.3297%" in the ".progressbar-text" "css_element"
    And I follow "Gradebook"
    And I should see "50.3297 %" in the "td.column-percentage" "css_element"
    And I log out
    Then I log in as "student2"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "50.7560%" in the ".progressbar-text" "css_element"
    And I follow "Gradebook"
    And I should see "50.7560 %" in the "td.column-percentage" "css_element"
    And I log out

  @javascript
  Scenario: Course tools should display the student grade with a letter when the gradebook is set as a letter for grading.
    Given I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Introduction"
    And I should see "A1"
    And I follow "Not Submitted"
    And I reload the page
    And I click on "//*[contains(text(),'Add submission')]" "xpath_element"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I click on "//*[contains(text(),'Submit assignment')]" "xpath_element"
    And I press "Continue"
    And I log out
    Then I log in as "teacher1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I grade the assignment "A1" in course "C1" as follows:
      | username | grade       | feedback                 |
      | student1 | 50.32973    | I'm the teacher feedback |
    And I log out
    Then I log in as "admin"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I follow "Gradebook"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Setup > Course grade settings" in current page administration
    And I set the field "Grade display type" to "Letter"
    And I click on "Save changes" "button"
    And I log out
    Then I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "F" in the ".progressbar-text" "css_element"
    And I log out
    Then I log in as "admin"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I follow "Gradebook"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Setup > Course grade settings" in current page administration
    And I set the field "Grade display type" to "Letter (real)"
    And I click on "Save changes" "button"
    And I log out
    Then I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "F" in the ".progressbar-text" "css_element"
    And I log out
    Then I log in as "admin"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I follow "Gradebook"
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Setup > Course grade settings" in current page administration
    And I set the field "Grade display type" to "Letter (percentage)"
    And I click on "Save changes" "button"
    And I log out
    Then I log in as "student1"
    And I follow "My Courses"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "F" in the ".progressbar-text" "css_element"
    And I log out

  @javascript
  Scenario: Course grader report should be have edit mode in Snap.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I follow "Gradebook setup"
    And I click on ".tertiary-navigation-selector" "css_element"
    # Check editing button works with Grader report.
    And I navigate to "Grader report" in current page administration
    And "Save changes" "button" should not exist
    And I switch edit mode in Snap
    And "Save changes" "button" should exist
    And I switch edit mode in Snap
    And "Save changes" "button" should not exist
    And I switch edit mode in Snap
    And I am on "Course 1" course homepage
    # Edit mode persit when changing page.
    And I follow "Course Dashboard"
    Then course page should be in edit mode
    And I log out

  @javascript
  Scenario: User Editing mode should remain enabled after saving changes when editing grades through the Edit Grade view.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I follow "Gradebook setup"
    And I click on ".tertiary-navigation-selector" "css_element"
    And I navigate to "Grader report" in current page administration
    And I switch edit mode in Snap
    And I click on "A1" "core_grades > grade_actions" in the "Student 1" "table_row"
    Given I change window size to "320x480"
    And I choose "Edit grade" in the open action menu
    Given I change window size to "medium"
    And I click on "Overridden" "checkbox"
    And I set the field "Final grade" to "10"
    And I press "Save changes"
    And "Save changes" "button" should exist
    And I switch edit mode in Snap
    And "Save changes" "button" should not exist
    And I log out

  @javascript
  Scenario: Grade report single view should have an edit button.
    Given I log in as "admin"
    When I am on "Course 1" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I follow "Gradebook setup"
    And I click on ".tertiary-navigation-selector" "css_element"
    # Check editing button works with Grade report single view.
    And I navigate to "Single view" in current page administration
    # Check grade items view.
    And I click on "Grade items" "link"
    And "input[value='Save']" "css_element" should not exist
    # Select an activity to grade.
    And I click on ".grade-search" "css_element"
    And I click on ".searchresultitemscontainer li" "css_element"
    When I switch edit mode in Snap
    And "input[value='Save']" "css_element" should exist
    When I switch edit mode in Snap
    Then the "input[value='Save']" "css_element" should be disabled
    And I switch edit mode in Snap
    And I am on "Course 1" course homepage
    # Edit mode does persist between courses.
    And I follow "Course Dashboard"
    Then course page should be in edit mode
    And I log out

  @javascript
  Scenario: From Grader report button, single view should have an edit button.
    Given I log in as "admin"
    When I am on "Course 1" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I follow "Gradebook setup"
    And I click on ".tertiary-navigation-selector" "css_element"
    # Check editing button works with Grade report single view.
    And I navigate to "Grader report" in current page administration
    And I click on "tr.userrow .moodle-actionmenu.grader" "css_element"
    And I use js to click on "[aria-label='Single view for this user']"
    # Check student grades view.
    And I should see "Student 1"
    Then the "Save" "button" should be disabled
    # Check editing button works.
    When I switch edit mode in Snap
    Then I should see "Student 1"
    And "Save" "button" should exist
