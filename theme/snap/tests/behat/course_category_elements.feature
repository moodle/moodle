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
# Tests for toggle course section visibility in non edit mode in snap.
#
# @package    theme_snap
# @author     Dayana Pardo <dayana.pardo@openlms.net>
# @copyright  Copyright (c) 2025 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course_categories

Feature: Check visual elements in the category and course view

  Background:
    Given the following config values are set as admin:
      | enableoutcomes | 1 |
      | theme          | snap |
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And I create the following course categories:
      | id | name        | category | idnumber |
      |  2 | Category 2  |     0    | CAT2     |
      |  3 | Category 3  |     0    | CAT3     |
    And the following "courses" exist:
      | fullname   | shortname | category | format | summary                | summaryformat |
      | Test Course| TC1       |  CAT2    | topics | This is a trial course | 1             |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | TC1    | editingteacher |


  @javascript
  Scenario: Check that the course image is not distorted
    Given I log in as "teacher1"

    And I am on "Test Course" course homepage
    And I navigate to "Settings" in current page administration
    And I should see "Course image"
    When I upload "/theme/snap/tests/fixtures/testpng_small.png" file to "Course image" filemanager
    And I press "Save and display"

    And I go to link "/course/"
    And I click on "Expand all" "link"
    And I wait "3" seconds
    And I should see "Test Course"
    And I click on ".moreinfo a" "css_element"
    Then I should see "This is a trial course"
    And ".courseimage img" "css_element" should exist
    And the image ".courseimage img" should maintain its aspect ratio