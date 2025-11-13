@ou @ou_vle @qtype @qtype_oumultiresponse @_switch_window @javascript
Feature: Preview an OU multiple response question
  As a teacher
  In order to check my OU multiple response questions will work for students
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
      | questioncategory | qtype           | name                | template    |
      | Test questions   | oumultiresponse | oumultiresponse 001 | two_of_four |

  Scenario: Preview a question and submit a partially correct response.
    When I am on the "oumultiresponse 001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "id_saverestart"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Two" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Two is even"
    And I should see "Mark 0.50 out of 1.00"
    And I should see "Parts, but only parts, of your response are correct."

  Scenario: Preview a question and submit a correct response.
    When I am on the "oumultiresponse 001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "id_saverestart"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Three" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Three is odd"
    And I should see "Mark 1.00 out of 1.00"
    And I should see "Well done!"
    And I should see "The odd numbers are One and Three."
    And I should see "The correct answers are: One, Three"

  Scenario: Preview a question and submit a partially correct response and has partially correct feedback number.
    When I am on the "oumultiresponse 001" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | name                                                                | oumultiresponse 002                                  |
      | For any partially correct response                                  | Parts, but only parts, of your response are correct. |
      | For any incorrect response                                          | That is not right at all.                            |
      | id_shownumcorrect                                                   | 1                                                    |
    And I click on "#id_submitbutton" "css_element"
    And I am on the "oumultiresponse 002" "core_question > preview" page
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "id_saverestart"
    And I click on "One" "qtype_multichoice > Answer"
    And I click on "Two" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Two is even"
    And I should see "Mark 0.50 out of 1.00"
    And I should see "Parts, but only parts, of your response are correct."
    And I should see "You have correctly selected one option."
