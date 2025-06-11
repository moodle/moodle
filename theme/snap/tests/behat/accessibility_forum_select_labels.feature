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
# Tests for toggle course section visibility in non edit mode in snap.
#
# @package    theme_snap
# @autor      Oscar Nadjar
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_ax
Feature: Aria label validation for core forum options.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
      | student1 | Student   | 1        | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  |
      | Course 1 | C1         | 0         |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: A teacher can see the attributes of the selectors.
    Given the following "activities" exist:
      | activity   | name                   | intro             | course | idnumber     | groupmode |
      | forum      | Test forum 1           | Test forum 1      | C1     | forum        | 0         |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I click on "//h3/div/a/p[contains(text(),'Test forum 1')]" "xpath_element"
    And I add a new discussion to "Test forum 1" forum with:
      | Subject | Discussion 1 |
      | Message | Test post message |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I click on "//h3/div/a/p[contains(text(),'Test forum 1')]" "xpath_element"
    And I click on "//th//a[contains(text(),'Discussion 1')]" "xpath_element"
    And "div[data-content='forum-discussion'] div.singleselect" "css_element" should exist
    And "div[data-content='forum-discussion'] div.movediscussionoption" "css_element" should exist
    And the "aria-label" attribute of "div[data-content='forum-discussion'] select.custom-select.singleselect" "css_element" should contain "Display options"
    And the "aria-label" attribute of "div[data-content='forum-discussion'] div.movediscussionoption select.custom-select.urlselect" "css_element" should contain "Move options"
