@qtype @qtype_wq @qtype_shortanswerwiris
Feature: Test creating a Short Answer Wiris question
  As a teacher
  In order to test my students
  I need to be able to create a Short Answer Wiris question

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
  Scenario: Create a Short Answer Wiris question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I choose the question type "Short answer - science"
    Then I should see "Adding a short answer - math & science question by WIRIS"
    Then I set the following fields to these values:
      | Question name | shortanswer-wiris-001                |
      | Question text | This is a Wiris shortanswer question |
    And I open Wiris Quizzes Studio
    And I wait "2" seconds
    And I type "42"
    And I save Wiris Quizzes Studio
    Then I set the following fields to these values:
      | id_fraction_0 | 100%                       |
      | id_feedback_0 | 42 is an OK good answer.   |
    And I press "id_submitbutton"
    Then I should see "shortanswer-wiris-001"
