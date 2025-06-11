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
# Tests for role switching features on course home page.
#
# @package    theme_snap
# @copyright  Copyright (c) 2018 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the moodle theme is set to Snap, switching between roles should be possible.

  Background:
    Given the following config values are set as admin:
      | defaulthomepage | 0 |

  @javascript
  Scenario: Teacher can switch to guest role and back in user menu.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I open the user menu
    And I follow "Switch role to..."
    And I wait until the page is ready
    Then I should see "Switch role to..."
    Then I should see "Student"
    And I click on "Student" "button"
    And I wait until the page is ready
    And I open the user menu
    Then I should see "Return to my normal role"
    And I follow "Return to my normal role"
    And I wait until the page is ready
    Then "#admin-menu-trigger" "css_element" should be visible
    And I click on "#admin-menu-trigger" "css_element"
    Then I should see "Course administration"
    And I open the user menu
    Then I should see "Switch role to..."

  @javascript
  Scenario: Teacher can switch to guest role and back in course front page.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I open the user menu
    And I follow "Switch role to..."
    And I wait until the page is ready
    And I click on "Guest" "button"
    And I wait until the page is ready
     #The admin gear is visible when we login as guest using the "Switch role to" option.
    Then "#admin-menu-trigger" "css_element" should be visible
    And I open the user menu
    Then I should see "Return to my normal role"
    And I follow "Return to my normal role"
    And I wait until the page is ready
    And I should not see "Return to my normal role"
    Then "#admin-menu-trigger" "css_element" should be visible
    And I click on "#admin-menu-trigger" "css_element"
    Then I should see "Course administration"
    And I log in as "guest"
     # The admin gear should not be visible for the guest user when login normally from the login page.
    Then "#admin-menu-trigger" "css_element" should not be visible
