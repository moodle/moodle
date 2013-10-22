@core @core_question
Feature: A teacher can delete questions in the question bank
  In order to remove unused questions from the question bank
  As a teacher
  I need to delete questions

  Background:
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
    And I add a "Essay" question filling the form with:
      | Question name | Test question name |
      | Question text | Write about whatever you want |
    And I follow "Course 1"

  @javascript
  Scenario: Delete a question not used in a quiz
    Given I follow "Question bank"
    And I click on "Delete" "link" in the "Test question name" "table_row"
    When I press "Continue"
    Then I should not see "Test question name"

  @javascript
  Scenario: Delete a question used in a quiz
    Given I turn editing mode on
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name | Test quiz |
    And I follow "Test quiz"
    And I follow "Edit quiz"
    And I follow "Show"
    And I click on "Add to quiz" "link" in the "Test question name" "table_row"
    And I follow "Course 1"
    And I follow "Question bank"
    And I click on "Delete" "link" in the "Test question name" "table_row"
    When I press "Continue"
    Then I should not see "Test question name"
    And I click on "Also show old questions" "checkbox"
    And I should see "Test question name"
    And I follow "Course 1"
    And I follow "Test quiz"
    And I click on "Preview quiz now" "button"
    And I should see "Write about whatever you want"