@core @core_question
Feature: A teacher can edit questions in the question bank
  In order to improve my questions
  As a teacher
  I need to be able to edit questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
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
    And I follow "Course 1"
    And I navigate to "Questions" node in "Course administration > Question bank"

  @javascript
  Scenario: Edit a previously created question
    When I click on "Edit" "link" in the "Test question to be edited" "table_row"
    And I set the following fields to these values:
      | Question name | Edited question name |
      | Question text | Write a lot about what you want |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
    And I should not see "Test question to be edited"
    And "Edited question name" row "Created by" column of "categoryquestions" table should contain "Admin User"
    And "Edited question name" row "Last modified by" column of "categoryquestions" table should contain "Teacher 1"

  @javascript
  Scenario: Editing a question can be cancelled
    When I click on "Edit" "link" in the "Test question to be edited" "table_row"
    And I set the field "Question name" to "Edited question name"
    And I press "Cancel"
    Then I should see "Test question to be edited"
    And "Test question to be edited" row "Created by" column of "categoryquestions" table should contain "Admin User"
    And "Test question to be edited" row "Last modified by" column of "categoryquestions" table should contain "Admin User"
