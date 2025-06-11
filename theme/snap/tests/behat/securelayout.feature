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

@theme @theme_snap
Feature: Using the snap theme page displaying with secure layout
  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | seb_requiresafeexambrowser |
      | quiz       | Quiz | Quiz 1 description | C1     | quiz1    | 4                            |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Quiz" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Sam1      | Student1 | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |

  Scenario: Confirm that the user name is displayed in the navbar without a link
    Given I log in as "admin"
    And the following config values are set as admin:
      | logininfoinsecurelayout | 1 |
    And I am on "Course 1" course homepage
    And I click on ".snap-asset-link a" "css_element"
    And I press "Preview quiz"
    Then I should see "You are logged in as Admin User" in the "nav" "css_element"
    But "Logout" "link" should not exist

  Scenario: Confirm that the custom menu items do not appear when language selection is enabled
    Given I log in as "admin"
    And the following config values are set as admin:
      | langmenuinsecurelayout | 1 |
      | custommenuitems | -This is a custom item\|/customurl/ |
    And I am on "Course 1" course homepage
    And I click on ".snap-asset-link a" "css_element"
    And I press "Preview quiz"
    Then I should not see "This is a custom item" in the "nav" "css_element"

  Scenario: Quiz description is displayed when Safe Exam Browser is required
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | seb_requiresafeexambrowser |
      | quiz       | Quiz2  | Quiz 2 description | C1     | quiz2    | 1                          |
    And quiz "Quiz2" contains the following questions:
      | question | page |
      | TF1      | 1    |
    When I log in as "student1"
    And I am on the "Quiz2" "quiz activity" page
    Then "body#page-mod-quiz-view" "css_element" should exist
    And I should see "Launch Safe Exam Browser"
    And I should see "Quiz 2 description"
