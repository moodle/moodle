@qbank @qbank_editquestion
Feature: Use the qbank base view to test the status change using
  the pop up

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
      | questioncategory | qtype     | name            | questiontext              |
      | Test questions   | truefalse | First question  | Answer the first question |
      | Test questions   | truefalse | Second question | Answer the first question |

  @javascript
  Scenario: Question status dropdown should change the status of the question
    Given I log in as "admin"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I set the field "Select a category" to "Test questions"
    And I should see "Test questions"
    And I should see "Ready" in the "First question" "table_row"
    And I should see "Ready" in the "Second question" "table_row"
    And I click on "question_status_dropdown" "select" in the "First question" "table_row"
    And I should see "Draft"
    And I click on "Draft" "option"
    And I reload the page
    And I should see "Draft" in the "First question" "table_row"
    And I should see "Ready" in the "Second question" "table_row"
    And I click on "question_status_dropdown" "select" in the "Second question" "table_row"
    And I click on "Draft" "option"
    And I click on "question_status_dropdown" "select" in the "First question" "table_row"
    And I click on "Ready" "option"
    And I reload the page
    Then I should see "Ready" in the "First question" "table_row"
    And I should see "Draft" in the "Second question" "table_row"
