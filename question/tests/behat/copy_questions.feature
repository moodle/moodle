@core @core_question
Feature: A teacher can duplicate questions in the question bank
  In order to reuse questions and modify duplicated questions
  As a teacher
  I need to duplicate questions

  @javascript
  Scenario: copy a previously created question
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "admin"
    And I follow "Course 1"
    And I add a "Essay" question filling the form with:
      | Question name | Test question to be copied    |
      | Question text | Write about whatever you want |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Question bank"
    When I click on "Duplicate" "link" in the "Test question to be copied" "table_row"
    And I set the following fields to these values:
      | Question name | Duplicated question name                |
      | Question text | Write a lot about duplicating questions |
    And I press "id_submitbutton"
    Then I should see "Duplicated question name"
    And I should see "Test question to be copied"
    And I should see "Teacher 1" in the ".categoryquestionscontainer tbody tr.r0 .creatorname" "css_element"
    And I should see "Admin User" in the ".categoryquestionscontainer tbody tr.r1 .creatorname" "css_element"
    And I click on "Duplicate" "link" in the "Duplicated question name" "table_row"
    And the field "Question name" matches value "Duplicated question name (copy)"
    And I press "Cancel"
    Then I should see "Duplicated question name"
    And I should see "Test question to be copied"
