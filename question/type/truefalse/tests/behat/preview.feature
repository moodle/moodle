@qtype @qtype_truefalse
Feature: Preview a Trtue/False question
  As a teacher
  In order to check my True/False questions will work for students
  I need to preview them

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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | template |
      | Test questions   | truefalse | true-false-001 | true     |

  @javascript @_switch_window
  Scenario: Preview a True/False question and submit a correct response.
    When I am on the "true-false-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on "True" "radio"
    And I press "Check"
    And I should see "This is the right answer."
    And I should see "The correct answer is 'True'."

  @javascript @_switch_window
  Scenario: Preview a True/False question and submit an incorrect response.
    When I am on the "true-false-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on "False" "radio"
    And I press "Check"
    And I should see "This is the wrong answer."
    And I should see "You should have selected true."
    And I should see "The correct answer is 'True'."
