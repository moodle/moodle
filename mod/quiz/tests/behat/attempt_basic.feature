@mod @mod_quiz
Feature: Attempt a quiz
  As a student
  In order to demonstrate what I know
  I need to be able to attempt quizzes

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | student  | Student   | One      | student@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student  | C1     | student |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |

  @javascript
  Scenario: Attempt a quiz with a single unnamed section
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And user "student" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I follow "Review"
    Then I should see "25.00 out of 100.00"

  @javascript
  Scenario: Attempt a quiz with mulitple sections
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
      | Test questions   | truefalse   | TF3   | Third question  |
      | Test questions   | truefalse   | TF4   | Fourth question |
      | Test questions   | truefalse   | TF5   | Fifth question  |
      | Test questions   | truefalse   | TF6   | Sixth question  |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 1    |
      | TF3      | 2    |
      | TF4      | 3    |
      | TF5      | 4    |
      | TF6      | 4    |
    And quiz "Quiz 1" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 1       |
      | Section 2 | 3         | 0       |
      |           | 4         | 1       |
      | Section 3 | 5         | 0       |

    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz now"

    Then I should see "Section 1" in the "Quiz navigation" "block"
    And I should see question "1" in section "Section 1" in the quiz navigation
    And I should see question "2" in section "Section 1" in the quiz navigation
    And I should see question "3" in section "Section 2" in the quiz navigation
    And I should see question "4" in section "Section 2" in the quiz navigation
    And I should see question "5" in section "Section 3" in the quiz navigation
    And I should see question "6" in section "Section 3" in the quiz navigation

    And I follow "Finish attempt ..."
    And I should see question "1" in section "Section 1" in the quiz navigation
    And I should see question "2" in section "Section 1" in the quiz navigation
    And I should see question "3" in section "Section 2" in the quiz navigation
    And I should see question "4" in section "Section 2" in the quiz navigation
    And I should see question "5" in section "Section 3" in the quiz navigation
    And I should see question "6" in section "Section 3" in the quiz navigation
    And I should see "Section 1" in the "quizsummaryofattempt" "table"
    And I should see "Section 2" in the "quizsummaryofattempt" "table"
    And I should see "Section 3" in the "quizsummaryofattempt" "table"

    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I should see question "1" in section "Section 1" in the quiz navigation
    And I should see question "2" in section "Section 1" in the quiz navigation
    And I should see question "3" in section "Section 2" in the quiz navigation
    And I should see question "4" in section "Section 2" in the quiz navigation
    And I should see question "5" in section "Section 3" in the quiz navigation
    And I should see question "6" in section "Section 3" in the quiz navigation

  @javascript
  Scenario: Next and previous navigation
    Given the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext                |
      | Test questions   | truefalse   | TF1   | Text of the first question  |
      | Test questions   | truefalse   | TF2   | Text of the second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz now"
    Then I should see "Text of the first question"
    And I should not see "Text of the second question"
    And I press "Next page"
    And I should see "Text of the second question"
    And I should not see "Text of the first question"
    And I click on "Finish attempt ..." "button" in the "region-main" "region"
    And I should see "Summary of attempt"
    And I press "Return to attempt"
    And I should see "Text of the second question"
    And I should not see "Text of the first question"
    And I press "Previous page"
    And I should see "Text of the first question"
    And I should not see "Text of the second question"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I should see "Text of the first question"
    And I should see "Text of the second question"
    And I follow "Show one page at a time"
    And I should see "Text of the first question"
    And I should not see "Text of the second question"
    And I follow "Next page"
    And I should see "Text of the second question"
    And I should not see "Text of the first question"
    And I follow "Previous page"
    And I should see "Text of the first question"
    And I should not see "Text of the second question"
