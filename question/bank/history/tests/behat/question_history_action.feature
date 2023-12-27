@qbank @qbank_history
Feature: Use the qbank plugin manager page for question history
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course         | C1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

  Scenario: Enable/disable question history column from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Question history"
    And I click on "Disable" "link" in the "Question history" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then the "History" action should not exist for the "First question" question in the question bank
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Question history" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then the "History" action should exist for the "First question" question in the question bank

  Scenario: History page shows only the specified features and questions
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I choose "History" action for "First question" in the question bank
    And I should see "Question"
    And I should see "Actions"
    And I should see "Status"
    And I should see "Version"
    And I should see "Created by"
    And I should see "First question"
    And the "History" action should not exist for the "First question" question in the question bank

  @javascript
  Scenario: Viewing history for a question in a non-default category
    Given the following "question categories" exist:
      | contextlevel | reference | name             |
      | Course       | C1        | Test questions 2 |
    And the following "questions" exist:
      | questioncategory | qtype     | name            | questiontext               |
      | Test questions 2 | truefalse | Second question | Answer the second question |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions 2"
    And I choose "History" action for "Second question" in the question bank
    Then I should see "Question history"
    And "Filter 1" "fieldset" should not exist
    And I should see "Second question"
    And "Second question" "table_row" should exist

  @javascript
  Scenario: Delete question from the history using Edit question menu
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I choose "History" action for "First question" in the question bank
    When I choose "Delete" action for "First question" in the question bank
    And I press "Delete"
    And I should not see "First question"
    Then I should see "All versions of this question have been deleted."
    And I click on "Continue" "button"
    And I should see "Question bank"
    And I should not see "First question"
