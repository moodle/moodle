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
# Tests for visibility of activity restriction tags.
#
# @package    theme_snap
# @copyright Copyright (c) 2020 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @_file_upload
Feature: When the moodle theme is set to Snap, the user can manipulate the files through the file manager.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity | name               | intro                   | course | idnumber | display | showexpanded |
      | folder   | Test folder name 1 | Test folder description | C1     | folder1  | 1       | 1            |
    Given I log in as "admin"
    And I am on the course main page for "C1"
    And I click on ".snap-edit-asset-more" "css_element"
    And I click on ".snap-edit-asset" "css_element"
    And I upload "lib/tests/fixtures/empty.txt" file to "Files" filemanager
    And I press "Save and return to course"

  @javascript
  Scenario: When a Filemanager is select Display folder with file details option, the files can be deleted
  with a button.
    Given I am on the course main page for "C1"
    And I click on ".snap-edit-asset-more" "css_element"
    And I click on ".snap-edit-asset" "css_element"
    Then ".filemanager .filemanager-toolbar" "css_element" should exist
    And I click on "button#displaydetailsbtn" "css_element"
    And I wait until "div.fp-tableview" "css_element" exists
    And I should see "empty.txt"
    And "button#deletebtn" "css_element" should exist
    And I click on "input[data-fullname=\"empty.txt\"]" "css_element"
    And I click on "button#deletebtn" "css_element"
    Then I should see "Are you sure you want to delete the selected"
    And I click on "Yes" "button"
    Then I should not see "empty.txt"
