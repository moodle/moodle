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
# Tests for availability of course tools section.
#
# @package   theme_snap
# @author    Diego Monroy.
# @copyright Copyright (c) 2022 Open LMS
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course @format_tiles
Feature: When the moodle theme is set to Snap with course format tiles, a course tools section is available.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname     | shortname | format | coursedisplay | numsections | enablecompletion |
      | Course Test  | C1        | tiles  | 0             | 5           | 1                |
    And the following "activities" exist:
      | activity | name         | intro                  | course | idnumber | section | visible |
      | quiz     | Test quiz V  | Test quiz description  | C1     | quiz1    | 1       | 1       |
      | page     | Test page V  | Test page description  | C1     | page1    | 1       | 1       |
      | forum    | Test forum V | Test forum description | C1     | forum1   | 1       | 1       |
      | url      | Test URL V   | Test url description   | C1     | url1     | 1       | 1       |
      | label    | Test label V | Test label description | C1     | label1   | 1       | 1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |

  @javascript
  Scenario: Edit mode is not displayed for students.
    Given I log in as "student1"
    And I am on the course main page for "C1"
    Then "Course Dashboard" "link" should be visible
    And ".editmode-switch-form" "css_element" should not exist
    When I click on "Course Dashboard" "link"
    And I wait until the page is ready
    And ".editmode-switch-form" "css_element" should not exist

  @javascript
  Scenario: Edit mode is displayed for teachers.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    Then "Course Dashboard" "link" should be visible
    And ".editmode-switch-form" "css_element" should exist
    When I click on "Course Dashboard" "link"
    And I wait until the page is ready
    And ".editmode-switch-form" "css_element" should exist

  @javascript
  Scenario: As teacher you can switch between edit mode on and edit mode off.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And ".editmode-switch-form" "css_element" should exist
    Then "Course Dashboard" "link" should be visible
    And I should not see "Add an activity or resource"
    When I switch edit mode in Snap
    And I wait until the page is ready
    And I should see "Add an activity or resource"
    And I switch edit mode in Snap
    And I wait until the page is ready
    And I should not see "Add an activity or resource"
    When I click on "Course Dashboard" "link"
    And I wait until the page is ready
    And I should not see "Add a block"
    And I switch edit mode in Snap
    And I wait until the page is ready
    And I click on "button[data-original-title='Toggle block drawer']" "css_element"
    And I should see "Add a block"

  @javascript
  Scenario: Users can use Tiles filters in Snap.
    Given the following config values are set as admin:
      | enableoutcomes | 1 |
      | theme | snap |
    Then I log in as "admin"
    And I change window size to "large"
    And I am on "Course Test" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Legacy outcomes" in current page administration
    And I click on "//*[contains(text(),'Manage outcomes')]" "xpath_element"
    And I press "Add a new outcome"
    And I set the following fields to these values:
      | Full name | Outcometest |
      | Short name | Outcometest |
    And I set the field with xpath "//select[@name='scaleid']" to "Separate and Connected ways of knowing"
    And I press "Save changes"
    And I am on "Course Test" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field with xpath "//select[@name='displayfilterbar']" to "Show buttons based on course outcomes"
    And I press "Save and display"

  @javascript
  Scenario: Users can change activity visibility and group settings using Tiles in Snap.
    Given I log in as "admin"
    And I am on "Course Test" course homepage
    And I switch edit mode in Snap
    And I wait until the page is ready
    And I click on ".modtype_quiz .moodle-actionmenu" "css_element"
    And I should see "Group mode"
    And I click on ".modtype_quiz .moodle-actionmenu [data-action='cmHide']" "css_element"
    Then I should see "Hidden from students"
    And I click on ".modtype_quiz .moodle-actionmenu" "css_element"
    And I click on ".modtype_quiz .moodle-actionmenu [aria-label='Group mode']" "css_element"
    And I click on ".modtype_quiz .moodle-actionmenu  [data-action='cmVisibleGroups']" "css_element"
    Then ".modtype_quiz .icon[alt='Visible groups']" "css_element" should be visible