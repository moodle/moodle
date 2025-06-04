@qbank @qbank_viewquestionnname @javascript
Feature: Filter questions by idnumber
  As a teacher
  In order to organise my questions
  I want to filter the list of questions by idnumber

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
      | questioncategory | qtype     | name | questiontext               | idnumber |
      | Test questions   | truefalse | a    | Answer the first question  | q_01_ab  |
      | Test questions   | numerical | b    | Answer the second question | q_02_bc  |
      | Test questions   | essay     | c    | Answer the third question  | Q_21_ca  |
    And I am on the "Qbank 1" "core_question > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    And I should see "q_01_ab"
    And I should see "q_02_bc"
    And I should see "Q_21_ca"

  Scenario: Filter by a single term
    When I apply question bank filter "Question ID number" with value "ab"
    Then I should see "q_01_ab"
    And I should not see "q_02_bc"
    And I should not see "Q_21_ca"

  Scenario: Filter by any term
    When I apply question bank filter "Question ID number" with value "ab, ca"
    Then I should see "q_01_ab"
    And I should not see "q_02_bc"
    And I should see "Q_21_ca"

  Scenario: Filter by all terms
    When I add question bank filter "Question ID number"
    And I set the field "Question ID number" to "q, c"
    And I set the field "Match" in the "Filter 3" "fieldset" to "All"
    And I press "Apply filters"
    Then I should not see "q_01_ab"
    And I should see "q_02_bc"
    # Filter should be case-insensitive.
    And I should see "Q_21_ca"

  Scenario: Exclude idnumbers by filter
    When I add question bank filter "Question ID number"
    And I set the field "Question ID number" to "ab, ca"
    And I set the field "Match" in the "Filter 3" "fieldset" to "None"
    And I press "Apply filters"
    Then I should not see "q_01_ab"
    And I should see "q_02_bc"
    And I should not see "Q_21_ca"
