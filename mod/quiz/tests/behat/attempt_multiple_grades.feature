@mod @mod_quiz
Feature: Attempt a quiz with multiple grades
  As a student
  In order to demonstrate multiple skills at once
  I need to be able to attempt quizzes with multiple grades setup

  Background:
    Given the following "users" exist:
      | username |
      | student  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role    |
      | student | C1     | student |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | course |
      | quiz       | Quiz 1 | C1     |

  @javascript
  Scenario: Navigation to, and display of, grading setup
    Given the following "questions" exist:
      | questioncategory | qtype     | name      | questiontext       |
      | Test questions   | truefalse | Reading   | Can you read this? |
      | Test questions   | truefalse | Listening | Can you hear this? |
    And the following "mod_quiz > grade items" exist:
      | quiz   | name                |
      | Quiz 1 | Grade for reading   |
      | Quiz 1 | Grade for listening |
      | Quiz 1 | Unused grade item   |
    And quiz "Quiz 1" contains the following questions:
      | question  | page | grade item           |
      | Reading   | 1    | Grade for reading    |
      | Listening | 1    | Grade for listening  |

    When I am on the "Quiz 1" "quiz activity" page logged in as "student"
    And I click on "Attempt quiz" "button"
    And I set the field "True" in the "Can you read this?" "question" to "1"
    And I set the field "False" in the "Can you hear this?" "question" to "1"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"

    Then I should see "1.00 out of 1.00" in the "Grade for reading" "table_row"
    And I should see "0.00 out of 1.00" in the "Grade for listening" "table_row"
    And I should not see "Unused grade item"
    And I should see "1.00/2.00" in the "Marks" "table_row"
    # Funny order because 'Grade' also appears in other rows.
    And I should see "Grade" in the "50.00 out of 100.00" "table_row"
    And I follow "Finish review"
    And I should not see "Unused grade item"
    And I should see "1.00/2.00" in the "Marks" "table_row"
    And I should see "Grade" in the "50.00 out of 100.00" "table_row"
