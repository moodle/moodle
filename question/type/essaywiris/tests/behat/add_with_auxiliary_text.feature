@qtype @qtype_wq @qtype_essaywiris
Feature: A teacher can add and preview an auxiliar text field in a Science Essay question
  In order to allow their students to comment on their answers
  As a teacher
  I need to be able to add an auxiliary text field

  Background:
    Given the "wiris" filter is "on"
    Given the "mathjaxloader" filter is "disabled"
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  @javascript @_switch_window
  Scenario: A teacher adds an auxiliary text field to a science essay
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I choose the question type "Essay - science"
    And I set the following fields to these values:
    | Question name | Science essay                                         |
    | Question text | What are the steps to solve a second degree equation? |
    | Default mark  | 1                                                     |
    And I open Wiris Quizzes Studio
    And I click on "Input options" "text"
    And I click on "Display auxiliary text field" "text"
    And I go back in Wiris Quizzes Studio
    And I save Wiris Quizzes Studio
    And I press "id_submitbutton"
    And I am on the "Science essay" "core_question > preview" page
    Then I should see "Write an optional reasoning for your answer:"
    And I wait "3" seconds
