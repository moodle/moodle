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
# @author     Rafael Becerra <rafael.becerrarodriguez@openlms.net>
# @copyright  Copyright (c) 2018 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When theme is set to Snap, and course:activityvisibility is set for students, a student should be able to hide an activity.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | manager        |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name             | intro             |
      | assign   | C1     | assign1  | Test assignment1 | Test assignment 1 |

  @javascript
  Scenario: Student can hide an activity if it has course:activityvisibility capability
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I set capability "moodle/course:activityvisibility" for students in the course
    And I log out

    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    Then I should see "Hide"

  @javascript
  Scenario: Student should not see the activity after hiding it
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I set capability "moodle/course:activityvisibility" for students in the course
    And I log out

    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    And I click on ".snap-activity[data-type='Assignment'] a.js_snap_hide" "css_element"

  @javascript
  Scenario: Student should not be able to hide an activity if the course doesn't have course:activityvisibility for students
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element" should not exist
