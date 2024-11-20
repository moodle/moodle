@mod @mod_book
Feature: In a book, change the navigation options
  In order to change the way a book's chapters can be traversed
  As a teacher
  I need to change navigation options on a book

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"

  # The option to "Style of navigation" is removed from the settings.
  Scenario: Change navigation options
    Given the following "activities" exist:
      | activity | name      | course | idnumber | navstyle |
      | book     | Test book | C1     | book1    | 0        |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test book"
    And I should see "Add new chapter"
    And I set the following fields to these values:
      | Chapter title | Test chapter 1 |
      | Content | Lorem ipsum dolor sit amet |
    And I press "Save changes"
    And I should see "Test book"
    And I should see "1. Test chapter 1"
    And I click on "Add new chapter" "link" in the "Table of contents" "block"
    And I set the following fields to these values:
      | Chapter title | Test chapter 2 |
      | Content | consectetur adipiscing elit |
    And I press "Save changes"
    And I should see "Test book"
    And I should see "2. Test chapter 2"
    And I click on "1. Test chapter 1" "link" in the "Table of contents" "block"
    And "Next" "link" should exist
    And I click on "2. Test chapter 2" "link" in the "Table of contents" "block"
    And "Previous" "link" should exist
