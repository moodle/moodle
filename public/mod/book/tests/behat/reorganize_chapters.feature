@mod @mod_book
Feature: In a book, chapters and subchapters can be rearranged
  In order to rearrange chapters and subchapters
  As a teacher
  I need to move chapters and subchapters up and down.

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
      | activity | name      | course | idnumber |
      | book     | Test book | C1     | book1    |
    And the following "mod_book > chapters" exist:
      | book      | title                        | content                | pagenum |subchapter |
      | Test book | Originally first chapter     | #1 chapter content     | 1       | 0         |
      | Test book | A great second chapter       | #2 chapter content     | 2       | 0         |
      | Test book | Second chapter, subchapter 1 | #21 subchapter content | 3       | 1         |
      | Test book | Second chapter, subchapter 2 | #22 subchapter content | 4       | 1         |
      | Test book | There aren't 2 without 3     | #3 subchapter content  | 5       | 0         |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test book"

  Scenario: Moving chapters down rearranges them properly
    Given I click on "Move chapter down \"1. Originally first chapter\"" "link"
    When I am on the "Test book" "book activity" page
    Then I should see "1. A great second chapter"
    And I should see "#2 chapter content"
    And I should see "1.1. Second chapter, subchapter 1"
    And I should see "1.2. Second chapter, subchapter 2"
    And I should see "2. Originally first chapter"
    And I should see "3. There aren't 2 without 3"

  Scenario: Moving chapters up rearranges them properly
    Given I click on "Move chapter up \"3. There aren't 2 without 3\"" "link"
    When I am on the "Test book" "book activity" page
    Then I should see "1. Originally first chapter"
    And I should see "#1 chapter content"
    And I should see "2. There aren't 2 without 3"
    And I should see "3. A great second chapter"
    And I should see "3.1. Second chapter, subchapter 1"
    And I should see "3.2. Second chapter, subchapter 2"

  Scenario: Moving subchapters down within chapter rearranges them properly
    Given I click on "Move chapter down \"2.1. Second chapter, subchapter 1\"" "link"
    When I should see "2.1. Second chapter, subchapter 2"
    Then I should see "2.2. Second chapter, subchapter 1"

  Scenario: Moving subchapters down out of chapter rearranges them properly
    Given I click on "Move chapter down \"2.2. Second chapter, subchapter 2\"" "link"
    When I should see "3.1. Second chapter, subchapter 2"
    Then I click on "Move chapter down \"3. There aren't 2 without 3\"" "link"
    And I should not see "4. There aren't 2 without 3"
    And I should see "3. There aren't 2 without 3"
    And I should see "3.1. Second chapter, subchapter 2"

  Scenario: Moving subchapters up within chapter rearranges them properly
    Given I click on "Move chapter up \"2.2. Second chapter, subchapter 2\"" "link"
    When I should see "2.1. Second chapter, subchapter 2"
    Then I should see "2.2. Second chapter, subchapter 1"

  Scenario: Moving subchapters up out of chapter rearranges them properly
    Given I click on "Move chapter up \"2.1. Second chapter, subchapter 1\"" "link"
    When I should see "1.1. Second chapter, subchapter 1"
    Then I click on "Move chapter up \"1.1. Second chapter, subchapter 1\"" "link"
    And I should not see "1.1. Second chapter, subchapter 1"
    And I should see "1. Second chapter, subchapter 1"
    And I should see "2. Originally first chapter"
