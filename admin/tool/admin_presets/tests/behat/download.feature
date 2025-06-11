@tool @tool_admin_presets
Feature: I can download a preset
  Background:
    Given the following "tool_admin_presets > preset" exist:
      | name       |
      | Custom preset |

  Scenario: Custom preset settings can be downloaded
    Given I log in as "admin"
    And I navigate to "Site admin presets" in site administration
    When I open the action menu in "Custom preset" "table_row"
    Then following "Download" in the "Custom preset" "table_row" should download a file that:
      | Has mimetype                 | text/xml      |
      | Contains text in xml element | Custom preset |

  Scenario: Core preset settings can be downloaded
    Given I log in as "admin"
    And I navigate to "Site admin presets" in site administration
    When I open the action menu in "Starter" "table_row"
    Then following "Download" in the "Starter" "table_row" should download a file that:
      | Has mimetype                 | text/xml |
      | Contains text in xml element | Starter  |
