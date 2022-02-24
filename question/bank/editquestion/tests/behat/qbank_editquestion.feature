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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name                  | questiontext              |
      | Test questions   | truefalse | First question        | Answer the first question |
      | Test questions   | truefalse | First question second | Answer the first question |

  Scenario: Enable/disable edit question columns from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Edit question"
    And I click on "Disable" "link" in the "Edit question" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    Then I should not see "Status"
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    And I should not see "Edit question" in the "region-main" "region"
    And I should not see "Duplicate" in the "region-main" "region"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Edit question" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    Then I should see "Status"
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    And I should see "Edit question" in the "region-main" "region"
    And I should see "Duplicate" in the "region-main" "region"
