@core @core_question
Feature: A teacher can duplicate questions in the question bank
  In order to efficiently expand my question bank
  As a teacher
  I need to be able to duplicate existing questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                       | questiontext                  |
      | Test questions   | essay | Test question to be copied | Write about whatever you want |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank > Questions" in current page administration

  Scenario: Duplicate a previously created question
    When I choose "Duplicate" action for "Test question to be copied" in the question bank
    And I press "id_submitbutton"
    Then I should see "Test question to be copied (copy)"
    And I should see "Test question to be copied"
    And "Test question to be copied (copy)" row "Created by" column of "categoryquestions" table should contain "Teacher 1"
    And "Test question to be copied (copy)" row "Last modified by" column of "categoryquestions" table should contain "Teacher 1"

  Scenario: Duplicating a question can be cancelled
    When I choose "Duplicate" action for "Test question to be copied" in the question bank
    And I set the field "Question name" to "Edited question name"
    And I press "Cancel"
    Then I should see "Test question to be copied"
    And I should not see "Edited question name"
    And I should not see "Test question to be copied (copy)"

  Scenario: Duplicating a question with an idnumber increments it
    Given the following "questions" exist:
      | questioncategory | qtype | name                   | questiontext                  | idnumber |
      | Test questions   | essay | Question with idnumber | Write about whatever you want | id101    |
    And I reload the page
    When I choose "Duplicate" action for "Question with idnumber" in the question bank
    And I press "id_submitbutton"
    Then I should see "Question with idnumber (copy)"
    Then I should see "id102" in the "Question with idnumber (copy)" "table_row"
