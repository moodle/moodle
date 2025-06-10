@qtype @qtype_wq @qtype_multichoicewiris
Feature: A student can answer a Wiris Multi Choice question type
  In order to answer the question
  As a student
  I need to answer the multi choice wiris question

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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Default for C1 |
    And the following "questions" exist:
      | questioncategory | qtype            | name               | template             |
      | Default for C1   | multichoicewiris | Multi choice wiris | four_of_five_science |

  @javascript
  Scenario: A student executes a wiris multichoice question type
    When I am on the "Multi choice wiris" "core_question > preview" page logged in as teacher
    Then Wirisformula should exist
    When I click on "1. " "text"
    And I press "Submit and finish"
    Then Feedback should exist
    And Generalfeedback should exist
