@qtype @qtype_match
Feature: Preview a Matching question
  As a teacher
  In order to check my Matching questions will work for students
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
      | questioncategory | qtype | name         | template | shuffleanswers |
      | Test questions   | match | matching-001 | foursubq | 0              |

  @javascript @_switch_window
  Scenario: Preview a Matching question and submit a correct response.
    When I am on the "matching-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field "Answer 1" to "amphibian"
    And I set the field "Answer 2" to "mammal"
    And I set the field "Answer 3" to "amphibian"
    And I press "Check"
    Then I should see "Well done!"
    And I should see "General feedback."

  @javascript @_switch_window
  Scenario: Preview a Matching question and submit a partially correct response.
    When I am on the "matching-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field "Answer 1" to "amphibian"
    And I set the field "Answer 2" to "insect"
    And I set the field "Answer 3" to "amphibian"
    And I press "Check"
    Then I should see "Parts, but only parts, of your response are correct."
    And I should see "General feedback."

  @javascript @_switch_window
  Scenario: Preview a Matching question and submit an incorrect response.
    When I am on the "matching-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field "Answer 1" to "mammal"
    And I set the field "Answer 2" to "insect"
    And I set the field "Answer 3" to "insect"
    And I press "Check"
    Then I should see "That is not right at all."
    And I should see "General feedback."
