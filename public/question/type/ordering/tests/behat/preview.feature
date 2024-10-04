@qtype @qtype_ordering
Feature: Preview an Ordering question
  As a teacher
  In order to check my Ordering questions will work for students
  I need to preview them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype    | name         | template | layouttype |
      | Test questions   | ordering | ordering-001 | moodle   | 0          |

  @javascript
  Scenario: Preview an Ordering question and submit a correct response.
    When I am on the "ordering-001" "core_question > preview" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "id_saverestart"
    And I drag "Modular" to space "1" in the ordering question
    And I drag "Object" to space "2" in the ordering question
    And I drag "Oriented" to space "3" in the ordering question
    And I drag "Dynamic" to space "4" in the ordering question
    And I drag "Learning" to space "5" in the ordering question
    And I drag "Environment" to space "6" in the ordering question
    And I press "Submit and finish"
    Then I should see "Well done!"
    And I should see "Mark 1.00 out of 1.00"

  @javascript
  Scenario: Preview an Ordering question with show number of correct option.
    When I am on the "ordering-001" "core_question > preview" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "id_saverestart"
    And I drag "Modular" to space "1" in the ordering question
    And I drag "Object" to space "6" in the ordering question
    And I drag "Oriented" to space "4" in the ordering question
    And I drag "Dynamic" to space "3" in the ordering question
    And I drag "Learning" to space "5" in the ordering question
    And I drag "Environment" to space "2" in the ordering question
    And I press "Submit and finish"
    And I should see "Correct items: 1"
    And I should see "Partially correct items: 5"

  @javascript
  Scenario: Preview an Ordering question with no show number of correct option.
    When I am on the "ordering-001" "core_question > edit" page logged in as teacher1
    And I set the following fields to these values:
      | id_shownumcorrect | 0                    |
      | Question name     | Renamed ordering-001 |
    And I press "id_submitbutton"
    And I am on the "Renamed ordering-001" "core_question > preview" page
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "id_saverestart"
    And I drag "Modular" to space "1" in the ordering question
    And I drag "Environment" to space "2" in the ordering question
    And I drag "Dynamic" to space "3" in the ordering question
    And I drag "Oriented" to space "4" in the ordering question
    And I drag "Learning" to space "5" in the ordering question
    And I drag "Object" to space "6" in the ordering question
    And I press "Submit and finish"
    And I should not see "You have 1 item correct."
    And I should not see "You have 5 items partially correct."
