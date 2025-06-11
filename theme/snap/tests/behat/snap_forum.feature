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
# Tests for visibility of activity restriction tags.
#
# @package    theme_snap
# @copyright Copyright (c) 2020 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap

Feature: When the moodle theme is set to Snap, core forums displays correctly.
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | initsections |
      | Course 1 | C1        | 0        |      1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I add a forum activity to course "C1" section "1" and I fill the form with:
      | Forum name  | Test forum name                |
      | Forum type  | Standard forum for general use |
      | Description | Test forum description         |
      | Whole forum grading > Type | Point           |
    And I log out
    And I log in as "student1"
    And I open the user menu
    And I follow "Preferences"
    And I click on "Forum preference" "link"
    And I set the following fields to these values:
      | Use experimental nested discussion view             | 1 |
    And I press "Save changes"
    And I log out

  @javascript
  Scenario Outline: Settings option is shown correctly
    Given I log in as "<user>"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on ".forum .instancename:contains('Test forum name')" "css_element"
    And "#region-main .action-menu-trigger" "css_element" should <exist>
    Examples:
      | user     | exist     |
      | student1 | exist     |
      | teacher1 | not exist |

  @javascript
  Scenario: Grading Buttons are usable for teachers.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on ".forum .instancename:contains('Test forum name')" "css_element"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 |
      | Message | Discussion contents 1, first message |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on ".forum .instancename:contains('Test forum name')" "css_element"
    And I click on "Grade users" "button"
    And I should see "The grade to award the student"
    And I set the following fields to these values:
      | Grade | 75 |
    And I click on "Save" "button"
    And I wait "1" seconds
    When I click on "Close grader" "button"
    Then I should see "Course 1"
    Then I should see "Test forum name"
    Then I should see "Test forum description"

  @javascript
  Scenario: Unread forum posts label is displayed in the Snap course.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on ".forum .instancename:contains('Test forum name')" "css_element"
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 1 |
      | Message | Discussion contents 1, first message |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion 2 |
      | Message | Discussion contents 2, first message |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should not see "2 unread post"
    And I open the user menu
    And I follow "Preferences"
    And I click on "Forum preference" "link"
    And I set the following fields to these values:
      | Forum tracking| 1 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "2 unread post"
    And I click on ".forum .instancename:contains('Test forum name')" "css_element"
    And I click on "Discussion 1" "link"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should see "1 unread post"
    And I click on ".forum .instancename:contains('Test forum name')" "css_element"
    And I click on "Discussion 2" "link"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    Then I should not see "1 unread post"