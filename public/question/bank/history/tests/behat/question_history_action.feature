@qbank @qbank_history
Feature: Use the qbank plugin manager page for question history
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity | name      | intro           | course | idnumber |
      | quiz     | Test quiz |                 | C1     | quiz1    |
      | qbank    | Qbank 1   | Question bank 1 | C1     | qbank1   |
      | qbank    | Qbank 2   | Question bank 2 | C1     | qbank2   |
    And the following "question categories" exist:
      | contextlevel    | reference | name              |
      | Activity module | quiz1     | Test questions    |
      | Activity module | qbank1    | Share questions   |
      | Activity module | qbank2    | Share questions 2 |
    And the following "questions" exist:
      | questioncategory | qtype     | name                       | questiontext                  |
      | Test questions   | truefalse | First question             | Answer the first question     |
      | Share questions  | essay     | Test question to be edited | Write about whatever you want |

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
      | contextlevel    | reference | name             |
      | Activity module | quiz1     | Test questions 2 |
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

  Scenario: Viewing history for a Question in a Subcategory
    Given the following "question categories" exist:
      | contextlevel    | reference | name                 |
      | Activity module | quiz1     | Questions Category 1 |
    And the following "question categories" exist:
      | contextlevel    | reference | name          | questioncategory     |
      | Activity module | quiz1     | Subcategory 1 | Questions Category 1 |
    And the following "questions" exist:
      | questioncategory | qtype     | name                | questiontext       |
      | Subcategory 1    | truefalse | First question (v1) | Question version 1 |
    When I am on the "Test quiz" "mod_quiz > question categories" page logged in as "admin"
    And I should see "Subcategory 1"
    And I click on "Subcategory 1" "link"
    Then I should see "First question (v1)"
    And I choose "Edit question" action for "First question (v1)" in the question bank
    And I set the following fields to these values:
      | Question name | First question (v2) |
      | Question text | Question version 2  |
    And I press "id_submitbutton"
    And I choose "History" action for "First question (v2)" in the question bank
    And "First question (v1)" "table_row" should exist
    And "First question (v2)" "table_row" should exist

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

  Scenario: Resetting the columns in the question history view will return it to its default setting.
    Given the following "user preferences" exist:
      | user  | preference                       | value                                                           |
      | admin | qbank_columnsortorder_hiddencols | qbank_usage\question_last_used_column-question_last_used_column |
    And the following "questions" exist:
      | questioncategory | qtype     | name            | questiontext               |
      | Test questions   | truefalse | Second question | Answer the second question |
    When I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And "Last used" "qbank_columnsortorder > column header" should not exist
    Then I should see "First question"
    And I should see "Second question"
    And I choose "History" action for "First question" in the question bank
    And "First question" "table_row" should exist
    And "Second question" "table_row" should not exist
    And "Last used" "qbank_columnsortorder > column header" should not exist
    And I follow "Reset columns"
    And "Last used" "qbank_columnsortorder > column header" should exist
    And "First question" "table_row" should exist
    And "Second question" "table_row" should not exist

  Scenario: Go History page in edit question page.
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    When I choose "Edit question" action for "First question" in the question bank
    And I click on "History" "link"
    Then I should see "First question"
    And I follow "Close"
    And the following fields match these values:
      | Question text | Answer the first question |
