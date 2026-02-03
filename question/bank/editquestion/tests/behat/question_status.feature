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
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name            | questiontext              |
      | Test questions   | truefalse | First question  | Answer the first question |
      | Test questions   | truefalse | Second question | Answer the first question |

  @javascript
  Scenario: Question status dropdown should change the status of the question
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I should see "Test questions"
    And the field "question_status_dropdown" in the "First question" "table_row" matches value "Ready"
    And the field "question_status_dropdown" in the "Second question" "table_row" matches value "Ready"
    And I set the field "question_status_dropdown" to "Draft"
    And I reload the page
    And the field "question_status_dropdown" in the "First question" "table_row" matches value "Draft"
    And the field "question_status_dropdown" in the "Second question" "table_row" matches value "Ready"
    And I set the field "question_status_dropdown" in the "Second question" "table_row" to "Draft"
    And I set the field "question_status_dropdown" in the "First question" "table_row" to "Ready"
    And I reload the page
    And the field "question_status_dropdown" in the "First question" "table_row" matches value "Ready"
    And the field "question_status_dropdown" in the "Second question" "table_row" matches value "Draft"

  @javascript
  Scenario: Non-editing users see a static output of the status
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | 1        |
    And the following "permission overrides" exist:
      | capability              | permission | role           | contextlevel | reference |
      | moodle/question:editall | Prevent    | editingteacher | System       |           |
    And the following "course enrolments" exist:
      | course | user     | role           |
      | C1     | teacher1 | editingteacher |
    And the following "questions" exist:
      | questioncategory | qtype     | name            | questiontext              | status |
      | Test questions   | truefalse | Third question  | Answer the first question | draft  |
      | Test questions   | truefalse | Fourth question | Answer the first question | hidden |
    When I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    And I apply question bank filter "Category" with value "Test questions"
    And I apply question bank filter "Show hidden questions" with value "Yes"
    Then I should see "Test questions"
    And "question_status_dropdown" "field" should not exist
    And I should see "Ready" in the "First question" "table_row"
    And I should see "Ready" in the "Second question" "table_row"
    And I should see "Draft" in the "Third question" "table_row"
    And I should see "Hidden" in the "Fourth question" "table_row"
