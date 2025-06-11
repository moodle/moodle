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
# Tests for Snap on a page resource.
#
# @package    theme_snap
# @copyright  Copyright (c) 2018 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When the moodle theme is set to Snap and there is a page resource, appearance options should not appear.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode | theme |
      | Course 1 | C1 | 0 | 1 | |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name       | intro        | content       |
      | page     | C1     | page1    | Test page1 | Test description  | test content |

  @javascript
  Scenario: Page description checkbox is not shown when using Snap.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I click on ".snap-edit-asset-more" "css_element"
    And I click on ".snap-edit-asset" "css_element"
    And I wait until the page is ready
    And I should not see "Display description on course page"

  @javascript
  Scenario: Teacher sees a warning message and is unable to choose any appearance options on page resource.
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I click on ".snap-edit-asset-more" "css_element"
    And I click on ".snap-edit-asset" "css_element"
    And I wait until the page is ready
    And I expand all fieldsets
    And I should see "Snap's design language prevents changes to \"Appearance\" settings."
    And the "//select[@id='id_display']" "xpath_element" should be readonly
    And the "//input[@id='id_printintro']" "xpath_element" should be readonly
