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
    And I am on the "Test book" "book activity" page logged in as teacher1
    And I set the following fields to these values:
      | Chapter title | First chapter |
      | Content | First chapter |
    And I press "Save changes"
    And I turn editing mode on
    And I click on "Add new chapter after \"First chapter\"" "link"
    And I set the following fields to these values:
      | Chapter title | Second chapter |
      | Content | Second chapter |
    And I press "Save changes"
    And I click on "Add new chapter after \"Second chapter\"" "link"
    And I set the following fields to these values:
      | Chapter title | Sub chapter |
      | subchapter | 1 |
      | Content | Sub chapter |
    And I press "Save changes"
    And I click on "Add new chapter after \"Sub chapter\"" "link"
    And I set the following fields to these values:
      | Chapter title | Third chapter |
      | subchapter | 0 |
      | Content | Third chapter |
    And I press "Save changes"
    And I click on "Add new chapter after \"Third chapter\"" "link"
    And I set the following fields to these values:
      | Chapter title | Fourth chapter |
      | Content | Fourth chapter |
    And I press "Save changes"

  @javascript
  Scenario: Show/hide chapters and subchapters
    When I follow "Hide chapter \"2. Second chapter\""
    And I follow "Hide chapter \"2. Third chapter\""
    And I am on the "Test book" "book activity" page
    And I turn editing mode off
    Then the "class" attribute of "a[title='Second chapter']" "css_element" should contain "dimmed_text"
    And the "class" attribute of "a[title='Third chapter']" "css_element" should contain "dimmed_text"
    And I turn editing mode on
    And I follow "Next"
    And I should see "Second chapter" in the ".book_content" "css_element"
    And I should not see "Exit book"
    And I follow "Next"
    And I should see "Sub chapter" in the ".book_content" "css_element"
    And I follow "Next"
    And I should see "Third chapter" in the ".book_content" "css_element"
    And I follow "Next"
    And I should see "Fourth chapter" in the ".book_content" "css_element"
    And I follow "Exit book"
    And I log out
    And I am on the "Test book" "book activity" page logged in as student1
    And I should not see "Second chapter" in the "Table of contents" "block"
    And I should not see "Third chapter" in the "Table of contents" "block"
    And I follow "Next"
    And I should see "Fourth chapter" in the ".book_content" "css_element"
    And I follow "Exit book"
    And I am on the "Test book" "book activity" page
    And I should see "First chapter" in the ".book_content" "css_element"
