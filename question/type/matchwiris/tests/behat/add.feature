@qtype @qtype_wq @qtype_matchwiris
Feature: Test creating a Matching Wiris question
  As a teacher
  In order to test my students
  I need to be able to create a Matching Wiris question

  Background:
    Given the "wiris" filter is "on"
    Given the "mathjaxloader" filter is "disabled"
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  @javascript
  Scenario: Create a Matching Wiris question with 3 subquestions
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I choose the question type "Matching - science"
    Then I open Wiris Quizzes Studio
    And I click on "Random variables" "text"
    And I add the variable "a" with value "1"
    And I add the variable "b" with value "2"
    And I add the variable "c" with value "3"
    And I go back in Wiris Quizzes Studio
    And I save Wiris Quizzes Studio
    Then I set the following fields to these values:
      | Question name                      | match-wiris-001                  |
      | Question text                      | Match the numbers.               |
      | General feedback                   | One=#a, Two=#b and Three=#c.     |
      | id_subquestions_0                  | One                              |
      | id_subanswers_0                    | #a                               |
      | id_subquestions_1                  | Two                              |
      | id_subanswers_1                    | #b                               |
      | id_subquestions_2                  | Three                            |
      | id_subanswers_2                    | #c                               |
      | For any correct response           | Your answer is correct           |
      | For any partially correct response | Your answer is partially correct |
      | For any incorrect response         | Your answer is incorrect         |
      | Hint 1                             | This is your first hint          |
      | Hint 2                             | This is your second hint         |
    And I press "id_submitbutton"
    Then I should see "match-wiris-001"
