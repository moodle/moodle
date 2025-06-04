@qbank @qbank_usage
Feature: Use the qbank plugin manager page for question usage
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
    And I change window size to "large"

  Scenario: Enable/disable question usage column from the base view
    Given I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Question usage"
    When I click on "Disable" "link" in the "Question usage" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then I should not see "Usage"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Question usage" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I should see "Usage"

  @javascript
  Scenario: Question usage modal should work without any usage data
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I should see "Default for Test quiz"
    And I should see "0" on the usage column
    When I click "0" on the usage column
    Then I should see "Version 1"
    And I should see "v1 (latest)" in the "Question 1" "question"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    And I should see "0" on the usage column

  @javascript
  Scenario: Question usage modal should work with usage data
    Given quiz "Test quiz" contains the following questions:
      | question       | page |
      | First question | 1    |
    And I am on the "Test quiz" "mod_quiz > question bank" page logged in as "admin"
    And I should see "Default for Test quiz"
    And I should see "1" on the usage column
    When I click "1" on the usage column
    Then "Test quiz" "table_row" should exist in the "question-usage_table" "region"
