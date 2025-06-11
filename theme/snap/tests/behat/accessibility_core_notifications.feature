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
# Tests for core notifications messages in Snap.
#
# @package    theme_snap
# @author     Rafael Becerra rafael.becerrarodriguez@openlms.net
# @copyright  Copyright (c) 2019 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_ax
Feature: When the Moodle theme is set to Snap, core notifications messages should have a specific aria attribute to
  screen readers functionality.

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format | initsections |
      | Course 1 | C1        | topics |      1       |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | student1  | C1      | student         |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I add a forum activity to course "C1" section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
    And I log out

  @javascript
  Scenario: Success notification should have close dialog as aria-label attribute to be accessible
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "li.modtype_forum a.mod-link" "css_element"
    And I wait until the page is ready
    When I add a new discussion to "Test forum name" forum with:
      | Subject | Test discussion 1 |
      | Message | Test discussion 1 description |
    And I should see "Your post was successfully added."
    And the "aria-label" attribute of "div.alert-success button.close" "css_element" should contain "Close"
