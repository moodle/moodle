@mod @mod_quiz
Feature: Enable deferred or immediate feedback for quiz
  As a teacher
  I should be able to set how questions behave to deferred or immediate feedback

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                |
      | student1  | Student   | 1        | student1@example.com |
      | teacher1  | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name    | course | idnumber |
      | qbank    | Qbank 1 | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |

  @javascript
  Scenario: Attempt quiz with How questions behave set to Deferred Feedback
    Given the following "activity" exists:
      | activity                    | quiz             |
      | name                        | Quiz 1           |
      | course                      | C1               |
      | preferredbehaviour          | deferredfeedback |
      | attemptimmediately          | 1                |
      | correctnessimmediately      | 1                |
      | maxmarksimmediately         | 1                |
      | marksimmediately            | 1                |
      | specificfeedbackimmediately | 1                |
      | generalfeedbackimmediately  | 1                |
      | rightanswerimmediately      | 1                |
      | overallfeedbackimmediately  | 1                |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And I am on the "Quiz 1" "quiz activity" page logged in as student1
    When I press "Attempt quiz"
    # Confirm that check button does not exist when attempting quiz
    Then "Check Question 1" "button" should not exist
    And I set the field "False" to "1"
    And I press "Finish attempt ..."
    And I should not see "This is the wrong answer."
    And I should not see "You should have selected true."
    And I should not see "The correct answer is 'True'."
    And I press "Submit all and finish"
    # Confirm that quiz answer feedback only appears when attempt is submitted
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I should see "This is the wrong answer."
    And I should see "You should have selected true."
    And I should see "The correct answer is 'True'."

  Scenario: Attempt quiz with How questions behave set to Immediate Feedback
    Given the following "activity" exists:
      | activity                    | quiz             |
      | name                        | Quiz 1           |
      | course                      | C1               |
      | preferredbehaviour          | immediatefeedback |
      | correctnessduring           | 1                 |
      | marksduring                 | 1                 |
      | specificfeedbackduring      | 1                 |
      | generalfeedbackduring       | 1                 |
      | rightanswerduring           | 1                 |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And I am on the "Quiz 1" "quiz activity" page logged in as student1
    When I press "Attempt quiz"
    Then "Check Question 1" "button" should exist
    And I set the field "False" to "1"
    # Confirm you can check your answer immediately before submitting the attempt
    And I press "Check"
    And I should see "The correct answer is 'True'."
    And the "True" "field" should be disabled
    And the "False" "field" should be disabled
    And "Check Question 1" "button" should not exist
