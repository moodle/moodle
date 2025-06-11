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
# Tests for visibility of grading activities only if user have grading capabilities.
#
# @package    theme_snap
# @author     Juan Ibarra <juan.ibarra@openlms.net>
# @copyright  Copyright (c) 2020 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @_file_upload
Feature: When the moodle theme is set to Snap, a student can remove a submitted file assignment.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode | initsections |
      | Course 1 | C1        | 0        | 1         |      1       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher@example.com  |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I add a assign activity to course "C1" section "1" and I fill the form with:
      | Assignment name | Assignment One |
      | Description | Submit your file |
      | assignsubmission_file_enabled | 1 |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_onlinetext_wordlimit_enabled | 0 |
    And I log out

  @javascript
  Scenario: User sees remove submission button and can remove submission with only files
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Assignment One']" "xpath_element"
    And I reload the page
    And I press "Add submission"
    And I wait until the page is ready
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    Then I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Assignment One']" "xpath_element"
    Then I should see "Remove submission"
    # Check if submission has file.
    And I press "Edit submission"
    And "//img[contains(@title, \"empty.txt\")]" "xpath_element" should exist
    And I press "Cancel"
    Then I press "Remove submission"
    And I should see "Are you sure you want to remove your submission?"
    And I press "Continue"
    Then I wait until the page is ready
    And I press "Add submission"
    And "//img[contains(@title, \"empty.txt\")]" "xpath_element" should not exist

  @javascript
  Scenario: User sees remove submission button and can remove submission with only text
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Assignment One']" "xpath_element"
    And I reload the page
    And I press "Add submission"
    And I wait until the page is ready
    And I set the following fields to these values:
      | Online text | I'm the student online text submission |
    And I press "Save changes"
    Then I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Assignment One']" "xpath_element"
    Then I should see "Remove submission"
    # Chcek if submission has online text.
    And I press "Edit submission"
    And "I'm the student online text submission" "text" should exist
    And I press "Cancel"
    Then I press "Remove submission"
    And I should see "Are you sure you want to remove your submission?"
    And I press "Continue"
    Then I wait until the page is ready
    And I press "Add submission"
    And "I'm the student online text submission" "text" should not exist

  @javascript
  Scenario: User sees remove submission button and can remove submission with mixed content
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Assignment One']" "xpath_element"
    And I reload the page
    And I press "Add submission"
    And I wait until the page is ready
    And I set the following fields to these values:
      | Online text | I'm the student online text submission |
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    Then I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on "//a[@class='mod-link']//p[text()='Assignment One']" "xpath_element"
    Then I should see "Remove submission"
    # Check if submission has file and online text.
    And I press "Edit submission"
    And "I'm the student online text submission" "text" should exist
    And "//img[contains(@title, \"empty.txt\")]" "xpath_element" should exist
    And I press "Cancel"
    Then I press "Remove submission"
    And I should see "Are you sure you want to remove your submission?"
    And I press "Continue"
    Then I wait until the page is ready
    And I press "Add submission"
    And "I'm the student online text submission" "text" should not exist
    And "//img[contains(@title, \"empty.txt\")]" "xpath_element" should not exist
