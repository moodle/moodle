@mod @mod_quiz
Feature: The various checks that may happen when an attept is started
  As a student
  In order to start a quiz with confidence
  I need to be waned if there is a time limit, or various similar things

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
      | questioncategory | qtype       | name  | questiontext               |
      | Test questions   | truefalse   | TF1   | Text of the first question |

  @javascript
  Scenario: Start a quiz with no time limit
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    Then I should see "Text of the first question"
    And I should not see "v1" in the "Question 1" "question"

  @javascript
  Scenario: Start a quiz with time limit and password
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | timelimit | quizpassword |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 3600      | Frog         |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    Then I should see "To attempt this quiz you need to know the quiz password" in the "Start attempt" "dialogue"
    And I should see "Your attempt will have a time limit of 1 hour. When you " in the "Start attempt" "dialogue"
    And I set the field "Quiz password" to "Frog"
    And I click on "Start attempt" "button" in the "Start attempt" "dialogue"
    And I should see "Text of the first question"

  @javascript
  Scenario: Cancel starting a quiz with time limit and password
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | timelimit | quizpassword |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 3600      | Frog         |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I click on "Cancel" "button" in the "Start attempt" "dialogue"
    Then I should see "Quiz 1 description"
    And "Attempt quiz" "button" should be visible

  @javascript
  Scenario: Start a quiz with time limit and password, get the password wrong first time
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | timelimit | quizpassword |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 3600      | Frog         |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I set the field "Quiz password" to "Toad"
    And I click on "Start attempt" "button" in the "Start attempt" "dialogue"
    Then I should see "Quiz 1 description"
    And I should see "To attempt this quiz you need to know the quiz password"
    And I should see "Your attempt will have a time limit of 1 hour. When you "
    And I should see "The password entered was incorrect"
    And I set the field "Quiz password" to "Frog"
    And I press "Start attempt"
    And I should see "Text of the first question"

  @javascript
  Scenario: Start a quiz with time limit and password, get the password wrong first time then cancel
    Given the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | timelimit | quizpassword |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | 3600      | Frog         |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I set the field "Quiz password" to "Toad"
    And I click on "Start attempt" "button" in the "Start attempt" "dialogue"
    And I should see "Quiz 1 description"
    And I should see "To attempt this quiz you need to know the quiz password"
    And I should see "Your attempt will have a time limit of 1 hour. When you "
    And I should see "The password entered was incorrect"
    And I set the field "Quiz password" to "Frog"
    And I press "Cancel"
    Then I should see "Quiz 1 description"
    And "Attempt quiz" "button" should be visible
