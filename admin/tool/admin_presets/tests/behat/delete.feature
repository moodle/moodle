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
    When I press "Delete" action in the "Custom preset" report row
    Then I should see "Are you sure you want to delete the site admin preset Custom preset?"
    And I should not see "This preset has been previously applied"
    And I click on "Cancel" "button" in the ".modal-dialog" "css_element"
    And I should see "Presets allow you to easily switch between different site admin configurations."
    And I press "Delete" action in the "Custom preset" report row
    And I should not see "This preset has been previously applied"
    And I click on "Delete" "button" in the ".modal-dialog" "css_element"
    And I should see "Presets allow you to easily switch between different site admin configurations."
    And I should not see "Custom preset" in the "reportbuilder-table" "table"

  Scenario: Delete preset that has been applied
    When I press "Review settings and apply" action in the "Custom preset" report row
    And I click on "Apply" "button"
    And I navigate to "Site admin presets" in site administration
    And I press "Delete" action in the "Custom preset" report row
    And I should see "Are you sure you want to delete the site admin preset Custom preset?"
    Then I should see "This preset has been previously applied"
    And I click on "Delete" "button" in the ".modal-dialog" "css_element"
    And I should see "Presets allow you to easily switch between different site admin configurations"
    And I should not see "Custom preset" in the "reportbuilder-table" "table"
