@qbank @qbank_viewquestiontext
Feature: Use the qbank plugin manager page for viewquestiontext
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
      | questioncategory | qtype     | name           | questiontext                                                         |
      | Test questions   | truefalse | First question | Answer the <span class="totestforhtml">first</span> &lt;question&gt; |

  @javascript
  Scenario: Display of plain question text can be turned on and off
    When I am on the "Test quiz" "mod_quiz > question bank" page logged in as admin
    And I set the field "Show question text in the question list?" to "text only"
    Then I should see "Answer the first <question>"
    And ".totestforhtml" "css_element" should not exist in the "Answer the first <question>" "table_row"
    And I set the field "Show question text in the question list?" to "No"
    And I should not see "Answer the first <question>"

  @javascript
  Scenario: Display of full question text can be turned on and off
    When I am on the "Test quiz" "mod_quiz > question bank" page logged in as admin
    And I set the field "Show question text in the question list?" to "with images"
    Then I should see "Answer the first <question>"
    And ".totestforhtml" "css_element" should exist in the "Answer the first <question>" "table_row"
    And I set the field "Show question text in the question list?" to "No"
    And I should not see "Answer the first <question>"

  @javascript
  Scenario: Option does not show if the plugin is disabled
    Given the following config values are set as admin:
      | disabled | 1 | qbank_viewquestiontext |
    When I am on the "Test quiz" "mod_quiz > question bank" page logged in as admin
    Then I should not see "Show question text in the question list"

  @javascript
  Scenario: Enable/disable viewquestiontext column from the base view
    Given I log in as "admin"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "View question text"
    When I click on "Disable" "link" in the "View question text" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    And I should not see "Show question text in the question list"
    Then "#categoryquestions .questiontext" "css_element" should not be visible
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "View question text" "table_row"
    And I am on the "Test quiz" "mod_quiz > question bank" page
    When I set the field "Show question text in the question list?" to "text only"
    And I should see "Answer the first <question>"
