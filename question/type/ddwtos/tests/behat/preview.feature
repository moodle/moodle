@ou @ou_vle @qtype @qtype_ddwtos
Feature: Preview a drag-drop into text question
  As a teacher
  In order to check my drag-drop into text questions will work for students
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
      | questioncategory | qtype  | name         | template |
      | Test questions   | ddwtos | Drag to text | fox      |
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

  @javascript
  Scenario: Preview a question using the mouse.
    When I click on "Preview" "link" in the "Drag to text" "table_row"
    And I switch to "questionpreview" window
    And I drag "quick" to space "1" in the drag and drop into text question
    And I drag "fox" to space "2" in the drag and drop into text question
    And I drag "assiduous" to space "3" in the drag and drop into text question
    And I press "Submit and finish"
    Then the state of "The" question is shown as "Partially correct"
    And I should see "Mark 0.67 out of 1.00"

  @javascript
  Scenario: Preview a question using the keyboard & submit incomplete.
    When I click on "Preview" "link" in the "Drag to text" "table_row"
    And I switch to "questionpreview" window
    And I type " " into space "1" in the drag and drop onto image question
    And I type "   " into space "2" in the drag and drop onto image question
    And I type " " into space "3" in the drag and drop onto image question
    And I press "Save"
    Then the state of "The" question is shown as "Incomplete answer"
    And I should see "Please put an answer in each box."

  @javascript
  Scenario: Preview a question using the keyboard.
    When I click on "Preview" "link" in the "Drag to text" "table_row"
    And I switch to "questionpreview" window
    And I type "  " into space "1" in the drag and drop onto image question
    And I type "  " into space "2" in the drag and drop onto image question
    And I type "  " into space "3" in the drag and drop onto image question
    And I press "Submit and finish"
    Then the state of "The" question is shown as "Incorrect"
    And I should see "Mark 0.00 out of 1.00"
