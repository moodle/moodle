@qbank @qbank_editquestion
Feature: Use the qbank plugin manager page for editquestion
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory      | qtype     | name                  | questiontext              |
      | Default for Test quiz | truefalse | First question        | Answer the first question |
      | Default for Test quiz | truefalse | First question second | Answer the first question |

  Scenario: Enable/disable edit question columns from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Edit question"
    And I click on "Disable" "link" in the "Edit question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then I should not see "Status"
    And the "Edit question" action should not exist for the "First question" question in the question bank
    And the "Duplicate" action should not exist for the "First question" question in the question bank
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Edit question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then I should see "Status"
    And the "Edit question" action should exist for the "First question" question in the question bank
    And the "Duplicate" action should exist for the "First question" question in the question bank
