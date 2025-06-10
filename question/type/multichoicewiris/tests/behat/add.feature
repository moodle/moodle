@qtype @qtype_wq @qtype_multichoicewiris
Feature: Test creating a Multiple choice wiris question without template
  As a teacher
  In order to test my students
  I need to be able to create a Multiple choice question using variables

  Background:
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
  Scenario: Create a Multiple choice wiris question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I choose the question type "Multiple choice - science"
    Then I set the following fields to these values:
      | Question name | multichoiceanswer-wiris-001             |
      | Question text | This is a multichoice wiris question    |
    And I open Wiris Quizzes Studio
    And I click on "Random variables" "text"
    And I add the variable "a" with value "10"
    And I add the variable "b" with value "-15/171"
    And I go back in Wiris Quizzes Studio
    And I save Wiris Quizzes Studio
    Then I set the following fields to these values:
      | Choice 1      | #a   |
      | id_fraction_0 | 100% |
      | Choice 2      | #b   |
      | id_fraction_1 | None |
    And I press "id_submitbutton"
    Then I should see "multichoiceanswer-wiris-001"
