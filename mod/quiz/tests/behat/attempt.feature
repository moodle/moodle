@mod @mod_quiz
Feature: Attemp a quiz where some questions require that the previous question has been answered.
  As a student
  In order to demonstrate what I know
  I need to be able to attempt quizzes

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | student  | Student   | One      | student@moodle.com |
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
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |

  @javascript
  Scenario: A student can attempt a quiz
    When I log in as "student"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "False" "radio" in the "Second question" "question"
    And I press "Next"
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    Then I should see "25.00 out of 100.00"
