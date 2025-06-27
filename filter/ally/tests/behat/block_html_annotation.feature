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
# Tests for Ally filter block_hmtl annotations.
#
# @package    filter_ally
# @author     Guy Thomas
# @copyright  Copyright (c) 2018 Open LMS / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


@filter @filter_ally @suite_ally
Feature: When the ally filter is enabled ally annotations are inserted when appropriate into block_html content.

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
      | teacher1 | C1     | teacher        |

  @javascript
  Scenario Outline: Block html is annotated and following HTML content URL for annotation works.
    Given the following config values are set as admin:
      | theme | <theme> |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I add a html block with title "Some block" and content "<p>Some content</p>"
    When I reload the page
    And I should see "Some content"
    And I wait until the page is ready
    And html block with title "Some block" is annotated
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And html block with title "Some block" is annotated

  Examples:
  | theme   |
  | classic |
  | boost   |
  # Note - we could test for snap here too but if we did that it'd break the tests on non standard moodle installs.
