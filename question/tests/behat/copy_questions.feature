@core @core_question
Feature: A teacher can duplicate questions in the question bank
  In order to reuse questions
  As a teacher
  I need to duplicate questions and make small changes

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                       | questiontext                  | idnumber |
      | Test questions   | essay | Test question to be copied | Write about whatever you want | qid      |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank > Questions" in current page administration

  @javascript
  Scenario: Duplicating a previously created question
    When I choose "Duplicate" action for "Test question to be copied" in the question bank
    And I set the following fields to these values:
      | Question name | Duplicated question name                |
      | Question text | Write a lot about duplicating questions |
    And I press "id_submitbutton"
    Then I should see "Duplicated question name"
    And I should see "Test question to be copied"
    And "Duplicated question name" row "Last modified by" column of "categoryquestions" table should contain "Teacher 1"
    And "Test question to be copied ID number qid" row "Created by" column of "categoryquestions" table should contain "Admin User"

  @javascript
  Scenario: Duplicated questions automatically get a new name suggested
    When I choose "Duplicate" action for "Test question to be copied" in the question bank
    Then the field "Question name" matches value "Test question to be copied (copy)"

  @javascript
  Scenario: The duplicate operation can be cancelled
    When I choose "Duplicate" action for "Test question to be copied" in the question bank
    And I press "Cancel"
    Then I should see "Test question to be copied"
    And the field "Select a category" matches value "&nbsp;&nbsp;&nbsp;Test questions (1)"
