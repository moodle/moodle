@mod @mod_book
Feature: Display the course linear navigation in the book pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in book pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name  | idnumber |
      | book     | C1     | Book1 | book1    |
    And the following "mod_book > chapters" exist:
      | book  | title          | content                | pagenum |subchapter |
      | book1 | First chapter  | First chapter content  | 1       | 0         |
      | book1 | Second chapter | Second chapter content | 2       | 0         |
      | book1 | Sub chapter    | Sub chapter content    | 3       | 1         |
      | book1 | Third chapter  | Third chapter content  | 4       | 0         |

  @javascript
  Scenario: As a student I should see the course linear navigation in book pages that allow it
    Given I am on the "Book1" "book activity" page logged in as "student"
    Then the course linear navigation should be visible
    And I follow "Next"
    And I should see "Second chapter content"
    And the course linear navigation should be visible
    But I navigate to "Print book" in current page administration
    And I switch to a second window
    And the course linear navigation should not be visible
    And I close all opened windows
    And I navigate to "Print this chapter" in current page administration
    And I switch to a second window
    And the course linear navigation should not be visible

  @javascript
  Scenario: As a teacher I should see the course linear navigation in book pages that allow it
    Given I am on the "Book1" "book activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    But I turn editing mode on
    And I click on "Add new chapter after \"Third chapter\"" "link" in the "Table of contents" "block"
    And the course linear navigation should not be visible
    And I navigate to "Import chapter" in current page administration
    And the course linear navigation should not be visible
