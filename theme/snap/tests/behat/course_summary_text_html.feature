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
# Test font and background colors when editing text on course introduction, labels,
# and pages.
#
# @package   theme_snap
# @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net)
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_color_check @theme_snap_course
Feature: When setting an html content on course sections (introduction, labels, pages),
  color and background color holds.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | category | groupmode | enablecompletion |
      | Course 1 | C1        | topics | 0        | 1         | 1                |
    And the following "activities" exist:
      | activity | name         | intro                         | course | idnumber    |
      | label    | Test label   | <div id="test_html_label"style="background: #f0f8ff;color: #1e90ff;">Test text <strong>New Changes</strong> | C1 | label1 |
      | page     | Test Page    | <div id="test_html_page"style="background: #f0f8ff;color: #1e90ff;">Test text <strong>New Changes</strong>  | C1 | page1  |

  @javascript
  Scenario: Set an html content to section description and check for color.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I click on ".edit-summary" "css_element"
    And I set the field "Section name" to "Test section"
    And I set the field "Description" to "<div id=\"test_html\"style=\"background: #f0f8ff;color: #1e90ff;\">Test text <strong>New Changes</strong>"
    And I press "Save changes"
    And I wait until "#test_html" "css_element" is visible
    And I check element "#test_html" with property "color" = "#1e90ff"
    And I check element "#test_html" with property "background" = "#f0f8ff"

  @javascript
  Scenario: Set simple content to section description and check for color.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I click on ".edit-summary" "css_element"
    And I set the field "Section name" to "Test section"
    And I set the field "Description" to "Test summary"
    And I press "Save changes"
    And I wait until ".summary .no-overflow" "css_element" is visible
    And I check element ".summary .no-overflow" with property "color" = "#565656"

  @javascript
  Scenario: Check html colors in label content.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I check element "#test_html_label" with property "color" = "#1e90ff"
    And I check element "#test_html_label" with property "background" = "#f0f8ff"

  @javascript
  Scenario: Check html colors in page content.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I check element "#test_html_page" with property "color" = "#1e90ff"
    And I check element "#test_html_page" with property "background" = "#f0f8ff"
