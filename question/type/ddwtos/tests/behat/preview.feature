@qtype @qtype_ddwtos @_switch_window
Feature: Preview a drag-drop into text question
  As a teacher
  In order to check my drag-drop into text questions will work for students
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
      | questioncategory | qtype  | name                  | template |
      | Test questions   | ddwtos | Drag to text          | fox      |
      | Test questions   | ddwtos | Drag to text infinite | infinite |

  @javascript @_bug_phantomjs
  Scenario: Preview a question using the mouse.
    When I am on the "Drag to text" "core_question > preview" page logged in as teacher
    And I should see "quick" in the home area of drag and drop into text question
    And I should see "slow" in the home area of drag and drop into text question
    And I drag "quick" to space "1" in the drag and drop into text question
    And I drag "fox" to space "2" in the drag and drop into text question
    And I drag "assiduous" to space "3" in the drag and drop into text question
    And I drag "slow" to placed drag "1" in the drag and drop into text question
    And I should see "quick" in the home area of drag and drop into text question
    And I drag "quick" to placed drag "1" in the drag and drop into text question
    And I should see "slow" in the home area of drag and drop into text question
    And I press "Submit and finish"
    Then the state of "The" question is shown as "Partially correct"
    And I should see "Mark 0.67 out of 1.00"

  @javascript
  Scenario: Preview a question using the keyboard & submit incomplete.
    When I am on the "Drag to text" "core_question > preview" page logged in as teacher
    And I type " " into space "1" in the drag and drop onto image question
    And I type "   " into space "2" in the drag and drop onto image question
    And I type " " into space "3" in the drag and drop onto image question
    And I press "Save"
    Then the state of "The" question is shown as "Incomplete answer"
    And I should see "Please put an answer in each box."

  @javascript
  Scenario: Preview a question using the keyboard.
    When I am on the "Drag to text" "core_question > preview" page logged in as teacher
    And I type "  " into space "1" in the drag and drop onto image question
    And I type "  " into space "2" in the drag and drop onto image question
    And I type "  " into space "3" in the drag and drop onto image question
    And I press "Submit and finish"
    Then the state of "The" question is shown as "Incorrect"
    And I should see "Mark 0.00 out of 1.00"

  @javascript
  Scenario: Preview a question that uses strange group numbers using the keyboard.
    Given the following "questions" exist:
      | questioncategory | qtype  | name         | template  |
      | Test questions   | ddwtos | Funny groups | oddgroups |
    When I am on the "Funny groups" "core_question > preview" page logged in as teacher
    And I type " " into space "1" in the drag and drop onto image question
    And I type " " into space "2" in the drag and drop onto image question
    And I type " " into space "3" in the drag and drop onto image question
    And I press "Submit and finish"
    Then the state of "The" question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"

  @javascript
  Scenario: Preview a infinite question.
    When I am on the "Drag to text infinite" "core_question > preview" page logged in as teacher
    And I press "Fill in correct responses"
    Then I should see "Option1" in the home area of drag and drop into text question
    And I should see "Option2" in the home area of drag and drop into text question
    And I should see "Option3" in the home area of drag and drop into text question
