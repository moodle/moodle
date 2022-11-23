@qbank @qbank_viewquestiontype
Feature: Use the qbank plugin manager page for viewquestiontype
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
  Scenario: Enable/disable viewquestiontype column from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "View question type"
    And I click on "Disable" "link" in the "View question type" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    Then "#categoryquestions .header.qtype" "css_element" should not be visible
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "View question type" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And "#categoryquestions .header.qtype" "css_element" should be visible
