@qtype @qtype_numerical
Feature: Preview a Numerical question
  As a teacher
  In order to check my Numerical questions will work for students
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
      | questioncategory | qtype     | name          | template |
      | Test questions   | numerical | Numerical-001 | pi       |
      | Test questions   | numerical | Numerical-002 | pi3tries |
    And the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |

  @javascript @_switch_window
  Scenario: Preview a Numerical question and submit a correct response.
    When I am on the "Numerical-001" "core_question > preview" page logged in as teacher
    And I should see "What is pi to two d.p.?"
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3.14"
    And I press "Check"
    Then I should see "Very good."
    And I should see "Mark 1#00 out of 1#00"
    And I press "Start again"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3,14"
    And I press "Check"
    And I should see "Please enter your answer without using the thousand separator (,)."
    And I press "Start again"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3#14"
    And I press "Check"
    And I should see "Very good."
    And I should see "Mark 1#00 out of 1#00"

  Scenario: Preview a Numerical question with optional units
    Given I am on the "Numerical-001" "core_question > edit" page logged in as teacher
    # Edit the existing numerical question, add in the optional units.
    And I set the following fields to these values:
      | Question name                      | Numerical Question (optional)              |
      | Question text                      | How many meter is 1m + 20cm + 50mm?        |
      | Default mark                       | 1                                          |
      | General feedback                   | The correct answer is 1.25m                |
      | id_answer_0                        | 1.25                                       |
      | id_tolerance_0                     | 0                                          |
      | id_fraction_0                      | 100%                                       |
      | id_answer_1                        | 125                                        |
      | id_tolerance_1                     | 0                                          |
      | id_fraction_1                      | 0%                                         |
      | id_unitrole                        | Units are optional.                        |
      | id_unitsleft                       | on the right, for example 1.00cm or 1.00km |
      | id_unit_0                          | m                                          |
    And I press "submitbutton"
    When I choose "Preview" action for "Numerical Question (optional)" in the question bank
    # Unit is optional, so the unit select box should not be exist.
    Then "Select one unit" "select" should not exist
