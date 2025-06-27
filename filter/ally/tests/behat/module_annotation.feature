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
# Tests for Ally filter label annotations.
#
# @package    filter_ally
# @author     Guy Thomas
# @copyright  Copyright (c) 2018 Open LMS / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@filter @filter_ally @suite_ally
Feature: When the ally filter is enabled ally annotations are inserted when appropriate into user generated label content.

  Background:
    Given the ally filter is enabled
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario Outline: Module content is annotated and following HTML content URL for annotation works.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    # Add padding to test viewport.
    And I create a <module> with html content "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br>" in section 1
    And I create a <module> with html content "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br>" in section 1
    And I create a <module> with html content "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br>" in section 1
    And I create a <module> with html content "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br>" in section 1
    And I create a <module> with html content "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br>" in section 1
    And I create a <module> with html content "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br>" in section 1
    # Add <module> to check.
    And I create a <module> with html content "<p>Some content</p>" in section 5
    When I reload the page
    And I should see "Some content"
    And the <module> with html content "Some content" is not visible or not in viewport
    Then <module> with html "Some content" is annotated
    And I follow the webservice content url for <module> "Some content"
    And the <module> with html content "Some content" is visible and in viewport
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And <module> with html "Some content" is annotated

  Examples:
  |module|
  |label |
  |page  |
  |book  |
  |lesson|

  @javascript
  Scenario: Book chapters are annotated.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I create a book with html content "<p>Some content</p>" in section 1
    And I add 2 chapters to "test book"
    And I reload the page
    And I open the book module
    # Refresh cache.
    And I navigate to "Settings" in current page administration
    And I press "Save and display"
    Then the current book chapter is annotated
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I open the book module
    Then the current book chapter is annotated
