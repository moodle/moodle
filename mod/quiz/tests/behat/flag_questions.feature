@mod @mod_quiz
Feature: Flag quiz questions
  As a student
  In order to flag a quiz questions
  All review options for immediately after the attempt are ticked

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | student1 | Student   | 1        | student1@email.com |
      | teacher1 | Teacher   | 1        | teacher1@email.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | teacher1 | C1     | teacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
      | Test questions   | truefalse   | TF3   | Third question  |
    And the following "activity" exists:
      | activity                    | quiz   |
      | name                        | Quiz 1 |
      | course                      | C1     |
      | attemptimmediately          | 1      |
      | correctnessimmediately      | 1      |
      | maxmarksimmediately         | 1      |
      | marksimmediately            | 1      |
      | specificfeedbackimmediately | 1      |
      | generalfeedbackimmediately  | 1      |
      | rightanswerimmediately      | 1      |
      | overallfeedbackimmediately  | 1      |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |

  @javascript
  Scenario: Flag a quiz during and after quiz attempt
    Given I am on the "Quiz 1" "quiz activity" page logged in as student1
    And I press "Attempt quiz"
    When I click on "Flag question" "button" in the "First question" "question"
    Then I should see "Remove flag" in the "First question" "question"
    # Confirm question 1 is flagged in navigation
    And "Question 1 This page Flagged" "link" should exist
    # Answer questions
    And I click on "True" "radio" in the "First question" "question"
    And I press "Next page"
    And I click on "True" "radio" in the "Second question" "question"
    And I press "Next page"
    And I click on "True" "radio" in the "Third question" "question"
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    # Confirm only flagged question is flagged
    And I should see "Remove flag" in the "First question" "question"
    And I should see "Flag question" in the "Second question" "question"
    And I click on "Flagged" "button" in the "Second question" "question"
    And I should see "Remove flag" in the "Second question" "question"
    And I should see "Flag question" in the "Third question" "question"
    And I am on the "Quiz 1" "mod_quiz > Grades report" page logged in as teacher1
    And "Flagged" "icon" should exist in the "Student 1" "table_row"
    And I am on the "Quiz 1" "mod_quiz > Responses report" page
    And "Flagged" "icon" should exist in the "Student 1" "table_row"
    And I am on the "Quiz 1 > student1 > Attempt 1" "mod_quiz > Attempt review" page
    And I should see "Remove flag" in the "First question" "question"
    And I should see "Remove flag" in the "Second question" "question"
    And I should see "Flag question" in the "Third question" "question"
