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
# Tests for setting colors per category.
#
# @package    theme_snap
# @copyright  Copyright (c) 2018 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_color_check @theme_snap_course
# Some scenarios will be testing AX through special steps depending on the needed rules.
# https://github.com/dequelabs/axe-core/blob/v3.5.5/doc/rule-descriptions.md#best-practices-rules.
# Color contrast: cat.color.
Feature: When the moodle theme is set to Snap, sets a color per category.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And I create the following course categories:
      | id | name   | category | idnumber | description |
      |  5 | Cat 5  |     0    |   CAT5   |   Test      |
      | 10 | Cat 10 |   CAT5   |   CAT10  |   Test      |
      | 20 | Cat 20 |   CAT10  |   CAT20  |   Test      |
      | 30 | Cat 30 |   CAT30  |   CAT30  |   Test      |
      | 40 | Miscellaneous |   misc  |   misc  |   Test      |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        |     0    | topics |
      | Course 2 | C2        |   CAT20  | topics |
      | Course 3 | C3        |   misc   | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C2     | editingteacher |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |
    And the following config values are set as admin:
      | category_color | {"5":"#00FF00","30":"#FF0000"} | theme_snap |
    And the following config values are set as admin:
      | allowcategorythemes     | true | theme_snap |

  @javascript
  Scenario: Load all classes in each category hierarchy.
    Given I log in as "admin"
    And I follow "My Courses"
    And I follow "Browse all courses"
    And I wait until the page is ready
    And I check body for classes "theme-snap"
    And I follow "Miscellaneous"
    And I check body for classes "theme-snap,category-40"
    And I follow "Courses"
    And I follow "Browse all courses"
    And I follow "Cat 5"
    And I check body for classes "theme-snap,category-5"
    And I follow "Cat 10"
    And I check body for classes "theme-snap,category-10"
    And I follow "Cat 20"
    And I check body for classes "theme-snap,category-20"

  @javascript
  Scenario: Check category colors in hierarchy.
    Given I log in as "admin"
    And I purge snap caches
    And I wait until the page is ready
    And I log out
    And I wait until the page is ready
    And I log in as "admin"
    And I wait until the page is ready
    And I follow "My Courses"
    And I follow "Browse all courses"
    And I follow "Cat 5"
    And I check element "a.btn.btn-secondary" with color "#00FF00"
    And I follow "Cat 10"
    And I check element "a.btn.btn-secondary" with color "#82009E"
    And I follow "Cat 20"
    And I check element "a.btn.btn-secondary" with color "#82009E"
    And I follow "Courses"
    And I follow "Browse all courses"
    And I follow "Cat 30"
    And I check element "a.btn.btn-secondary" with color "#FF0000"

  @javascript @accessibility
  Scenario: Check category colors from nearest parent in hierarchy.
    Given the following config values are set as admin:
      | category_color | {"5":"#006100","10":"#800000"} | theme_snap |
    Given I log in as "admin"
    And I purge snap caches
    And I wait until the page is ready
    And I log out
    And I wait until the page is ready
    And I log in as "admin"
    And I wait until the page is ready
    And I follow "My Courses"
    And I follow "Browse all courses"
    And I wait until the page is ready
    And I follow "Cat 5"
    And I check element "a.btn.btn-secondary" with color "#006100"
    And I follow "Cat 10"
    And I check element "a.btn.btn-secondary" with color "#800000"
    And I follow "Cat 20"
    And I check element "a.btn.btn-secondary" with color "#82009E"
    And I follow "Courses"
    And I follow "Browse all courses"
    And I follow "Cat 30"
    And I check element "a.btn.btn-secondary" with color "#82009E"
    And the page should meet "cat.color" accessibility standards
