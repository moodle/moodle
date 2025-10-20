@mod @mod_quiz
Feature: Attempt quiz with password and time limit
  In order to attempt a quiz with password and time limit
  As a teacher
  I need to be able to set password and time limit to quiz

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name   | course | quizpassword | browsersecurity | timelimit |
      | quiz     | Quiz 1 | C1     | Passw0rd     | securewindow    | 60        |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | Quiz 1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext    |
      | Test questions   | truefalse | TF1  | First question  |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |

  @javascript
  Scenario: Attempt password restricted quiz
    Given I am on the "Quiz 1" "quiz activity" page logged in as student1
    When I press "Attempt quiz"
    # TODO: remove. This is an extra step due to the bug reported in MDL-81377.
    And I press "Start attempt"
    # This confirms that quiz is attempted in a separate window.
    And I switch to a second window
    # Confirm that the time limit warning is displayed.
    Then I should see "Your attempt will have a time limit of 1 min. When you start, the timer will begin to count down and cannot be paused. You must finish your attempt before it expires. Are you sure you wish to start now?"
    And I set the field "Quiz password" to "Passw0rd"
    And I press "Start attempt"
    # Confirm that quiz timer is displayed when attempting the quiz.
    And I should see "Time left"

  @javascript
  Scenario: Attempt quiz after entering incorrect password
    Given I am on the "Quiz 1" "quiz activity" page logged in as student1
    When I press "Attempt quiz"
    # TODO: remove. This is an extra step due to the bug reported in MDL-81377.
    And I press "Start attempt"
    # This confirms that quiz is attempted in a separate window.
    And I switch to a second window
    # Incorrect password is entered.
    And I set the field "Quiz password" to "password"
    And I press "Start attempt"
    # Confirm that error is displayed.
    Then I should see "The password entered was incorrect"
    # Confirm that the time limit warning is displayed.
    And I should see "Your attempt will have a time limit of 1 min. When you start, the timer will begin to count down and cannot be paused. You must finish your attempt before it expires. Are you sure you wish to start now?"
    # Enter the correct password to attempt quiz.
    And I set the field "Quiz password" to "Passw0rd"
    And I press "Start attempt"
    # Confirm that quiz attempt proceeded.
    And I should not see "The password entered was incorrect"
    And I should not see "Your attempt will have a time limit of 1 min. When you start, the timer will begin to count down and cannot be paused. You must finish your attempt before it expires. Are you sure you wish to start now?"
    And I should see "Time left"
