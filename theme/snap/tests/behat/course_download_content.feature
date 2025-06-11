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
# @author     Jonathan Garcia jonathan.garcia@openlms.net
# @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap_course_content_download
Feature: Users can see a button or link to download content when using snap.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
      | Course 2 | C2        | 0        |  weeks |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
      | student1 | C2     | student        |
      | teacher1 | C2     | editingteacher |

  @javascript
  Scenario: Buttons is visible when allowing course download for students.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should not see "Download course content"
    And I am on "Course 2" course homepage
    And I should not see "Download course content"
    And I log out
    # Teacher can't see the button yet.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "#page-mast div.singlebutton button[data-downloadcourse='1']" "css_element" should not exist
    And I click on "#admin-menu-trigger" "css_element"
    And I should not see "Download course content"
    And I am on "Course 2" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I should not see "Download course content"
    And "#page-mast div.singlebutton button[data-downloadcourse='1']" "css_element" should not exist
    And I log out
    # Change the site configuration as admin to allow course download content.
    And I log in as "admin"
    And the following config values are set as admin:
      | downloadcoursecontentallowed | 1 |
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      |  Enable download course content | 1 |
    And I click on "Save and display" "button"
    And I am on "Course 2" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      |  Enable download course content | 1 |
    And I click on "Save and display" "button"
    And I log out
    # Download content enabled.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "Download course content"
    # Only visible in admin menu.
    And I follow "Course Dashboard"
    And I should see "Download course content"
    And I am on "Course 2" course homepage
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Course Dashboard"
    And I should see "Download course content"
    # Only visible in admin menu.
    And I am on "Course 2" course homepage
    And I follow "Course Dashboard"
    And I should see "Download course content"
