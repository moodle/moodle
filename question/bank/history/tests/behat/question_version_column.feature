@qbank @qbank_history @javascript
Feature: Use the qbank plugin manager page for version column
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name             |
      | Test questions   | truefalse | First Question   |
    And the following "core_question > updated questions" exist:
      | questioncategory | question        | name            |
      | Test questions   | First Question  | First Edit      |
      | Test questions   | First Question  | Edit the Second |
      | Test questions   | First Question  | The Final Edit  |

  Scenario: Enable/disable version column from the base view
    Given I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Question history"
    When I click on "Disable" "link" in the "Question history" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    Then I should not see "Version" in the "region-main" "region"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Question history" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I apply question bank filter "Category" with value "Test questions"
    And I should see "Version" in the "region-main" "region"

  Scenario: Sort the question versions
    Given I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I apply question bank filter "Category" with value "Test questions"
    When I choose "History" action for "The Final Edit" in the question bank
    Then "Edit the Second" "checkbox" should appear before "The Final Edit" "checkbox"
    And "First Edit" "checkbox" should appear before "Edit the Second" "checkbox"
    And "First Question" "checkbox" should appear before "First Edit" "checkbox"
    And I follow "Sort by Version descending"
    And "The Final Edit" "checkbox" should appear before "Edit the Second" "checkbox"
    And "Edit the Second" "checkbox" should appear before "First Edit" "checkbox"
    And "First Edit" "checkbox" should appear before "First Question" "checkbox"
    And I follow "Sort by Version ascending"
    And "Edit the Second" "checkbox" should appear before "The Final Edit" "checkbox"
    And "First Edit" "checkbox" should appear before "Edit the Second" "checkbox"
    And "First Question" "checkbox" should appear before "First Edit" "checkbox"
