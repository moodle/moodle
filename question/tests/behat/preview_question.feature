@core @core_question
Feature: A teacher can preview questions in the question bank
  In order to ensure the questions are properly created
  As a teacher
  I need to preview the questions

  @javascript
  Scenario: Preview a previously created question
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I add a "Numerical" question filling the form with:
      | Question name | Test question name |
      | Question text | How much is 1 + 1 |
      | answer[0] | 2 |
      | fraction[0] | 100% |
      | answer[1] | * |
      | fraction[1] | None |
    When I click on "Preview" "link" in the "Test question name" table row
    And I switch to "questionpreview" window
    And I fill the moodle form with:
      | Whether correct | Shown |
      | How questions behave | Deferred feedback |
    And I press "Start again with these options"
    Then I should see "Not yet answered"
    And I fill in "Answer:" with "2"
    And I press "Submit and finish"
    And the state of "How much is 1 + 1" question is shown as "Correct"
    And I press "Start again"
    And the state of "How much is 1 + 1" question is shown as "Not yet answered"
    And I fill in "Answer:" with "1"
    And I press "Submit and finish"
    And the state of "How much is 1 + 1" question is shown as "Incorrect"
    And I press "Start again"
    And I press "Fill in correct responses"
    And the "Answer:" field should match "2" value
    And I press "Close preview"
    And I switch to the main window
