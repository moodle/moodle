@tool @tool_admin_presets @core_reportbuilder @javascript
Feature: Manage admin presets list
  In order to use admin presets
  As an admin
  I need to be able to create, edit and delete custom presets

  Scenario: Using filters in the presets list
    Given I log in as "admin"
    And I navigate to "Site admin presets" in site administration
    And I should see "Starter" in the "reportbuilder-table" "table"
    And I should see "Full" in the "reportbuilder-table" "table"
    Then I click on "Filters" "button"
    And I set the following fields in the "Name" "core_reportbuilder > Filter" to these values:
      | Name operator | Is equal to |
      | Name value    | Starter     |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    And I should see "Starter" in the "reportbuilder-table" "table"
    And I should not see "Full" in the "reportbuilder-table" "table"

  Scenario: Edit preset name
    Given I log in as "admin"
    And I navigate to "Site admin presets" in site administration
    And I click on "Create preset" "button"
    And I set the field "Name" to "My preset name"
    And I click on "Create preset" "button"
    And I should see "My preset name" in the "reportbuilder-table" "table"
    Then I click on "Edit admin preset name" "link" in the "My preset name" "table_row"
    And I set the field "New value for My preset name" to "My edited preset name"
    And I press the enter key
    And I should not see "My preset name" in the "reportbuilder-table" "table"
    And I should see "My edited preset name" in the "reportbuilder-table" "table"
