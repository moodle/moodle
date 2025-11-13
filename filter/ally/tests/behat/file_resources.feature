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
# Tests for Ally filter file resources javascript.
#
# @package    filter_ally
# @author     Guy Thomas
# @copyright  Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@filter @filter_ally @suite_ally
Feature: When the ally filter is enabled ally place holders are inserted when appropriate into file resources.

  Background:
    Given the ally filter is enabled

  @javascript
  Scenario Outline: File resources are processed when viewed on a course page or a course section page.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course   | role    |
      | student1 | <course> | student |
      | teacher1 | <course> | teacher |
    And I log in as "teacher1"
    And <coursestep>
    And I allow guest access for current course
    And I create file resources using fixtures "test.doc, test.docx, test.odt, bpd_bikes_640px.jpg, testgif_small.gif, testpng_small.png" in section "<section>"
    When I reload the page
    Then I should see the feedback place holder for the "1st" file resource
    And I should see the feedback place holder for the "2nd" file resource
    And I should see the feedback place holder for the "3rd" file resource
    And I should see the feedback place holder for the "4th" file resource
    And I should see the feedback place holder for the "5th" file resource
    And I should see the feedback place holder for the "6th" file resource
    And I should see the download place holder for the "1st" file resource
    And I should see the download place holder for the "2nd" file resource
    And I should see the download place holder for the "3rd" file resource
    And I should see the download place holder for the "4th" file resource
    And I should see the download place holder for the "5th" file resource
    And I should see the download place holder for the "6th" file resource
    And I log out
    And I log in as "student1"
    When <coursestep>
    Then I should not see the feedback place holder for the "1st" file resource
    And I should not see the feedback place holder for the "2nd" file resource
    And I should not see the feedback place holder for the "3rd" file resource
    And I should not see the feedback place holder for the "4th" file resource
    And I should not see the feedback place holder for the "5th" file resource
    And I should not see the feedback place holder for the "6th" file resource
    And I should see the download place holder for the "1st" file resource
    And I should see the download place holder for the "2nd" file resource
    And I should see the download place holder for the "3rd" file resource
    And I should see the download place holder for the "4th" file resource
    And I should see the download place holder for the "5th" file resource
    And I should see the download place holder for the "6th" file resource
    And I log out
    And I log in as "guest"
    When <coursestep>
    Then I should not see the feedback place holder for the "1st" file resource
    And I should not see the feedback place holder for the "2nd" file resource
    And I should not see the feedback place holder for the "3rd" file resource
    And I should not see the feedback place holder for the "4th" file resource
    And I should not see the feedback place holder for the "5th" file resource
    And I should not see the feedback place holder for the "6th" file resource
    And I should not see the download place holder for the "1st" file resource
    And I should not see the download place holder for the "2nd" file resource
    And I should not see the download place holder for the "3rd" file resource
    And I should not see the download place holder for the "4th" file resource
    And I should not see the download place holder for the "5th" file resource
    And I should not see the download place holder for the "6th" file resource
    Examples:
      | course               | coursestep                          | section |
      | C1                   | I am on "Course 1" course homepage  | 1       |
      | C1                   | I am on course "C1" section 2       | 2       |
      | Acceptance test site | I am on site homepage               | 1       |
