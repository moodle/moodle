@mod @mod_quiz
Feature: Set a quiz with certainty-based marking
  As a teacher
  In order to set a a quiz with certainty-based marking
  I should set question behaviour to "Immediate feedback with CBM"

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name    | course | idnumber |
      | quiz     | Quiz 1  | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
      | Test questions   | truefalse   | TF3   | Third question  |
    And quiz "Quiz 1" contains the following questions:
      | question | page |
      | TF1      | 1    |
      | TF2      | 2    |
      | TF3      | 3    |

  @javascript
  Scenario: Teacher can set a quiz with certainty-based marking
    Given I am on the "Quiz 1" "quiz activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | preferredbehaviour | immediatecbm |
    And I press "Save and return to course"
    And I am on the "Quiz 1" "quiz activity" page logged in as student1
    When I press "Attempt quiz"
    # Press "Check" without selecting a certainty
    And I press "Check"
    # Confirm that "Please select a certainty." is displayed when "Check" is pressed
    Then I should see "Please select a certainty."
    And I click on "True" "radio" in the "First question" "question"
    And I click on "C=1 (Unsure: <67%)" "radio" in the "First question" "question"
    And I press "Next page"
    And I click on "False" "radio" in the "Second question" "question"
    And I click on "C=2 (Mid: >67%)" "radio" in the "Second question" "question"
    And I press "Next page"
    And I click on "True" "radio" in the "Third question" "question"
    And I click on "C=3 (Quite sure: >80%)" "radio" in the "Third question" "question"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    # As student1, confirm the results of own attempt
    And the following should exist in the "quizreviewsummary" table:
      | -1-              | -2-                                                                                                                 |
      | Marks            | 2.00/3.00                                                                                                           |
      | Grade            | 66.67 out of 100.00                                                                                                 |
      | Average CBM mark | 0.67                                                                                                                |
      | Accuracy         | 66.7%                                                                                                               |
      | CBM bonus        | 0.0%                                                                                                                |
      | Accuracy + Bonus | 66.7%                                                                                                               |
      | C=3              | Responses: 1. Accuracy: 100%. (Optimal range 80% to 100%). You were OK using this certainty level.                  |
      | C=2              | Responses: 1. Accuracy: 0%. (Optimal range 67% to 80%). You were a bit over-confident using this certainty level.   |
      | C=1              | Responses: 1. Accuracy: 100%. (Optimal range 0% to 67%). You were a bit under-confident using this certainty level. |
    And I should see "CBM mark 1.00" in the "First question" "question"
    And I should see "CBM mark -2.00" in the "Second question" "question"
    And I should see "CBM mark 3.00" in the "Third question" "question"
    # As teacher, confirm same quiz contents
    And I am on the "Quiz 1" "quiz activity" page logged in as teacher1
    And I press "Preview quiz"
    And I should see "C=1 (Unsure: <67%)"
    And I should see "C=2 (Mid: >67%)"
    And I should see "C=3 (Quite sure: >80%)"
    And I am on the "Quiz 1" "mod_quiz > Grades report" page
    And I click on "Review attempt" "link" in the "Student One" "table_row"
    # As teacher, confirm that the attempt result is same with student1 view
    And the following should exist in the "quizreviewsummary" table:
      | -1-              | -2-                                                                                                                 |
      | Marks            | 2.00/3.00                                                                                                           |
      | Grade            | 66.67 out of 100.00                                                                                                 |
      | Average CBM mark | 0.67                                                                                                                |
      | Accuracy         | 66.7%                                                                                                               |
      | CBM bonus        | 0.0%                                                                                                                |
      | Accuracy + Bonus | 66.7%                                                                                                               |
      | C=3              | Responses: 1. Accuracy: 100%. (Optimal range 80% to 100%). You were OK using this certainty level.                  |
      | C=2              | Responses: 1. Accuracy: 0%. (Optimal range 67% to 80%). You were a bit over-confident using this certainty level.   |
      | C=1              | Responses: 1. Accuracy: 100%. (Optimal range 0% to 67%). You were a bit under-confident using this certainty level. |
    And I should see "CBM mark 1.00" in the "First question" "question"
    And I should see "CBM mark -2.00" in the "Second question" "question"
    And I should see "CBM mark 3.00" in the "Third question" "question"
