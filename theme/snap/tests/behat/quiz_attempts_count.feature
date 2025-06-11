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
# Quiz attempts count validation
#
# @package    theme_snap
# @autor      Oscar Nadjar
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_quiz
Feature: Quiz attempts from suspended users and previews from admin or teachers
  should not be count.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | teacher1    | Teacher1 | teacher1@example.com |
      | student1 | student1      | Student1 | student1@example.com |
      | student2 | student2      | Student2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Quiz" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And I log out

  @javascript
  Scenario: Student sees correct meta data against quiz activities
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And ".snap-completion-meta a" "css_element" should exist
    And I should see "0 of 2 Attempted"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I click on ".snap-asset-link a" "css_element"
    And I press "Attempt quiz"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "False" "radio" in the "Second question" "question"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And ".snap-completion-meta a" "css_element" should exist
    And I should see "1 of 2 Attempted"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I click on ".snap-asset-link a" "css_element"
    And I press "Attempt quiz"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "False" "radio" in the "Second question" "question"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And ".snap-completion-meta a" "css_element" should exist
    And I should see "2 of 2 Attempted"
    And the following "course enrolments" exist:
      | user | course | role | status |
      | student1 | C1 | student | 1 |
    And I am on "Course 1" course homepage
    And I should see "1 of 1 Attempted"
    And I should see "Excludes attempts from suspended users"
