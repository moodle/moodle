@tool @tool_admin_presets @javascript
Feature: Admin preset deletion

  Background: Create a preset to delete
    Given I log in as "admin"
    And the following "tool_admin_presets > preset" exist:
      | name       |
      | Custom preset |
    And I navigate to "Site admin presets" in site administration

  Scenario: Core preset settings can't be deleted
    Given I should see "Starter"
    And I should see "Full"
    And I should see "Custom preset"
    When I open the action menu in "Custom preset" "table_row"
    And I should see "Delete"
    And I press the escape key
    And I open the action menu in "Full" "table_row"
    Then I should not see "Delete"
    And I press the escape key
    And I open the action menu in "Starter" "table_row"
    And I should not see "Delete"

  Scenario: Custom preset settings can be deleted
    Given I should see "Custom preset"
    And I open the action menu in "Custom preset" "table_row"
    When I choose "Delete" in the open action menu
    And I should see "Are you sure you want to delete the site admin preset Custom preset?"
    And I should not see "This preset has been previously applied"
    And I click on "Cancel" "button"
    And I should see "Presets allow you to easily switch between different site admin configurations."
    And "Custom preset" "table_row" should exist
    And I open the action menu in "Custom preset" "table_row"
    And I choose "Delete" in the open action menu
    And I should not see "This preset has been previously applied"
    And I click on "Delete" "button"
    And I should see "Presets allow you to easily switch between different site admin configurations."
    Then "Custom preset" "table_row" should not exist

  Scenario: Delete preset that has been applied
    Given I open the action menu in "Custom preset" "table_row"
    And I choose "Review settings and apply" in the open action menu
    And I click on "Apply" "button"
    And I navigate to "Site admin presets" in site administration
    When I open the action menu in "Custom preset" "table_row"
    And I choose "Delete" in the open action menu
    And I should see "Are you sure you want to delete the site admin preset Custom preset?"
    Then I should see "This preset has been previously applied"
    And I click on "Delete" "button"
    And I should see "Presets allow you to easily switch between different site admin configurations"
    And "Custom preset" "table_row" should not exist
