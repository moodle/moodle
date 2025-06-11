@qbank @qbank_bulkmove
Feature: Use the qbank plugin manager page for bulkmove
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
      | contextlevel | reference | name            |
      | Course       | C1        | Test questions  |
      | Course       | C1        | Moved questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

  @javascript
  Scenario: Enable/disable bulk move questions bulk action from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Bulk move questions"
    And I click on "Disable" "link" in the "Bulk move questions" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    Then I should not see question bulk action "move"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Bulk move questions" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I click on "First question" "checkbox"
    And I click on "With selected" "button"
    And I should see question bulk action "move"

  @javascript
  Scenario: Questions can be bulk moved from the question bank
    Given the following "questions" exist:
      | questioncategory | qtype       | name       | questiontext              |
      | Test questions   | truefalse   | Question 1 | Answer the first question |
      | Test questions   | missingtype | Question 2 | Write something           |
      | Test questions   | essay       | Question 3 | frog                      |
    # Navigate to question bank.
    And I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    # Select questions to be moved.
    And I click on "Question 1" "checkbox"
    And I click on "Question 2" "checkbox"
    And I click on "With selected" "button"
    When I press "Move to"
    # Select a different category to move the questions into.
    And I select "Moved questions" from the "category" singleselect
    And I press "Move to"
    # Confirm that selected questions are moved to selected category while unselected questions are not moved.
    Then I should see "Moved questions"
    And I should see "Question 1"
    And I should see "Question 2"
    And I should not see "Question 3"
