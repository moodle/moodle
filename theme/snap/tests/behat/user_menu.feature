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
# Tests for User menu display on initial login.
#
# @package    theme_snap
# @author     Daniel Cifuentes daniel.cifuentes@openlms.net
# @copyright  Copyright (c) 2023 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When the moodle theme is set to Snap and can open the user menu from the header

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And I am on site homepage

  @javascript
  Scenario: User logs in as guest, no my courses link or user menu
    Given I follow "Log in"
    And I set the field "username" to "guest"
    And I set the field "password" to "guest"
    And I press "Log in"
    And ".usermenu" "css_element" should not be visible
    And ".snap-my-courses-link" "css_element" should not be visible

  @javascript
  Scenario: User logs in and sees my courses link and user menu
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
    And I log in as "teacher1"
    Then ".usermenu" "css_element" should be visible
    And ".snap-my-courses-link" "css_element" should be visible
    And I am on "Course 1" course homepage
    Then ".usermenu" "css_element" should be visible
    And ".snap-my-courses-link" "css_element" should be visible

  @javascript
  Scenario: After login, admin user sees the expected links in the user menu.
    Given I log in as "admin"
    And I click on ".usermenu .dropdown-toggle" "css_element"
    Then I should see "Profile"
    Then I should see "Grades"
    Then I should see "Calendar"
    Then I should see "Private files"
    Then I should see "Reports"
    Then I should see "My Account"
    Then I should see "Dashboard"
    Then I should see "Course catalogue"
    Then I should see "Program catalogue"
    Then I should see "My programs"
    Then I should see "My Reports"
    Then I should see "Preferences"
    Then I should see "Switch role to..."
    Then I should see "Log out"

  @javascript
  Scenario: My Courses option works responsively
    Given I am logged in as "teacher1"
    And I change window size to "570x800"
    And I click on ".usermenu .dropdown-toggle" "css_element"
    And I should not see "My Courses" in the ".snap-my-courses-link" "css_element"
    And I should see "My Courses" in the "#user-action-menu" "css_element"
    Then I change window size to "800x600"
    And I click on ".usermenu .dropdown-toggle" "css_element"
    And I should see "My Courses" in the ".snap-my-courses-link" "css_element"
    And I should not see "My Courses" in the "#user-action-menu" "css_element"
