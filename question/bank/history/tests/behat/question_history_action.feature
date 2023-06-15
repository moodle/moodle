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

  @javascript
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
    Then I should not see "Select a category"
    And I should see "No tag filters applied"
    And I should see "Question"
    And I should see "Actions"
    And I should see "Status"
    And I should see "Version"
    And I should see "Created by"
    And I should see "First question"
    And the "History" action should not exist for the "First question" question in the question bank
    And I click on "#qbank-history-close" "css_element"
    And the "History" action should exist for the "First question" question in the question bank

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
