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
# @author     Rafael Becerra rafael.becerrarodriguez@openlms.net
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_ax
Feature: When adding a submission in an assignment, file picker options should exists as buttons.

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
    And the following "activities" exist:
      | activity | course | idnumber | name             | intro             | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes | submissiondrafts |
      | assign   | C1     | assign1  | Assignment One   | Test assignment 1 | 1                             | 1                              | 0                                  | 0                |

  @javascript
  Scenario: Filepicker options needs to exists as buttons
    Given I log in as "student1"
    And I am on the course main page for "C1"
    And I click on "li.modtype_assign a.mod-link" "css_element"
    And I reload the page
    And I press "Add submission"
    And "a#addbtn" "css_element" should exist
    And "button#createfolderbtn" "css_element" should exist
    And "button#displayiconsbtn" "css_element" should exist
    And "button#displaydetailsbtn" "css_element" should exist
    And "button#displaytreebtn" "css_element" should exist
