@core @core_question
Feature: A teacher can manage tags on questions in the question bank
  In order to organise my questions
  As a teacher
  I need to be able to tag them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
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
      | questioncategory | qtype | name                       | questiontext                  |
      | Test questions   | essay | Test question to be tagged | Write about whatever you want |
    And I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"

  @javascript
  Scenario: Manage tags on a question
    When I choose "Manage tags" action for "Test question to be tagged" in the question bank
    And I should see "Test question to be tagged" in the "Question tags" "dialogue"
    And I set the field "Tags" to "my-tag"
    And I click on "Save changes" "button" in the "Question tags" "dialogue"
    Then I should see "my-tag" in the "Test question to be tagged" "table_row"

  @javascript
  Scenario: Manage tags works even on questions of a type is no longer installed
    Given the following "questions" exist:
      | questioncategory | qtype       | name            | questiontext    |
      | Test questions   | missingtype | Broken question | Write something |
    And I reload the page
    When I choose "Manage tags" action for "Broken question" in the question bank
    And I set the field "Tags" to "my-tag"
    And I click on "Save changes" "button" in the "Question tags" "dialogue"
    Then I should see "my-tag" in the "Broken question" "table_row"
