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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Book" to section "1" and I fill the form with:
      | Name | Test book |
      | Description | A book about rearrangements! |
    And I follow "Test book"
    And I should see "Add new chapter"
    And I set the following fields to these values:
      | Chapter title | Originally first chapter |
      | Content | #1 chapter content |
    And I press "Save changes"
    And I click on "a[href*='pagenum=1']" "css_element"
    And I set the following fields to these values:
      | Chapter title | A great second chapter |
      | Content | #2 chapter content |
    And I press "Save changes"
    And I click on "a[href*='pagenum=2']" "css_element"
    And I set the following fields to these values:
      | Chapter title | Second chapter, subchapter 1 |
      | Content | #21 subchapter content |
      | Subchapter | 1 |
    And I press "Save changes"
    And I click on "a[href*='pagenum=3']" "css_element"
    And I set the following fields to these values:
      | Chapter title | Second chapter, subchapter 2 |
      | Content | #22 subchapter content |
      | Subchapter | 1 |
    And I press "Save changes"
    And I click on "a[href*='pagenum=4']" "css_element"
    And I set the following fields to these values:
      | Chapter title | There aren't 2 without 3 |
      | Content | #3 subchapter content |
      | Subchapter | 0 |
    And I press "Save changes"

  Scenario: Moving chapters down rearranges them properly
    Given I click on "Move chapter down \"1. Originally first chapter\"" "link"
    When I follow "Test book"
    Then I should see "1. A great second chapter"
    And I should see "#2 chapter content"
    And I should see "1.1. Second chapter, subchapter 1"
    And I should see "1.2. Second chapter, subchapter 2"
    And I should see "2. Originally first chapter"
    And I should see "3. There aren't 2 without 3"

  Scenario: Moving chapters up rearranges them properly
    Given I click on "Move chapter up \"3. There aren't 2 without 3\"" "link"
    When I follow "Test book"
    Then I should see "1. Originally first chapter"
    And I should see "#1 chapter content"
    And I should see "2. There aren't 2 without 3"
    And I should see "3. A great second chapter"
    And I should see "3.1. Second chapter, subchapter 1"
    And I should see "3.2. Second chapter, subchapter 2"

  Scenario: Moving subchapters down within chapter rearranges them properly
    Given I click on "Move chapter down \"2.1. Second chapter, subchapter 1\"" "link"
    When I follow "Test book"
    Then I should see "2.1. Second chapter, subchapter 2"
    And I should see "2.2. Second chapter, subchapter 1"

  Scenario: Moving subchapters down out of chapter rearranges them properly
    Given I click on "Move chapter down \"2.2. Second chapter, subchapter 2\"" "link"
    When I follow "Test book"
    Then I should see "3.1. Second chapter, subchapter 2"
    And I click on "Move chapter down \"3. There aren't 2 without 3\"" "link"
    And I should not see "4. There aren't 2 without 3"
    And I should see "3. There aren't 2 without 3"
    And I should see "3.1. Second chapter, subchapter 2"

  Scenario: Moving subchapters up within chapter rearranges them properly
    Given I click on "Move chapter up \"2.2. Second chapter, subchapter 2\"" "link"
    When I follow "Test book"
    Then I should see "2.1. Second chapter, subchapter 2"
    And I should see "2.2. Second chapter, subchapter 1"

  Scenario: Moving subchapters up out of chapter rearranges them properly
    Given I click on "Move chapter up \"2.1. Second chapter, subchapter 1\"" "link"
    When I follow "Test book"
    Then I should see "1.1. Second chapter, subchapter 1"
    And I click on "Move chapter up \"1.1. Second chapter, subchapter 1\"" "link"
    And I should not see "1.1. Second chapter, subchapter 1"
    And I should see "1. Second chapter, subchapter 1"
    And I should see "2. Originally first chapter"
