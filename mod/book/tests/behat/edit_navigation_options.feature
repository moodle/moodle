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
    And I follow "Course 1"
    And I turn editing mode on

  Scenario: Change navigation options
    Given I add a "Book" to section "1" and I fill the form with:
      | Name | Test book |
      | Description | A book about lorem ipsum |
      | Style of navigation | TOC Only         |
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
    And "Next" "link" should not exist
    And I click on "2. Test chapter 2" "link" in the "Table of contents" "block"
    And "Previous" "link" should not exist
    And I navigate to "Edit settings" in current page administration
    And I set the field "Style of navigation" to "Images"
    And I press "Save and display"
    And "//a/child::img[contains(@src, 'nav_next')]" "xpath_element" should exist
    And I click on "2. Test chapter 2" "link" in the "Table of contents" "block"
    And "//a/child::img[contains(@src, 'nav_prev')]" "xpath_element" should exist
    When I navigate to "Edit settings" in current page administration
    And I set the field "Style of navigation" to "Text"
    And I press "Save and display"
    Then "Next" "link" should exist
    And I click on "2. Test chapter 2" "link" in the "Table of contents" "block"
    And "Previous" "link" should exist
