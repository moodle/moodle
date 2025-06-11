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
# Test for Snap feeds side menu.
#
# @package    theme_snap
# @autor      Daniel Cifuentes
# @copyright  Copyright (c) 2024 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: Users can access the Snap feeds information using the nav button in Snap.

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1 | Teacher     | 1         | teacher1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1 | C1       | editingteacher  |
    Given the following "activities" exist:
      | activity | course | idnumber | name             | intro             | duedate   |
      | assign   | C1     | assign1  | Test assignment1 | Test assignment 1 | ##today## |

  @javascript
  Scenario: User can access to Snap feeds menu with advanced feeds enabled.
    Given I log in as "admin"
    And the following config values are set as admin:
      | advancedfeedsenable | true |
    And I log in as "teacher1"
    And I am on site homepage
    And "#snap_feeds_side_menu_trigger" "css_element" should exist
    Then I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I should see "Test assignment1 is due"
    And I should see "Today"
    And I am on "Course 1" course homepage
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    Then I follow "Edit settings"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Due date | ##tomorrow## |
    And I press "Save and return to course"
    And I log in as "teacher1"
    Then I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I should see "Tomorrow"

  @javascript
  Scenario: User can access to Snap feeds menu with advanced feeds disabled.
    Given I log in as "admin"
    And the following config values are set as admin:
      | advancedfeedsenable | false |
    And I log in as "teacher1"
    And I am on site homepage
    And "#snap_feeds_side_menu_trigger" "css_element" should exist
    Then I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I should see "Test assignment1 is due"
    And I should see "Today"
    And I am on "Course 1" course homepage
    And I click on ".snap-activity[data-type='Assignment'] button.snap-edit-asset-more" "css_element"
    Then I follow "Edit settings"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Due date | ##tomorrow## |
    And I press "Save and return to course"
    And I log in as "teacher1"
    Then I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I should see "Tomorrow"

  @javascript
  Scenario: User can see the Snap feeds menu items based on the Snap settings.
    And I log in as "teacher1"
    Then I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I should see "Deadlines"
    And I should see "Grading"
    And I should see "Messages"
    And I should see "Forum posts"
    Then the following config values are set as admin:
      | deadlinestoggle | 0 | theme_snap  |
      | feedbacktoggle  | 0  | theme_snap |
    And I log in as "teacher1"
    Then I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I should not see "Deadlines"
    And I should not see "Grading"
    And I should see "Messages"
    And I should see "Forum posts"
    Then the following config values are set as admin:
      | messagestoggle   | 0  | theme_snap  |
      | forumpoststoggle | 0  | theme_snap  |
    And I log in as "teacher1"
    And "#snap_feeds_side_menu_trigger" "css_element" should not exist