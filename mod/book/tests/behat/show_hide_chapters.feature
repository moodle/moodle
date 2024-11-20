@mod @mod_book
Feature: Book activity chapter visibility management
  In order to properly manage chapters in a book activity
  As a teacher
  I need to be able to show or hide chapters.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 2 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
    And the following "activity" exists:
      | course   | C1                  |
      | activity | book                |
      | name     | Test book           |
    And the following "mod_book > chapters" exist:
      | book      | title          | content        | pagenum |subchapter |
      | Test book | First chapter  | First chapter  | 1       | 0         |
      | Test book | Second chapter | Second chapter | 2       | 0         |
      | Test book | Sub chapter    | Sub chapter    | 3       | 1         |
      | Test book | Third chapter  | Third chapter  | 4       | 0         |
      | Test book | Fourth chapter | Fourth chapter | 5       | 0         |
    And I am on the "Course 1" course page logged in as teacher1
    And I turn editing mode on
    And I am on the "Test book" "book activity" page
    And I click on "4. Fourth chapter" "link" in the "Table of contents" "block"

  @javascript
  Scenario: Show/hide chapters and subchapters
    When I follow "Hide chapter \"2. Second chapter\""
    And I follow "Hide chapter \"2. Third chapter\""
    And I am on the "Test book" "book activity" page
    And I am on "Course 1" course homepage
    And I turn editing mode off
    And I click on "Test book" "link" in the "region-main" "region"
    Then the "class" attribute of "a[title='Second chapter']" "css_element" should contain "dimmed_text"
    And the "class" attribute of "a[title='Third chapter']" "css_element" should contain "dimmed_text"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Test book" "link" in the "region-main" "region"
    And I follow "Next"
    And I should see "Second chapter" in the ".book_content" "css_element"
    And I follow "Next"
    And I should see "Sub chapter" in the ".book_content" "css_element"
    And I follow "Next"
    And I should see "Third chapter" in the ".book_content" "css_element"
    And I follow "Next"
    And I should see "Fourth chapter" in the ".book_content" "css_element"
    And I am on the "Test book" "book activity" page logged in as student1
    And I should not see "Second chapter" in the "Table of contents" "block"
    And I should not see "Third chapter" in the "Table of contents" "block"
    And I follow "Next"
    And I should see "Fourth chapter" in the ".book_content" "css_element"
