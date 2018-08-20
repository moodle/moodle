@core @core_question
Feature: A teacher can delete questions in the question bank
  In order to remove unused questions from the question bank
  As a teacher
  I need to delete questions

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
      | questioncategory | qtype | name                        | questiontext                  |
      | Test questions   | essay | Test question to be deleted | Write about whatever you want |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank > Questions" in current page administration

  @javascript
  Scenario: A question not used anywhere can really be deleted
    When I click on "Delete" "link" in the "Test question to be deleted" "table_row"
    And I press "Delete"
    And I click on "Also show old questions" "checkbox"
    Then I should not see "Test question to be deleted"

  @javascript
  Scenario: Deleting a question can be cancelled
    When I click on "Delete" "link" in the "Test question to be deleted" "table_row"
    And I press "Cancel"
    Then I should see "Test question to be deleted"

  @javascript
  Scenario: Delete a question used in a quiz
    Given I am on "Course 1" course homepage with editing mode on
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name | Test quiz |
    And I add a "True/False" question to the "Test quiz" quiz with:
      | Question name | Test used question to be deleted |
      | Question text | Write about whatever you want    |
    And I am on "Course 1" course homepage
    And I navigate to "Question bank > Questions" in current page administration
    When I click on "Delete" "link" in the "Test used question to be deleted" "table_row"
    And I press "Delete"
    Then I should not see "Test used question to be deleted"
    And I click on "Also show old questions" "checkbox"
    And I should see "Test used question to be deleted"
    And I am on "Course 1" course homepage
    And I follow "Test quiz"
    And I click on "Preview quiz now" "button"
    And I should see "Write about whatever you want"
