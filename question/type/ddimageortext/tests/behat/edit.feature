@qtype @qtype_ddimageortext
Feature: Test editing a drag and drop onto image questions
  As a teacher
  In order to be able to update my drag and drop onto image questions
  I need to edit them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
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
      | questioncategory | qtype         | name            | template |
      | Test questions   | ddimageortext | Drag onto image | xsection |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript
  Scenario: Edit a drag and drop onto image question
    And I choose "Edit question" action for "Drag onto image" in the question bank
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"

  Scenario: Edit a drag and drop onto image question and verify penalty works as expected
    When I choose "Edit question" action for "Drag onto image" in the question bank
    Then the following fields match these values:
      | Question name                       | Drag onto image |
      | Penalty for each incorrect try      | 33.33333%       |
      | Penalty for each incorrect try      | 0.3333333       |

  Scenario: Edit a drag and drop onto image question and verify penalty works as expected with custom decimal separator
    When the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    And I choose "Edit question" action for "Drag onto image" in the question bank
    Then the following fields match these values:
      | Question name                       | Drag onto image |
      | Penalty for each incorrect try      | 33#33333%       |
      | Penalty for each incorrect try      | 0.3333333       |
