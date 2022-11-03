@qbank @qbank_tagquestion
Feature: Use the qbank plugin manager page for tagquestion
  In order to check the plugin behaviour with enable and disable

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course         | C1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

  Scenario: Enable/disable tagquestion column from the base view
    Given I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Tag question"
    And I click on "Disable" "link" in the "Tag question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And the "Manage tags" action should not exist for the "First question" question in the question bank
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Tag question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then the "Manage tags" action should exist for the "First question" question in the question bank

  Scenario: Enable/disable tagquestion section from question edit form
    Given I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Tag question"
    And I click on "Disable" "link" in the "Tag question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I choose "Edit question" action for "First question" in the question bank
    Then I should not see "Tags" in the "region-main" "region"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Tag question" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I choose "Edit question" action for "First question" in the question bank
    And I should see "Tags" in the "region-main" "region"
