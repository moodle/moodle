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
# Tests for mod folder files processed by Ally filter.
#
# @package    filter_ally
# @copyright  Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@filter @filter_ally @_file_upload
Feature: When the ally filter is enabled ally place holders are inserted when appropriate for files in mod folder.

  Background:
    Given the ally filter is enabled
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |

  @javascript
  Scenario Outline: Folder files are processed appropriately when viewed on own page
    Given the following "course enrolments" exist:
      | user     | course   | role           |
      | student1 | <course> | student        |
      | teacher1 | <course> | editingteacher |
    # in the following setting, display 0 = "On a separate page", 1 = "Inline on a course page".
    And the following "activities" exist:
      | activity | name             | intro                   | course               | idnumber | display | showexpanded | section |
      | folder   | Test folder name | Test folder description | Acceptance test site | folder1  | 0       | 1            | 1       |
      | folder   | Test folder name | Test folder description | C1                   | folder1  | 0       | 1            | 0       |
    And the following config values are set as admin:
      | config              | value            |
      | slasharguments      | <slasharguments> |
    When I log in as "teacher1"
    And <coursestep>
    And I allow guest access for current course
    And I should see "Test folder name"
    And I follow "Test folder name"
    And I press "Edit"
    And I upload "filter/ally/tests/fixtures/test_text_file.txt" file to "Files" filemanager
    And I upload "filter/ally/tests/fixtures/testgif.gif" file to "Files" filemanager
    And I create "Folder" folder in "Files" filemanager
    And I open "Folder" folder from "Files" filemanager
    And I upload "filter/ally/tests/fixtures/testpng.png" file to "Files" filemanager
    And I press "Save changes"
    Then I should see the feedback place holder for the "1st" file in subfolder
    And I should see the feedback place holder for the "2nd" file in folder
    And I should see the feedback place holder for the "3rd" file in folder
    And I should see the download place holder for the "1st" file in subfolder
    And I should see the download place holder for the "2nd" file in folder
    And I should see the download place holder for the "3rd" file in folder
    And I log out
    And I log in as "student1"
    And <coursestep>
    And I should see "Test folder name"
    And I follow "Test folder name"
    And I should not see the feedback place holder for the "1st" file in subfolder
    And I should not see the feedback place holder for the "2nd" file in folder
    And I should not see the feedback place holder for the "3rd" file in folder
    And I should see the download place holder for the "1st" file in subfolder
    And I should see the download place holder for the "2nd" file in folder
    And I should see the download place holder for the "3rd" file in folder
    And I log out
    And I log in as "guest"
    And <coursestep>
    And I should see "Test folder name"
    And I follow "Test folder name"
    And I should not see the feedback place holder for the "1st" file in subfolder
    And I should not see the feedback place holder for the "2nd" file in folder
    And I should not see the feedback place holder for the "3rd" file in folder
    And I should not see the download place holder for the "1st" file in subfolder
    And I should not see the download place holder for the "2nd" file in folder
    And I should not see the download place holder for the "3rd" file in folder
  Examples:
  | course               | coursestep                         | slasharguments |
  | C1                   | I am on "Course 1" course homepage | 1              |
  | C1                   | I am on "Course 1" course homepage | 1              |
  | C1                   | I am on "Course 1" course homepage | 0              |
  | C1                   | I am on "Course 1" course homepage | 0              |

  @javascript
  Scenario Outline: Folder files are processed appropriately when viewed inline on course page
    Given the following "course enrolments" exist:
      | user     | course   | role           |
      | student1 | <course> | student        |
      | teacher1 | <course> | editingteacher |
    # in the following setting, display 0 = "On a separate page", 1 = "Inline on a course page".
    And the following "activities" exist:
      | activity | name               | intro                     | course               | idnumber | display | showexpanded | section |
      | folder   | Inline folder name | Inline folder description | C1                   | folder1  | 1       | 1            | 0       |
      | folder   | Inline folder name | Inline folder description | Acceptance test site | folder1  | 1       | 1            | 1       |
    And the following config values are set as admin:
      | config              | value            |
      | slasharguments      | <slasharguments> |
    When I log in as "teacher1"
    And <coursestep>
    And I allow guest access for current course
    And I turn editing mode on
    And I open "Inline folder name" actions menu
    And I click on "Edit settings" "link" in the "Inline folder name" activity
    And I upload "filter/ally/tests/fixtures/test_text_file.txt" file to "Files" filemanager
    And I upload "filter/ally/tests/fixtures/testgif.gif" file to "Files" filemanager
    And I create "Folder" folder in "Files" filemanager
    And I open "Folder" folder from "Files" filemanager
    And I upload "filter/ally/tests/fixtures/testpng.png" file to "Files" filemanager
    And I press "Save and return to course"
    Then I should see the feedback place holder for the "1st" file in subfolder
    And I should see the feedback place holder for the "2nd" file in folder
    And I should see the feedback place holder for the "3rd" file in folder
    And I should see the download place holder for the "1st" file in subfolder
    And I should see the download place holder for the "2nd" file in folder
    And I should see the download place holder for the "3rd" file in folder
    And I log out
    And I log in as "student1"
    And <coursestep>
    Then I should not see the feedback place holder for the "1st" file in subfolder
    And I should not see the feedback place holder for the "2nd" file in folder
    And I should not see the feedback place holder for the "3rd" file in folder
    And I should see the download place holder for the "1st" file in subfolder
    And I should see the download place holder for the "2nd" file in folder
    And I should see the download place holder for the "3rd" file in folder
    And I log out
    And I log in as "guest"
    And <coursestep>
    And I should not see the feedback place holder for the "1st" file in subfolder
    And I should not see the feedback place holder for the "2nd" file in folder
    And I should not see the feedback place holder for the "3rd" file in folder
    And I should not see the download place holder for the "1st" file in subfolder
    And I should not see the download place holder for the "2nd" file in folder
    And I should not see the download place holder for the "3rd" file in folder
  Examples:
  | course               | coursestep                         | slasharguments |
  | C1                   | I am on "Course 1" course homepage | 1              |
  | Acceptance test site | I am on site homepage              | 1              |
  | C1                   | I am on "Course 1" course homepage | 0              |
  | Acceptance test site | I am on site homepage              | 0              |


