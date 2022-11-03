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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course         | C1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

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
    And I set the field "Select a category" to "Test questions"
    And I should see "Test questions"
    And I should see "0" on the usage column
    When I click "0" on the usage column
    Then I should see "Version 1"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    And I should see "0" on the usage column
