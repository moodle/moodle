@qtype @qtype_numerical
Feature: Test editing a Numerical question
  As a teacher
  In order to be able to update my Numerical question
  I need to edit them

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
      | questioncategory | qtype     | name                  | template |
      | Test questions   | numerical | Numerical for editing | pi       |

  Scenario: Edit a Numerical question when using a custom decimal separator
    Given the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    When I am on the "Numerical for editing" "core_question > edit" page logged in as teacher
    And the field "id_answer_0" matches value "3#14"
    And I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    And I should see "You must supply a value here."
    And I set the following fields to these values:
      | Question name | Edited Numerical name |
    And I press "id_submitbutton"
    Then I should see "Edited Numerical name"
    And I choose "Edit question" action for "Edited Numerical name" in the question bank
    And I set the following fields to these values:
      | id_answer_1    | 3#141592 |
      | id_tolerance_1 | 0#005    |
      | id_answer_2    | 3.05     |
      | id_tolerance_2 | 0.005    |
      | id_answer_3    | 3,01     |
    And I press "id_submitbutton"
    And I should see "Edited Numerical name"

  Scenario: Edit a Numerical question with very small answer
    When I am on the "Numerical for editing" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | id_answer_0    | 0.00000123456789 |
      | id_tolerance_1 | 0.0000123456789  |
    And I press "id_submitbutton"
    And I choose "Edit question" action for "Numerical for editing" in the question bank
    Then the following fields match these values:
      | id_answer_0    | 0.00000123456789 |
      | id_tolerance_1 | 0.0000123456789  |
