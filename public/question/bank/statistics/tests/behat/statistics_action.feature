@qbank @qbank_statistics
Feature: Use the qbank plugin manager page for statistics
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

  Scenario: Enable/disable statistics columns from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Question statistics"
    And I click on "Disable" "link" in the "Question statistics" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then I should not see "Facility index"
    And I should not see "Discriminative efficiency"
    And I should not see "Needs checking?"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Question statistics" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I should see "Facility index"
    And I should see "Discriminative efficiency"
    And I should see "Needs checking?"
