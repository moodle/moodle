@core @core_question @core_customfield @qbank_customfields @javascript
Feature: A teacher can edit question with custom fields
  In order to improve my questions
  As a teacher
  I need to be able to edit questions and add extra metadata via custom fields

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher   | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "custom field categories" exist:
      | name              | component          | area     | itemid |
      | Category for test | qbank_customfields | question | 0      |
    And the following "custom fields" exist:
      | name    | category          | type | shortname |
      | Field 1 | Category for test | text | f1        |
    And the following "activity" exists:
      | activity | quiz                  |
      | course   | C1                    |
      | idnumber | 00001                 |
      | name     | Test quiz name        |
      | intro    | Test quiz description |
      | section  | 1                     |
      | grade    | 10                    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "True/False" question to the "Test quiz name" quiz with:
      | Question name                      | First question                          |
      | Question text                      | Answer the first question               |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | False                                   |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I am on the "Test quiz name" "quiz activity" page
    And I navigate to "Question bank" in current page administration

  Scenario: Edit a previously created question and see the custom field in the overview table and in the question preview.
    When I choose "Edit question" action for "First question" in the question bank
    And I should see "Category for test"
    And I click on "Expand all" "link"
    And I should see "Field 1"
    And I set the following fields to these values:
      | Field 1 | custom field text |
    And I press "id_submitbutton"
    And I should see "First question"
    And I should see "custom field text"
    And I choose "Preview" action for "First question" in the question bank
    And I should see "Field 1"
    Then I should see "custom field text"

  Scenario: Preview a previously created question with custom fields set with empty values
    When I choose "Preview" action for "First question" in the question bank
    And I should see "Field 1"
    Then I should not see "custom field text"
