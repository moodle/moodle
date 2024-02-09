@mod @mod_quiz
Feature: Several attempts in a quiz
  As a student
  In order to demonstrate what I know
  I need to be able to attempt quizzes and sometimes take multiple attempts

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                |
      | student1  | Student   | One      | student1@example.com |
      | student2  | Student   | One      | student2@example.com |
      | teacher   | Teacher   | One      | teacher@example.com  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | teacher  | C1     | teacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name | questiontext    |
      | Test questions   | truefalse | TF1  | First question  |
      | Test questions   | truefalse | TF2  | Second question |
    And the following "activities" exist:
      | activity | name   | intro              | course | idnumber | preferredbehaviour | navmethod  |
      | quiz     | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  | free       |
    And quiz "Quiz 1" contains the following questions:
      | question | page | requireprevious |
      | TF1      | 1    | 1               |
      | TF2      | 2    | 1               |
    # Add some attempts
    And user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | True     |
      | 2    | False    |
    And user "student2" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | True     |
      | 2    | True     |
    # Add a second attempt by student1
    And user "student1" has attempted "Quiz 1" with responses:
      | slot | response |
      | 1    | False    |
      | 2    | False    |

  @javascript
  Scenario: The redo question buttons are visible after 2 attempts are preset for student1.
    Given I am on the "Quiz 1" "mod_quiz > View" page logged in as "student1"
    Then "Re-attempt quiz" "button" should exist
    And I should see "Finished" in the "Attempt 1" "list_item"
    And I should see "1.00/2.00" in the "Attempt 1" "list_item"
    And I should see "Finished" in the "Attempt 2" "list_item"
    And I should see "0.00/2.00" in the "Attempt 2" "list_item"
