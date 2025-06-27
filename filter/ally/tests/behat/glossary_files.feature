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
# Tests for glossary attachments processed by Ally filter.
#
# @package    filter_ally
# @author     Guy Thomas
# @copyright  Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@filter @filter_ally @_file_upload @suite_ally
Feature: When the ally filter is enabled ally place holders are inserted when appropriate into glossary attachments.

  Background:
    Given the ally filter is enabled

  @javascript
  Scenario Outline: Glossary attachments are processed appropriately.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher        |
    And the following config values are set as admin:
      | config              | value            |
      | slasharguments      | <slasharguments> |
    And the following "activities" exist:
      | activity | name       | intro                     | displayformat | course | idnumber  |
      | glossary | MyGlossary | Test glossary description | encyclopedia  | C1     | glossary1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "MyGlossary" "link" in the ".activityname" "css_element"
    When I add a glossary entry with the following data:
      | Concept    | Teacher Entry      |
      | Definition | Teacher Definition |
      | Attachment | lib/tests/fixtures/empty.txt |
    Then I should see the feedback place holder for the "1st" glossary attachment
    And I should see the download place holder for the "1st" glossary attachment
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on "MyGlossary" "link" in the ".activityname" "css_element"
    Then I should not see the feedback place holder for the "1st" glossary attachment
    And I should see the download place holder for the "1st" glossary attachment
    # Note, we have to give the Student entry a prefix of B because "S" for student clicks on 'special' instead of 'S'
    When I add a glossary entry with the following data:
      | Concept       | B Student Entry              |
      | Definition    | B Student Definition         |
      | Attachment    | lib/tests/fixtures/empty.txt |
    # Student will not see any place holders for the entry they added.
    Then I should not see the feedback place holder for the "1st" glossary attachment
    Then I should not see the download place holder for the "1st" glossary attachment
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "MyGlossary" "link" in the ".activityname" "css_element"
    When I click on "T" "link" in the ".entrybox" "css_element"
    Then I should see the feedback place holder for the "1st" glossary attachment
    And I should see the download place holder for the "1st" glossary attachment
    When I click on "B" "link" in the ".entrybox" "css_element"
    Then I should not see the feedback place holder for the "1st" glossary attachment
    And I should not see the download place holder for the "1st" glossary attachment

  Examples:
  | slasharguments |
  | 1              |
  | 0              |
