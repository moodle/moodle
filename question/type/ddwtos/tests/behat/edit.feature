@qtype @qtype_ddwtos
Feature: Test editing a drag and drop into text questions
  As a teacher
  In order to be able to update my drag and drop into text questions
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
      | questioncategory | qtype  | name         | template |
      | Test questions   | ddwtos | Drag to text | fox      |

  @javascript
  Scenario: Edit a drag and drop into text question
    When I am on the "Drag to text" "core_question > edit" page logged in as teacher
    And I should see "Choice [[1]]"
    And I should see "Choice [[2]]"
    And I should see "Choice [[3]]"
    And I should see "Write the answers to be dragged into the gaps. You can include extra answers to increase difficulty." in the "Choices" "fieldset"
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"

  Scenario: Cannot update a drag and drop into text question to the unsolvable questions
    When I am on the "Drag to text" "core_question > edit" page logged in as teacher
    And I should see "Choice [[1]]"
    And I should see "Choice [[2]]"
    And I should see "Choice [[3]]"
    And I set the following fields to these values:
      | Question name | Edited question name                   |
      | Question text | Choice [[1]] Choice [[2]] Choice [[1]] |
    And I press "id_submitbutton"
    Then I should see "Choice [[1]] has been used more than once without being set to \"Unlimited\". Please recheck this question."
