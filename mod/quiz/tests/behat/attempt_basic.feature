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
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
      | Test questions   | truefalse   | TF3   | Third question  |
      | Test questions   | truefalse   | TF4   | Fourth question |
      | Test questions   | truefalse   | TF5   | Fifth question  |
      | Test questions   | truefalse   | TF6   | Sixth question  |
    And the following "activities" exist:
      | activity | name   | intro              | course | idnumber | grade | navmethod  |
      | quiz     | Quiz 1 | Quiz 1 description | C1     | quiz1    | 100   | free       |
      | quiz     | Quiz 2 | Quiz 2 description | C1     | quiz2    | 6     | free       |
      | quiz     | Quiz 3 | Quiz 3 description | C1     | quiz3    | 100   | free       |
      | quiz     | Quiz 4 | Quiz 4 description | C1     | quiz4    | 100   | sequential |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And quiz "Quiz 2" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 1    |
      | TF3      | 2    |
      | TF4      | 3    |
      | TF5      | 4    |
      | TF6      | 4    |
    And quiz "Quiz 2" contains the following sections:
      | heading   | firstslot | shuffle |
      | Section 1 | 1         | 0       |
      | Section 2 | 3         | 0       |
      |           | 4         | 1       |
      | Section 3 | 5         | 1       |
    And quiz "Quiz 3" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
    And quiz "Quiz 4" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |

  @javascript
  Scenario: Attempt a quiz with a single unnamed section, review and re-attempt
    Given user "student" has attempted "Quiz 1" with responses:
      | slot | response |
      |   1  | True     |
      |   2  | False    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I follow "Review"
    Then I should see "Started on"
    And I should see "State"
    And I should see "Completed on"
    And I should see "Time taken"
    And I should see "Marks"
    And I should see "Grade"
    And I should see "25.00 out of 100.00"
    And I follow "Finish review"
    And I press "Re-attempt quiz"

  @javascript
  Scenario: Attempt a quiz with multiple sections
    Given I am on the "Quiz 2" "mod_quiz > View" page logged in as "student"
    When I press "Attempt quiz"

    Then I should see "Section 1" in the "Quiz navigation" "block"
    And I should see question "1" in section "Section 1" in the quiz navigation
    And I should see question "2" in section "Section 1" in the quiz navigation
    And I should see question "3" in section "Section 2" in the quiz navigation
    And I should see question "4" in section "Untitled section" in the quiz navigation
    And I should see question "5" in section "Section 3" in the quiz navigation
    And I should see question "6" in section "Section 3" in the quiz navigation
    And I click on "True" "radio" in the "First question" "question"

    And I follow "Finish attempt ..."
    And I should see question "1" in section "Section 1" in the quiz navigation
    And I should see question "2" in section "Section 1" in the quiz navigation
    And I should see question "3" in section "Section 2" in the quiz navigation
    And I should see question "4" in section "Untitled section" in the quiz navigation
    And I should see question "5" in section "Section 3" in the quiz navigation
    And I should see question "6" in section "Section 3" in the quiz navigation
    And I should see "Section 1" in the "quizsummaryofattempt" "table"
    And I should see "Section 2" in the "quizsummaryofattempt" "table"
    And I should see "Untitled section" in the "quizsummaryofattempt" "table"
    And I should see "Section 3" in the "quizsummaryofattempt" "table"

    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I should see "1.00 out of 6.00 (16.67%)" in the "Grade" "table_row"
    And I should see question "1" in section "Section 1" in the quiz navigation
    And I should see question "2" in section "Section 1" in the quiz navigation
    And I should see question "3" in section "Section 2" in the quiz navigation
    And I should see question "4" in section "Untitled section" in the quiz navigation
    And I should see question "5" in section "Section 3" in the quiz navigation
    And I should see question "6" in section "Section 3" in the quiz navigation

    And I follow "Show one page at a time"
    And I should see "First question"
    And I should not see "Third question"
    And I should see "Next page"

    And I follow "Show all questions on one page"
    And I should see "Fourth question"
    And I should see "Sixth question"
    And I should not see "Next page"

  @javascript
  Scenario: Next and previous navigation
    Given I am on the "Quiz 3" "mod_quiz > View" page logged in as "student"
    When I press "Attempt quiz"
    Then I should see "First question"
    And I should not see "Second question"
    And I press "Next page"
    And I should see "Second question"
    And I should not see "First question"
    And I click on "Finish attempt ..." "button" in the "region-main" "region"
    And I should see "Summary of attempt"
    And I press "Return to attempt"
    And I should see "Second question"
    And I should not see "First question"
    And I press "Previous page"
    And I should see "First question"
    And I should not see "Second question"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I should see "Once you submit your answers, you won’t be able to change them." in the "Submit all your answers and finish?" "dialogue"
    And I should see "Questions without a response: 2" in the "Submit all your answers and finish?" "dialogue"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I should see "First question"
    And I should see "Second question"
    And I follow "Show one page at a time"
    And I should see "First question"
    And I should not see "Second question"
    And I follow "Next page"
    And I should see "Second question"
    And I should not see "First question"
    And I follow "Previous page"
    And I should see "First question"
    And I should not see "Second question"

  @javascript
  Scenario: Next and previous with sequential navigation method
    Given I am on the "Quiz 4" "mod_quiz > View" page logged in as "student"
    When I press "Attempt quiz"
    Then I should see "First question"
    And I should not see "Second question"
    And I press "Next page"
    And I should see "Second question"
    And I should not see "First question"
    And "Previous page" "button" should not exist
    And I click on "Finish attempt ..." "button" in the "region-main" "region"
    And I should see "Summary of attempt"
    And I press "Submit all and finish"
    And I should see "Once you submit your answers, you won’t be able to change them." in the "Submit all your answers and finish?" "dialogue"
    And I should not see "Questions without a response: 2" in the "Submit all your answers and finish?" "dialogue"
    And I click on "Submit" "button" in the "Submit all your answers and finish?" "dialogue"
    And I should see "First question"
    And I should see "Second question"
    And I follow "Show one page at a time"
    And I should see "First question"
    And I should not see "Second question"
    And I follow "Next page"
    And I should see "Second question"
    And I should not see "First question"
    And I follow "Previous page"
    And I should see "First question"
    And I should not see "Second question"

  @javascript
  Scenario: Take a quiz with number of attempts set
    Given the following "activities" exist:
      | activity | name   | course | grade | navmethod  | attempts |
      | quiz     | Quiz 5 | C1     | 100   | free       | 2        |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF7   | First question  |
    And quiz "Quiz 5" contains the following questions:
      | question | page |
      | TF7      | 1    |
    And user "student" has attempted "Quiz 5" with responses:
      | slot | response |
      |   1  | True     |
    When I am on the "Quiz 5" "mod_quiz > View" page logged in as "student"
    Then I should see "Attempts allowed: 2"
    And I should not see "No more attempts are allowed"
    And I press "Re-attempt quiz"
    And I should see "First question"
    And I click on "Finish attempt ..." "button" in the "region-main" "region"
    And I press "Submit all and finish"
    And I should see "Once you submit your answers, you won’t be able to change them." in the "Submit all your answers and finish?" "dialogue"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I follow "Finish review"
    And I should not see "Re-attempt quiz"
    And I should see "No more attempts are allowed"
