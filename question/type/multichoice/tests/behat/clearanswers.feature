@qtype @qtype_multichoice
Feature: Clear my answers
  As a student
  In order to reset Multiple choice ansers
  I need to clear my choice

  Background:
    Given the following "users" exist:
      | username |
      | student  |
    And the following "course" exists:
      | fullname  | Course 1  |
      | shortname | C1        |
      | category  | 0         |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |
    And the following "question category" exists:
      | contextlevel  | Course          |
      | reference     | C1              |
      | name          | Test questions  |
    And the following "question" exists:
      |  questioncategory  |  Test questions    |
      |  qtype             |  multichoice       |
      |  name              |  Multi-choice-001  |
      |  template          |  one_of_four       |
      |  questiontext      |  Question One      |
    And the following "activity" exists:
      |  activity            |  quiz                |
      |  name                |  Quiz 1              |
      |  intro               |  Quiz 1 description  |
      |  course              |  C1                  |
      |  idnumber            |  quiz1               |
      |  preferredbehaviour  |  immediatefeedback   |
      |  canredoquestions    |  1                   |
    And quiz "Quiz 1" contains the following questions:
      | question         | page |
      | Multi-choice-001 | 1    |

  @javascript
  Scenario: Attempt a quiz and reset my chosen answer.
    Given I am on the "Quiz 1" "quiz activity" page logged in as student
    When I press "Attempt quiz"
    And I should see "Question One"
    And I click on "Four" "qtype_multichoice > Answer" in the "Question One" "question"
    And I should see "Clear my choice"
    And I click on "Clear my choice" "button" in the "Question One" "question"
    Then I should not see "Clear my choice"
    And I click on "Check" "button" in the "Question One" "question"
    And I should see "Please select an answer" in the "Question One" "question"

  @javascript
  Scenario: Attempt a quiz and revisit a cleared answer.
    Given I am on the "Quiz 1" "quiz activity" page logged in as student
    When I press "Attempt quiz"
    And I should see "Question One"
    And I click on "Four" "qtype_multichoice > Answer" in the "Question One" "question"
    And I follow "Finish attempt ..."
    And I click on "Return to attempt" "button"
    And I click on "Clear my choice" "button" in the "Question One" "question"
    And I follow "Finish attempt ..."
    And I click on "Return to attempt" "button"
    Then I should not see "Clear my choice"
