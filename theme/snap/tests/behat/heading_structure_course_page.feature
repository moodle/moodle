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
# Tests for course resource and activity editing features.
#
# @package    theme_snap
# @author     Rafael Becerra rafael.becerrarodriguez@openlms.net
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@mod @mod_hsuforum @theme @theme_snap
Feature: With a discussion created, the heading levels structure should be correct on course page.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name            | intro                  | course | idnumber | Display recent posts on course page |
      | hsuforum | Test forum name | Test forum description | C1     | forum    | 1                                   |

  @javascript
  Scenario: Heading levels should be correct.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "li.modtype_hsuforum a.mod-link" "css_element"
    And I add a new discussion to "Test forum name" Open Forum with:
      | Subject | Forum post subject |
      | Message | This is the body   |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And ".section li.snap-activity.modtype_hsuforum .snap-asset-content .snap-asset-meta .hsuforum-recent .snap-media-object .snap-media-body h5" "css_element" should exist
    And "#snap-course-footer-recent-activity .hsuforum-recent h4" "css_element" should exist
