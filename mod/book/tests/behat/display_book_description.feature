@mod @mod_book
Feature: Display the book description in the book and optionally in the course
  In order to display the the book description in the course
  As a teacher
  I need to enable the 'Display description on course page' setting.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name      | intro                | course | idnumber | section |
      | book     | Test book | A book about dreams! | C1     | book1    | 1       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I am on the "Test book" "book activity" page
    And I should see "Add new chapter"
    And I set the following fields to these values:
      | Chapter title | Dummy first chapter             |
      | Content       | Dream is the start of a journey |
    And I press "Save changes"

  Scenario: Description is displayed in the book
    Given I am on "Course 1" course homepage
    When I am on the "Test book" "book activity" page
    Then I should see "A book about dreams!"

  Scenario: Show book description in the course homepage
    Given I am on "Course 1" course homepage
    And I am on the "Test book" "book activity" page
    And I navigate to "Settings" in current page administration
    And the following fields match these values:
      | Display description on course page |  |
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should see "A book about dreams!"

  Scenario: Hide book description in the course homepage
    Given I am on "Course 1" course homepage
    And I am on the "Test book" "book activity" page
    And I navigate to "Settings" in current page administration
    And the following fields match these values:
      | Display description on course page |  |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should not see "A book about dreams!"

  @javascript
  Scenario: Description is displayed in the book for students when there are no chapters added yet
    Given I am on "Course 1" course homepage with editing mode on
    And I am on the "Test book" "book activity" page
    And I click on "Delete chapter \"1. Dummy first chapter\"" "link" in the "Table of contents" "block"
    And I click on "Yes" "button" in the "Confirmation" "dialogue"
    And I log out
    And I am on the "Test book" "book activity" page logged in as student1
    Then I should see "A book about dreams!"
