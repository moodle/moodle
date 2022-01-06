@core @core_question
Feature: A teacher can edit questions in the question bank
  In order to improve my questions
  As a teacher
  I need to be able to edit questions

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
      | Test questions   | essay | Test question to be edited | Write about whatever you want |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank > Questions" in current page administration

  Scenario: Edit a previously created question
    When I choose "Edit question" action for "Test question to be edited" in the question bank
    And I set the following fields to these values:
      | Question name | Edited question name |
      | Question text | Write a lot about what you want |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
    And I should not see "Test question to be edited"
    And "Edited question name" row "Created by" column of "categoryquestions" table should contain "Admin User"
    And "Edited question name" row "Last modified by" column of "categoryquestions" table should contain "Teacher 1"

  Scenario: Editing a question can be cancelled
    When I choose "Edit question" action for "Test question to be edited" in the question bank
    And I set the field "Question name" to "Edited question name"
    And I press "Cancel"
    Then I should see "Test question to be edited"
    And "Test question to be edited" row "Created by" column of "categoryquestions" table should contain "Admin User"
    And "Test question to be edited" row "Last modified by" column of "categoryquestions" table should contain "Admin User"

  Scenario: A question can have its idnumber removed
    Given the following "questions" exist:
      | questioncategory | qtype | name                   | idnumber |
      | Test questions   | essay | Question with idnumber | frog     |
    And I reload the page
    Then I should see "frog" in the "Question with idnumber" "table_row"
    When I choose "Edit question" action for "Question with idnumber" in the question bank
    And I set the field "ID number" to ""
    And I press "id_submitbutton"
    Then I should not see "frog" in the "Question with idnumber" "table_row"

  Scenario: If the question type is no longer installed, then most edit actions are not present
    Given the following "questions" exist:
      | questioncategory | qtype       | name            | questiontext    |
      | Test questions   | missingtype | Broken question | Write something |
    When I reload the page
    Then "Edit question" "link" should not exist in the "Broken question" "table_row"
    And "Duplicate" "link" should not exist in the "Broken question" "table_row"
    And "Manage tags" "link" should exist in the "Broken question" "table_row"
    And "Preview" "link" should not exist in the "Broken question" "table_row"
    And "Delete" "link" should exist in the "Broken question" "table_row"
    And "Export as Moodle XML" "link" should not exist in the "Broken question" "table_row"
