@editor @qtype @qtype_essay @javascript
Feature: Set editor values when the editor is not in a form
    As an automated tester
    In order to use a non-form editor
    I need to set values

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
      | questioncategory | qtype | name  | template         |
      | Test questions   | essay | essay | editorfilepicker |

  Scenario: Preview an Essay question that uses the HTML editor with embedded files.
    When I am on the "essay" "core_question > preview" page logged in as teacher
    And I expand all fieldsets
    And I set the following fields to these values:
      | Answer text Question 1 | <p>The <b>cat</b> sat on the mat. Then it ate a <b>frog</b>.</p> |
    And I press "Submit and finish"
