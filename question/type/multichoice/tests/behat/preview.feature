@qtype @qtype_multichoice
Feature: Preview a Multiple choice question
  As a teacher
  In order to check my Multiple choice questions will work for students
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
      | questioncategory | qtype       | name             | template    |
      | Test questions   | multichoice | Multi-choice-001 | two_of_four |
      | Test questions   | multichoice | Multi-choice-002 | one_of_four |

  @javascript @_switch_window
  Scenario: Preview a Multiple choice question and submit a partially correct response.
    When I am on the "Multi-choice-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Two" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Two is even"
    And I should see "Mark 0.50 out of 1.00"
    And I should see "Parts, but only parts, of your response are correct."

  @javascript @_switch_window
  Scenario: Preview a Multiple choice question and submit multiple correct responses.
    When I am on the "Multi-choice-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Three" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Three is odd"
    And I should see "Mark 1.00 out of 1.00"
    And I should see "Well done!"
    And I should see "The odd numbers are One and Three."
    And I should see "The correct answers are: One, Three"

  @javascript @_switch_window
  Scenario: Preview a Multiple choice question and submit a single correct response.
    When I am on the "Multi-choice-002" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on "One" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "The oddest number is One."
    And I should see "Mark 1.00 out of 1.00"
    And I should see "Well done!"
    And I should see "The correct answer is: One"

  @javascript @_switch_window
  Scenario: Preview a multiple choice question (single response) and clear a previous selected option.
    When I am on the "Multi-choice-002" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I click on "One" "qtype_multichoice > Answer"
    Then I should see "Clear my choice"
    And I click on "Clear my choice" "text"
    And I should not see "Clear my choice"
