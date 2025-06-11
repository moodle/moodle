# This file is part of Moodle - https://moodle.org/
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
# along with Moodle. If not, see <https://www.gnu.org/licenses/>.
#
# Test to verify the rendering of the Page Activity expand
# icon on the Course page based on its configured settings.
#
# @package    theme_snap
# @copyright  Copyright (c) 2025 Open LMS (https://www.openlms.net)
# @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: Show or hide the expand icon on the Course page based on whether the Page Activity description is empty or not

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | initsections |
      | Course 1 | C1        | 0        | topics | 1            |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | TeacherFN   | TeacherLN | teacher1@example.com |
      | student1 | StudentFN   | StudentLN | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name     | intro           | content     | course | idnumber | section |
      | page     | PageName | PageDescription | PageContent | C1     | page1    | 0       |

  @javascript
  Scenario: Remove Page Activity description to verify expand icon visibility
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    Then ".readmore-container" "css_element" should exist
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    Then ".readmore-container" "css_element" should exist
    And I log out
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on ".modtype_page button.snap-edit-asset-more" "css_element"
    And I follow "Edit settings"
    And I set the field "Description" to ""
    And I press "Save and return to course"
    Then ".readmore-container" "css_element" should not exist
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    Then ".readmore-container" "css_element" should not exist
