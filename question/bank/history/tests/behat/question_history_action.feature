@qbank @qbank_history
Feature: Use the qbank plugin manager page for question history
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

  @javascript
  Scenario: Enable/disable question history column from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Question history"
    And I click on "Disable" "link" in the "Question history" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    Then I should not see "History" in the "region-main" "region"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Question history" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    And I should see "History" in the "region-main" "region"

  Scenario: History page shows only the specified features and questions
    Given I log in as "admin"
    And I am on the "Test quiz" "quiz activity" page
    When I navigate to "Question bank" in current page administration
    And I choose "History" action for "First question" in the question bank
    Then I should not see "Select a category"
    And I should see "No tag filters applied"
    And I should see "Question"
    And I should see "Actions"
    And I should see "Status"
    And I should see "Version"
    And I should see "Created by"
    And I should see "First question"
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    But I should not see "History"
    And I click on "#qbank-history-close" "css_element"
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    And I should see "History" in the "region-main" "region"
