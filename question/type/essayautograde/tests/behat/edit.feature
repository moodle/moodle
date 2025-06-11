@qtype @qtype_essayautograde
Feature: Test editing an Essay autograde question
  As a teacher
  In order to be able to update my Essay autograde question
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration
    And I add a "Essay (auto-grade)" question filling the form with:
      | Question name            | essayautograde-001                                      |
      | Question text            | Please write a story about a frog.                      |
      | General feedback         | I hope your story had a beginning, a middle and an end. |
      | Response format          | HTML editor                                             |
    And I add a "Essay (auto-grade)" question filling the form with:
      | Question name            | essayautograde-002                                      |
      | Question text            | Please write a story about a frog.                      |
      | General feedback         | I hope your story had a beginning, a middle and an end. |
      | Response format          | HTML editor with file picker                            |
    And I add a "Essay (auto-grade)" question filling the form with:
      | Question name            | essayautograde-003                                      |
      | Question text            | Please write a story about a frog.                      |
      | General feedback         | I hope your story had a beginning, a middle and an end. |
      | Response format          | Plain text                                              |

  Scenario: Edit an Essay autograde question
    When I click on "Edit" "link" in the "essayautograde-001" "table_row"
    And I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    Then I should see "You must supply a value here."
    When I set the following fields to these values:
      | Question name   | Edited essayautograde-001 name |
      | Response format | No online text                 |
    And I press "id_submitbutton"
    Then I should see "When \"No online text\" is selected, or responses are optional, you must allow at least one attachment."
    When I set the following fields to these values:
      | Response format | Plain text |
    And I press "id_submitbutton"
    Then I should see "Edited essayautograde-001 name"
