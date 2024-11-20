@qtype @qtype_essay
Feature: Preview Essay questions
  As a teacher
  In order to check my Essay questions will work for students
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
      | questioncategory | qtype | name      | template         |
      | Test questions   | essay | essay-001 | editor           |
      | Test questions   | essay | essay-002 | editorfilepicker |
      | Test questions   | essay | essay-003 | plain            |

  @javascript @_switch_window
  Scenario: Preview an Essay question that uses the HTML editor.
    When I am on the "essay-001" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I should see "Please write a story about a frog."

  @javascript @_switch_window
  Scenario: Preview an Essay question that uses the HTML editor with embedded files.
    When I am on the "essay-002" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I should see "Please write a story about a frog."
    And I should see "You can drag and drop files here to add them."

  @javascript @_switch_window
  Scenario: Preview an Essay question that uses a plain text area.
    When I am on the "essay-003" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I should see "Please write a story about a frog."
