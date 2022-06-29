@qbank @qbank_deletequestion
Feature: Use the qbank plugin manager page for deletequestion
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

  Scenario: Enable/disable delete question column from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Delete question"
    And I click on "Disable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    Then I should not see "Delete" in the "region-main" "region"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I click on ".dropdown-toggle" "css_element" in the "First question" "table_row"
    And I should see "Delete" in the "region-main" "region"

  @javascript
  Scenario: Enable/disable delete questions bulk action from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Delete question"
    And I click on "Disable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I click on "With selected" "button"
    Then I should not see question bulk action "deleteselected"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Delete question" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I click on "With selected" "button"
    And I should see question bulk action "deleteselected"

  @javascript
  Scenario: I should not see the deleted questions in the base view
    Given I log in as "admin"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I click on "First question" "checkbox"
    And I click on "First question second" "checkbox"
    And I click on "With selected" "button"
    And I click on question bulk action "deleteselected"
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    Then I should not see "First question"
    And I should not see "First question second"
