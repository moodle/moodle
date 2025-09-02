@qbank @qbank_previewquestion
Feature: Use the qbank plugin manager page for previewquestion
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory      | qtype     | name           | questiontext              |
      | Default for Test quiz | truefalse | First question | Answer the first question |

  Scenario: Enable/disable previewquestion column from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Preview question"
    And I click on "Disable" "link" in the "Preview question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then the "Preview" item should not exist in the "Edit" action menu of the "First question" "table_row"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Preview question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And the "Preview" item should exist in the "Edit" action menu of the "First question" "table_row"

  Scenario: Enable/disable preview button from question edit form
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Preview question"
    And I click on "Disable" "link" in the "Preview question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I choose "Edit question" action for "First question" in the question bank
    Then I should not see "Preview" in the "region-main" "region"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Preview question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I choose "Edit question" action for "First question" in the question bank
    And I should see "Preview" in the "region-main" "region"
