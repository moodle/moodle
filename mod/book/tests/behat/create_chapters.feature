@mod @mod_book
Feature: In a book, create chapters and sub chapters
  In order to create chapters and subchapters
  As a teacher
  I need to add chapters and subchapters to a book.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
    And the following "activities" exist:
      | activity | name      | intro                 | course | section |
      | book     | Test book | A book about dreams!  | C1     | 1       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Create chapters and sub chapters and navigate between them
    Given I am on the "Test book" Activity page
    And I should see "Add new chapter"
    And I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
    And I press "Save changes"
    And I should not see "No content has been added to this book yet."
    And I should see "1. Dummy first chapter" in the "Table of contents" "block"
    And I click on "Add new chapter after \"Dummy first chapter\"" "link" in the "Table of contents" "block"
    And I should see "Dummy first chapter"
    And I set the following fields to these values:
      | Chapter title | Dummy second chapter |
      | Content | The path is the second part |
    And I press "Save changes"
    And I should see "2. Dummy second chapter" in the "Table of contents" "block"
    And I click on "Add new chapter after \"Dummy first chapter\"" "link" in the "Table of contents" "block"
    And I should see "Dummy first chapter"
    And I set the following fields to these values:
      | Chapter title | Dummy first subchapter |
      | Content | The path is the second part |
      | Subchapter | true |
    And I press "Save changes"
    And I should see "1.1. Dummy first subchapter" in the "Table of contents" "block"
    And I should see "1. Dummy first chapter" in the ".book_content" "css_element"
    And I should see "1.1. Dummy first subchapter" in the ".book_content" "css_element"
    And I click on "Next" "link"
    And I should see "2. Dummy second chapter" in the ".book_content" "css_element"
    And I should see "2. Dummy second chapter" in the "strong" "css_element"
    And I should not see "Next" in the ".book_content" "css_element"
    And I am on "Course 1" course homepage
    And I should see "Test book" in the "Topic 1" "section"
    And I click on "Test book" "link" in the "Topic 1" "section"
    And I should not see "Previous" in the ".book_content" "css_element"
    And I should see "1. Dummy first chapter" in the "strong" "css_element"
    When I click on "Next" "link"
    Then I should see "1.1. Dummy first subchapter" in the ".book_content" "css_element"
    And I should see "1.1. Dummy first subchapter" in the "strong" "css_element"
    And I click on "Previous" "link"
    And I should see "1. Dummy first chapter" in the ".book_content" "css_element"
    And I should see "1. Dummy first chapter" in the "strong" "css_element"

  Scenario: Change editing mode for an individual chapter
    Given I am on the "Test book" Activity page
    And I should see "Add new chapter"
    And I set the following fields to these values:
      | Chapter title | Dummy first chapter |
      | Content | Dream is the start of a journey |
    And I press "Save changes"
    And I should see "1. Dummy first chapter" in the "Table of contents" "block"
    And "Edit chapter \"1. Dummy first chapter\"" "link" should exist in the "Table of contents" "block"
    And "Delete chapter \"1. Dummy first chapter\"" "link" should exist in the "Table of contents" "block"
    And "Hide chapter \"1. Dummy first chapter\"" "link" should exist in the "Table of contents" "block"
    And "Add new chapter" "link" should exist in the "Table of contents" "block"
    When I turn editing mode off
    Then "Edit chapter \"1. Dummy first chapter\"" "link" should not exist in the "Table of contents" "block"
    And "Delete chapter \"1. Dummy first chapter\"" "link" should not exist in the "Table of contents" "block"
    And "Hide chapter \"1. Dummy first chapter\"" "link" should not exist in the "Table of contents" "block"
    And "Add new chapter after \"Dummy first chapter\"" "link" should not exist in the "Table of contents" "block"

  Scenario: When chapters are not created yet, students can see a notification in the book activity
    Given I am on the "Test book" "book activity" page logged in as student1
    Then I should see "No content has been added to this book yet." in the ".alert-info" "css_element"
    And I should not see "Table of contents"
