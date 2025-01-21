@qbank @qbank_viewquestiontext @javascript
Feature: Filter questions by text found in the question text or general feedback
  As a teacher
  In order to organise my questions
  I want to filter the list of questions by text content

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
      | questioncategory | qtype         | name            | questiontext  | generalfeedback  |
      | Test questions   | truefalse     | Question 1      | ABC           | EFG              |
      | Test questions   | truefalse     | Question 2      | CDE           | ABC              |
      | Test questions   | truefalse     | Question 3      | EFGZ          | IJK              |
      | Test questions   | truefalse     | Question 4      | GHI           | CDEZ             |
      | Test questions   | truefalse     | Question 5      | IJK           | GHI              |
    And I am on the "Qbank 1" "core_question > question bank" page logged in as "admin"
    And I should see "Question 1"
    And I should see "Question 2"
    And I should see "Question 3"
    And I should see "Question 4"
    And I should see "Question 5"

  Scenario: Filter by a single piece of text
    When I apply question bank filter "Question text and general feedback" with value "A"
    Then I should see "Question 1"
    And I should see "Question 2"
    And I should not see "Question 3"
    And I should not see "Question 4"
    And I should not see "Question 5"

  Scenario: Filter by any piece of text
    When I apply question bank filter "Question text and general feedback" with value "A, K"
    Then I should see "Question 1"
    And I should see "Question 2"
    And I should see "Question 3"
    And I should see "Question 5"
    And I should not see "Question 4"

  Scenario: Filter by all pieces of text
    When I add question bank filter "Question text and general feedback"
    And I set the field "Question text and general feedback" to "F, G"
    And I set the field "Match" in the "Filter 3" "fieldset" to "All"
    And I press "Apply filters"
    Then I should see "Question 1"
    Then I should see "Question 3"
    Then I should not see "Question 2"
    Then I should not see "Question 4"
    Then I should not see "Question 5"

  Scenario: Exclude text by filter
    When I add question bank filter "Question text and general feedback"
    And I set the field "Question text and general feedback" to "A, Z"
    And I set the field "Match" in the "Filter 3" "fieldset" to "None"
    And I press "Apply filters"
    Then I should see "Question 5"
    Then I should not see "Question 1"
    Then I should not see "Question 2"
    Then I should not see "Question 3"
    Then I should not see "Question 4"
