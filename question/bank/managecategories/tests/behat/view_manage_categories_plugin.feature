@qbank @qbank_managecategories @javascript
Feature: Use the qbank plugin manager page for managecategories
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
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

  Scenario: Enable/disable managecategories plugin from the base view
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Manage categories"
    And I click on "Disable" "link" in the "Manage categories" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    Then "Categories" "link" should not exist in current page administration
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Manage categories" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank" in current page administration
    And I should see "Categories" in the "//div[contains(@class, 'urlselect')]//option[contains(text(), 'Categories')]" "xpath_element"

  Scenario: Enable/disable the tab New category when tyring to add a random question to a quiz
    Given I log in as "admin"
    When I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I should see "Manage categories"
    And I click on "Disable" "link" in the "Manage categories" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I click on "Add question" "link"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    Then I should not see "New category"
    And I press "id_cancel"
    And I navigate to "Plugins > Question bank plugins > Manage question bank plugins" in site administration
    And I click on "Enable" "link" in the "Manage categories" "table_row"
    And I am on the "Test quiz" "quiz activity" page
    And I click on "Add question" "link"
    And I open the "last" add to quiz menu
    And I follow "a random question"
    And I should see "New category"
