@mod @mod_book
Feature: Display the book description in the book and optionally in the course
  In order to display the the book description in the course
  As a teacher
  I need to enable the 'Display description on course page' setting.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course | name      | intro                |
      | book     | C1     | Test book | A book about dreams! |
    And the following "mod_book > chapter" exists:
      | book    | Test book                       |
      | title   | Dummy first chapter             |
      | content | Dream is the start of a journey |
    And I log in as "teacher1"

  Scenario: Description is displayed in the book
    When I am on the "Test book" "book activity" page
    Then I should see "A book about dreams!"

  Scenario: Show book description in the course homepage
    Given I am on the "Test book" "book activity editing" page
    And the following fields match these values:
      | Display description on course page | |
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should see "A book about dreams!"

  Scenario: Hide book description in the course homepage
    Given I am on the "Test book" "book activity editing" page
    And the following fields match these values:
      | Display description on course page | |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should not see "A book about dreams!"
