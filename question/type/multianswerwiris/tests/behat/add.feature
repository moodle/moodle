@qtype @qtype_wq @qtype_multianswerwiris @qtype_multianswerwiris_add
Feature: Test creating a Multianswer Wiris (Cloze) question
  As a teacher
  In order to test my students
  I need to be able to create a Wiris Cloze question

  Background:
    Given the "wiris" filter is "on"
    Given the "mathjaxloader" filter is "disabled"
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  @javascript
  Scenario: Create a Cloze question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I choose the question type "Cloze - science"
    Then I set the following fields to these values:
      | Question name        | multianswer-wiris-001                    |
      | Question text        | {1:SHORTANSWER:=#r1} is the number one.  |
      | General feedback     | The capital of Germany is #r1.           |
    And I open Wiris Quizzes Studio
    And I click on "Random variables" "text"
    And I add the variable "r1" with value "1"
    And I go back in Wiris Quizzes Studio
    And I save Wiris Quizzes Studio
    And I press "id_submitbutton"
    Then I should see "multianswer-wiris-001" in the "categoryquestions" "table"

  @javascript
  Scenario: Create a broken Cloze question and correct it
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I choose the question type "Cloze - science"
    And I set the field "Question name" to "multianswer-wiris-002"
    And I open Wiris Quizzes Studio
    And I click on "Random variables" "text"
    And I add the variable "r1" with value "1"
    And I add the variable "r2" with value "5"
    And I go back in Wiris Quizzes Studio
    And I save Wiris Quizzes Studio
    And I set the field "Question text" to "Please select number one {1:MC:=#r1}"
    And I set the field "General feedback" to "You are the number one."
    When I press "id_submitbutton"
    Then I should see "This type of question requires at least 2 choices"
    And I set the following fields to these values:
      | Question text | Please select number one {1:MC:=#r1~#r2} |
    And I press "id_submitbutton"
    And I should see "multianswer-wiris-002" in the "categoryquestions" "table"

  Scenario: Try to create a Cloze question that has no answer
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I choose the question type "Cloze - science"
    And I set the following fields to these values:
      | Question name | multianswer-wiris-003         |
      | Question text | {1:SA:=  } is the number one. |
    And I press "id_submitbutton"
    And I should see "This type of question requires at least 1 answers"

  @javascript
  Scenario: A teacher creates true false random
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I choose the question type "Cloze - science"
    And I set the following fields to these values:
      | Question name | multianswer-wiris-004                                                                                    |
      | Question text | <p>Type -10: {:SA:=\#a}</p> <p>Type 5: {:SA:=5}</p> <p>Choose 5/57: {:MC:=\#b~1~2}</p> <p>Formula #b</p> |
    And I open Wiris Quizzes Studio
    And I click on "Random variables" "text"
    And I add the variable "a" with value "-10"
    And I add the variable "b" with value "15/171"
    And I go back in Wiris Quizzes Studio
    And I save Wiris Quizzes Studio
    And I press "id_submitbutton"
