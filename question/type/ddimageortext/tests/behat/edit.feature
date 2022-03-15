@qtype @qtype_ddimageortext
Feature: Test editing a drag and drop onto image questions
  As a teacher
  In order to be able to update my drag and drop onto image questions
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
      | questioncategory | qtype         | name            | template |
      | Test questions   | ddimageortext | Drag onto image | xsection |

  @javascript
  Scenario: Edit a drag and drop onto image question
    When I am on the "Drag onto image" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
