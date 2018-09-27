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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Book" to section "1" and I fill the form with:
      | Name | Test book |
      | Description | A book about dreams! |
    And I follow "Test book"
    And I set the following fields to these values:
      | Chapter title | First chapter |
      | Content | First chapter |
    And I press "Save changes"
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
    And I turn editing mode off
    And I am on "Course 1" course homepage
    And I follow "Test book"
    Then the "class" attribute of "a[title='Second chapter']" "css_element" should contain "dimmed_text"
    And the "class" attribute of "a[title='Third chapter']" "css_element" should contain "dimmed_text"
    And I follow "Next"
    And I should see "Fourth chapter" in the ".book_content" "css_element"
    And I follow "Exit book"
    And I follow "Test book"
    And I should see "First chapter" in the ".book_content" "css_element"
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
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test book"
    And I should not see "Second chapter" in the "Table of contents" "block"
    And I should not see "Third chapter" in the "Table of contents" "block"
