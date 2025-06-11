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
# along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

# Tests for moving and rearranging multiple activities within a course in the Snap theme.
#
# @package    theme_snap
# @copyright  Copyright (c) 2024 Open LMS (https://www.openlms.net)
# @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the theme is set to Snap, teachers can move and rearrange multiple activities at once in a course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | theme |
      | Course 1 | C1 | snap |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | teacherfn | teacherln | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  @javascript
  Scenario: Within a course in Snap, move and rearrange multiple activities at once
    Given the following "activities" exist:
      | activity   | name              | course    | idnumber     |
      | resource   | Test Resource 1   | C1        | resource1    |
      | forum      | Test Forum 1      | C1        | forum1       |
      | assign     | Test Assignment 1 | C1        | assign1      |

    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    # Initial order of the activities.
    And "li.activity:nth-of-type(1).resource" "css_element" should be visible
    And "li.activity:nth-of-type(2).forum" "css_element" should be visible
    And "li.activity:nth-of-type(3).assign" "css_element" should be visible
    And ".snap-activity.assign .snap-asset-actions" "css_element" should be visible
    And I click on ".snap-activity.assign .snap-asset-actions" "css_element"
    And ".snap-asset-move" "css_element" should exist in the ".snap-activity.assign #snap-asset-menu" "css_element"
    And ".snap-activity.assign .snap-asset-move-wrapper" "css_element" should not be visible
    And I click on ".snap-activity.assign .snap-asset-move" "css_element"
    And ".snap-activity.assign .snap-asset-move-wrapper" "css_element" should be visible
    And ".snap-move-note" "css_element" should be visible
    And I click on ".snap-activity.forum .snap-asset-move-input" "css_element"
    And I click on ".snap-move-note:nth-of-type(1)" "css_element"
    # Final order of the activities.
    And "li.activity:nth-of-type(1).assign" "css_element" should be visible
    And "li.activity:nth-of-type(2).forum" "css_element" should be visible
    And "li.activity:nth-of-type(3).resource" "css_element" should be visible
    And ".snap-activity.assign .snap-asset-move-wrapper" "css_element" should not be visible
    And ".snap-move-note" "css_element" should not be visible
