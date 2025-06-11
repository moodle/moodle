@core @core_question
Feature: A teacher can delete questions in the question bank
  In order to remove unused questions from the question bank
  As a teacher
  I need to delete questions

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
      | questioncategory | qtype | name                        | questiontext                  |
      | Test questions   | essay | Test question to be deleted | Write about whatever you want |
    And I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"

  @javascript
  Scenario: A question not used anywhere can really be deleted
    When I choose "Delete" action for "Test question to be deleted" in the question bank
    And I press "Delete"
    And I apply question bank filter "Show hidden questions" with value "Yes"
    Then I should not see "Test question to be deleted"

  Scenario: Deleting a question can be cancelled
    When I choose "Delete" action for "Test question to be deleted" in the question bank
    And I press "Cancel"
    Then I should see "Test question to be deleted"

  @javascript
  Scenario: Delete a question used in a quiz
    Given the following "activity" exists:
      | course   | C1        |
      | activity | quiz      |
      | idnumber | Test quiz |
      | name     | Test quiz |
    And the following "question" exists:
      | questioncategory | Test questions                   |
      | qtype            | truefalse                        |
      | name             | Test used question to be deleted |
      | questiontext     | Write about whatever you want    |
    And quiz "Test quiz" contains the following questions:
      | question                         | page | requireprevious |
      | Test used question to be deleted | 1    | 0               |
    When I am on the "Course 1" "core_question > course question bank" page
    And I choose "Delete" action for "Test used question to be deleted" in the question bank
    And I should see "This will delete the following question and all its versions:"
    And I should see "* Denotes questions which can't be deleted because they are in use. Instead, they will be hidden in the question bank unless you set 'Show hidden questions' to 'Yes'."
    And I press "Delete"
    Then I should not see "Test used question to be deleted"
    And I apply question bank filter "Show hidden questions" with value "Yes"
    And I should see "Test used question to be deleted"
    And I am on the "Test quiz" "quiz activity" page
    And I click on "Preview quiz" "button"
    And I should see "Write about whatever you want"

  @javascript
  Scenario: A question can be deleted even if that question type is no longer installed
    Given the following "questions" exist:
      | questioncategory | qtype       | name            | questiontext    |
      | Test questions   | missingtype | Broken question | Write something |
    And I reload the page
    When I choose "Delete" action for "Broken question" in the question bank
    And I press "Delete"
    And I apply question bank filter "Show hidden questions" with value "Yes"
    Then I should not see "Broken question"

  @javascript
  Scenario: Delete question has multiple versions in question bank page
    Given I am on the "Course 1" "core_question > course question bank" page logged in as "teacher1"
    When the following "core_question > updated questions" exist:
      | questioncategory | question                    | questiontext                          |
      | Test questions   | Test question to be deleted | Test question to be deleted version 2 |
    And I choose "Delete" action for "Test question to be deleted" in the question bank
    And I should see "This will delete the following question and all its versions:"
    And I should not see "* Denotes questions which can't be deleted because they are in use. Instead, they will be hidden in the question bank unless you set 'Show hidden questions' to 'Yes'."
    And I press "Delete"
    Then I should not see "Test question to be deleted"
    And I should not see "Test question to be deleted version2"
