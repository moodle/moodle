@qbank @qbank_editquestion @javascript
Feature: Filter questions by status
  As a teacher
  In order to quickly find questions in Draft or Ready status
  I want to filter the list of questions by status

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber |
      | qbank      | Qbank 1 | Question bank 1    | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name            | questiontext               | status |
      | Test questions   | truefalse | First question  | Answer the first question  | ready  |
      | Test questions   | numerical | Second question | Answer the second question | draft  |
      | Test questions   | essay     | Third question  | Answer the third question  | ready  |

  Scenario: Filter by ready status
    Given I am on the "Qbank 1" "core_question > question bank" page logged in as "admin"
    And I should see "First question"
    And I should see "Second question"
    And I should see "Third question"
    When I apply question bank filter "Status of latest version" with value "Ready"
    Then I should see "First question"
    And I should not see "Second question"
    And I should see "Third question"

  Scenario: Filter by draft status
    Given I am on the "Qbank 1" "core_question > question bank" page logged in as "admin"
    And I should see "First question"
    And I should see "Second question"
    And I should see "Third question"
    When I apply question bank filter "Status of latest version" with value "Draft"
    Then I should not see "First question"
    And I should see "Second question"
    And I should not see "Third question"
