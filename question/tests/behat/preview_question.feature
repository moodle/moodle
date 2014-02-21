@core @core_question @_switch_window
Feature: A teacher can preview questions in the question bank
  In order to ensure the questions are properly created
  As a teacher
  I need to preview the questions

  @javascript
  Scenario: Preview a previously created question
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I add a "Numerical" question filling the form with:
      | Question name | Test question to be previewed |
      | Question text | How much is 1 + 1 |
      | answer[0] | 2 |
      | fraction[0] | 100% |
      | answer[1] | * |
      | fraction[1] | None |
    When I click on "Preview" "link" in the "Test question to be previewed" "table_row"
    And I switch to "questionpreview" window
    And I set the following fields to these values:
      | Whether correct | Shown |
      | How questions behave | Deferred feedback |
    And I press "Start again with these options"
    Then I should see "Not yet answered"
    And I set the field "Answer:" to "2"
    And I press "Submit and finish"
    And the state of "How much is 1 + 1" question is shown as "Correct"
    And I press "Start again"
    And the state of "How much is 1 + 1" question is shown as "Not yet answered"
    And I set the field "Answer:" to "1"
    And I press "Submit and finish"
    And the state of "How much is 1 + 1" question is shown as "Incorrect"
    And I press "Start again"
    And I press "Fill in correct responses"
    And the field "Answer:" matches value "2"
    And I press "Close preview"
    And I switch to the main window
