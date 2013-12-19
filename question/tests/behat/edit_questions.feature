@core @core_question
Feature: A teacher can edit questions in the question bank
  In order to refine questions contents
  As a teacher
  I need to edit questions

  @javascript @_switch_window
  Scenario: Edit a previously created question
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I follow "Course 1"
    And I add a "Essay" question filling the form with:
      | Question name | Test question name |
      | Question text | Write about whatever you want |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Question bank"
    When I click on "Edit" "link" in the "Test question name" table row
    And I fill the moodle form with:
      | Question name | Edited question name |
      | Question text | Write a lot about what you want |
    And I press "Save changes"
    Then I should see "Edited question name"
    And I should not see "Test question name"
    And I should see "Admin User" in the ".categoryquestionscontainer tbody .creatorname" "css_element"
    And I should see "Teacher 1" in the ".categoryquestionscontainer tbody .modifiername" "css_element"
    And I click on "Edit" "link" in the "Edited question name" table row
    And the "Question name" field should match "Edited question name" value
    And I press "Cancel"
    And I click on "Preview" "link" in the "Edited question name" table row
    And I switch to "questionpreview" window
    And I should see "Edited question name"
    And I should see "Write a lot about what you want"
    And I press "Close preview"
    And I switch to the main window
