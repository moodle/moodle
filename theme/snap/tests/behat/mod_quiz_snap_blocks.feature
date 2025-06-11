# This file is part of Moodle - https://moodle.org/
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
# along with Moodle. If not, see <https://www.gnu.org/licenses/>.
#
# Test to verify the rendering of the Page Activity expand
# icon on the Course page based on its configured settings.
#
# @package    theme_snap
# @copyright  Copyright (c) 2025 Open LMS (https://www.openlms.net)
# @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_quiz
Feature: Display of blocks in quiz activities with snap theme

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | student  | Student   | One      | student@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity | name   | intro              | course | idnumber |
      | quiz     | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext    |
      | Test questions   | truefalse | TF1  | First question  |
      | Test questions   | truefalse | TF2  | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |

  @javascript
  Scenario: The navigation block is displayed when reviewing a quiz attempt
    Given user "student" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | True     |
      | 2    | False    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I follow "Review"
    Then I should see "25.00 out of 100.00"
    And I should see "Quiz navigation" in the "#mod_quiz_navblock" "css_element"

  @javascript
  Scenario: The navigation block is displayed when attempting a quiz
    Given I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    When I press "Attempt quiz"
    Then I should see "Quiz navigation" in the "#mod_quiz_navblock" "css_element"