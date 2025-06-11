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
# Tests for navigation between activities with restrictions.
#
# @package    theme_snap
# @author     2017 Jun Pataleta <jun@moodle.com>
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: Activity navigation involving activities with access restrictions in Snap theme
  In order to quickly switch to another activity that has access restrictions
  As a student
  I need to be able to use the activity navigation feature to access the activity after satisfying its access conditions

  Background:
    Given the following config values are set as admin:
      | theme | snap |
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity    | name          | intro                       | course | idnumber   | section |
      | assign      | Assignment 1  | Test assign description 1   | C1     | assign1    | 0       |
      | forum       | Forum 1       | Test forum description      | C1     | forum1     | 0       |
      | book        | Book 1        | Test book description       | C1     | book1      | 0       |
      | quiz        | Quiz 1        | Test quiz description       | C1     | quiz1      | 0       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    # Set completion for Forum 1.
    And I click on ".modtype_forum .snap-edit-asset-more" "css_element"
    And I click on ".modtype_forum .snap-edit-asset" "css_element"
    And I expand all fieldsets
    And I set the field "None" to "1"
    And I should not see "Expect completed on"
    And I should not see "Start discussions or post replies"
    And I set the field "Students must manually mark the activity as done" to "1"
    And I should see "Set reminder in Timeline"
    And I should not see "Start discussions or post replies"
    And I set the field "Add requirements" to "1"
    And I should see "Set reminder in Timeline"
    And I should see "Start discussions or post replies"
    And I set the field "View the activity" to "1"
    And I press "Save and return to course"
    # Require Forum 1 to be completed first before Book 1 can be accessed.
    And I click on ".modtype_book .snap-edit-asset-more" "css_element"
    And I click on ".modtype_book .snap-edit-asset" "css_element"
    # And I click on "Edit settings" "link" in the "Book 1" activity.
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "//button[text()=\"Activity completion\"]" "xpath_element"
    And I set the field "Activity or resource" to "Forum 1"
    And I press "Save and return to course"
    And I log out

  @javascript
  Scenario: Activity navigation involving activities with access restrictions
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on ".modtype_assign .mod-link" "css_element"
    Then I should see "Forum 1" in the "#next-activity-link" "css_element"
    # Activity that has access restriction should not show up in the dropdown.
    And the "Jump to..." select box should not contain "Book 1"
    And I select "Quiz 1" from the "Jump to..." singleselect
    # Forum 1 should be shown in the previous link since Book 1 is not yet available.
    And I should see "Forum 1" in the "#prev-activity-link" "css_element"
    And the "Jump to..." select box should not contain "Book 1"
    # Navigate to Forum 1.
    And I click on "//div/a[contains(text(),'Forum 1')]" "xpath_element"
    # Since Forum 1 has now been viewed and deemed completed, Book 1 can now be accessed.
    And I should see "Book 1" in the "#next-activity-link" "css_element"
    And the "Jump to..." select box should contain "Book 1"
