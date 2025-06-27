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
# Tests for Ally filter.
#
# @package    filter_ally
# @author     Guy Thomas
# @copyright  Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@filter @filter_ally @suite_ally
Feature: When the ally filter is enabled ally place holders and annotations are inserted when appropriate into user generated content.

  Background:
    Given the ally filter is enabled

  @javascript
  Scenario: Img tags for local files are processed.
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
      | teacher1 | C1     | teacher        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I create a label with fixture images "bpd_bikes_640px.jpg, testgif_small.gif, testpng_small.png"
    When I reload the page
    Then I should see the feedback place holder for the "1st" image
    And I should see the feedback place holder for the "2nd" image
    And I should see the feedback place holder for the "3rd" image
    And the ally image cover area should exist for the "1st" image
    And the ally image cover area should exist for the "2nd" image
    And the ally image cover area should exist for the "3rd" image
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should not see the feedback place holder for the "1st" image
    And I should not see the feedback place holder for the "2nd" image
    And I should not see the feedback place holder for the "3rd" image
    And the ally image cover area should exist for the "1st" image
    And the ally image cover area should exist for the "2nd" image
    And the ally image cover area should exist for the "3rd" image

  @javascript
  Scenario Outline: Anchors linking to local files are processed where filter enabled in course.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher1  | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | teacher        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And the ally filter is <enabledornot> for course
    And I allow guest access for current course
    And I create a label with random text files "test1.txt, test2.txt, test3.txt"
    When I reload the page
    Then I <shouldornot> see the feedback place holder for the "1st" anchor
    And I <shouldornot> see the feedback place holder for the "2nd" anchor
    And I <shouldornot> see the feedback place holder for the "3rd" anchor
    And I <shouldornot> see the download place holder for the "1st" anchor
    And I <shouldornot> see the download place holder for the "2nd" anchor
    And I <shouldornot> see the download place holder for the "3rd" anchor
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should not see the feedback place holder for the "1st" anchor
    And I should not see the feedback place holder for the "2nd" anchor
    And I should not see the feedback place holder for the "3rd" anchor
    And I <shouldornot> see the download place holder for the "1st" anchor
    And I <shouldornot> see the download place holder for the "2nd" anchor
    And I <shouldornot> see the download place holder for the "3rd" anchor
    And I log out
    And I log in as "guest"
    And I am on "Course 1" course homepage
    Then I should not see the feedback place holder for the "1st" anchor
    And I should not see the feedback place holder for the "2nd" anchor
    And I should not see the feedback place holder for the "3rd" anchor
    And I should not see the download place holder for the "1st" anchor
    And I should not see the download place holder for the "2nd" anchor
    And I should not see the download place holder for the "3rd" anchor
  Examples:
    | shouldornot | enabledornot |
    | should      | enabled      |
    | should not  | not enabled  |


  @javascript
  Scenario: Course section html is annotated.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher1  | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | teacher        |
    And course "C1" section 1 has html summary of "<p>My section summary</p>"
    And course "C1" section 2 has text summary of "Plain text summary - should have no annotation"
    When I log in as "teacher1"
    Then I am on "Course 1" course homepage
    And section 1 html is annotated
    And section 2 html is not annotated
    And I log out
    # Annotations for HTML summaries should be available for all roles.
    When I log in as "student1"
    Then I am on "Course 1" course homepage
    And section 1 html is annotated
    And section 2 html is not annotated
