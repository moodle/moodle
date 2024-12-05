@qbank @qbank_deletequestion
Feature: Use the qbank plugin manager page for deletequestion
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name             |
      | Activity module | quiz1     | Test questions   |
    And the following "questions" exist:
      | questioncategory | qtype     | name       | questiontext               |
      | Test questions   | truefalse | Question 1 | Answer the first question  |
      | Test questions   | truefalse | Question 2 | Answer the second question |
      | Test questions   | truefalse | Question 3 | Answer the third question  |

  Scenario: Enable/disable delete question column from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Delete question"
    And I click on "Disable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then the "Delete" action should not exist for the "Question 1" question in the question bank
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And the "Delete" action should exist for the "Question 1" question in the question bank

  @javascript
  Scenario: Enable/disable delete questions bulk action from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Delete question"
    And I click on "Disable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I click on "With selected" "button"
    Then I should not see question bulk action "deleteselected"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I click on "With selected" "button"
    And I should see question bulk action "deleteselected"

  @javascript
  Scenario: I should not see the deleted questions in the base view
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I click on "Question 1" "checkbox"
    And I click on "Question 2" "checkbox"
    And I click on "With selected" "button"
    And I click on question bulk action "deleteselected"
    And I click on "Delete" "button" in the "Delete questions?" "dialogue"
    Then I should not see "Question 1"
    And I should not see "Question 2"

  @javascript
  Scenario: I should be able to delete a question when filtered using tags
    Given I am on the "Question 1" "core_question > edit" page logged in as "admin"
    And I change window size to "large"
    And I set the following fields to these values:
      | Tags | foo |
    And I click on "Save changes" "button"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Tag" with value "foo"
    And I click on "Question 1" "checkbox"
    And I click on "With selected" "button"
    And I click on question bulk action "deleteselected"
    When I click on "Delete" "button" in the "Delete question?" "dialogue"
    Then I should not see "Third question"
    And "foo" "autocomplete_selection" should exist

  @javascript
  Scenario: Questions can be bulk deleted from the question bank
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "teacher1"
    # Select questions to be deleted.
    And I click on "Question 1" "checkbox"
    And I click on "Question 2" "checkbox"
    And I click on "With selected" "button"
    When I press "Delete"
    # Confirm that delete confirmation message is displayed.
    Then I should see "This will delete the following questions and all their versions:"
    # Confirm that selected questions are listed on the confirmation dialog.
    And I should see "Question 1 v1"
    And I should see "Question 2 v1"
    # Delete selected questions.
    And I press "Delete"
    # Confirm that selected questions are deleted while unselected questions still exist.
    And I should not see "Question 1"
    And I should not see "Question 2"
    And I should see "Question 3"
