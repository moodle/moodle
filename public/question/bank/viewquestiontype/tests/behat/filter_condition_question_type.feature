@qbank @qbank_viewquestiontype @javascript
Feature: Filter questions by type
  As a teacher
  In order to organise my questions
  I want to filter the list of questions by type

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber |
      | qbank      | Qbank 1 | Question bank 1    | C1     | qbank1   |
    And the following "questions" exist:
      | questioncategory    | qtype     | name            | questiontext               |
      | Default for Qbank 1 | truefalse | First question  | Answer the first question  |
      | Default for Qbank 1 | numerical | Second question | Answer the second question |
      | Default for Qbank 1 | essay     | Third question  | Answer the third question  |
    And I am on the "Qbank 1" "core_question > question bank" page logged in as "admin"

  Scenario: Filter by a single type
    When I apply question bank filter "Type" with value "True/False"
    Then I should see "First question"
    And I should not see "Second question"
    And I should not see "Third question"

  Scenario: Filter by multiple types
    When I apply question bank filter "Type" with value "True/False, Essay"
    Then I should see "First question"
    And I should not see "Second question"
    And I should see "Third question"

  Scenario: Exclude types by filter
    When I add question bank filter "Type"
    And I set the field "Type" to "True/False, Essay"
    And I set the field "Match" in the "Filter 3" "fieldset" to "None"
    And I press "Apply filters"
    Then I should not see "First question"
    And I should see "Second question"
    And I should not see "Third question"
