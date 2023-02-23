@tool @tool_admin_presets @_file_upload @javascript
Feature: I can upload a preset file

  Background: Go to the Import settings page
    Given I log in as "admin"
    And I navigate to "Site admin presets" in site administration
    And I click on "Import preset" "link_or_button"

  Scenario: Import settings and plugins from a valid XML file
    Given I should see "Import site admin preset"
    And I click on "Import" "button"
    And I should see "You must supply a value here"
    And I upload "admin/presets/tests/fixtures/import_settings_plugins.xml" file to "Select file" filemanager
    And I click on "Import" "button"
    And I should see "Setting changes"
    And I should see "Imported preset"
    And I should see "Enable portfolios" in the "core" "table_row"
    And I should see "900" in the "mod_lesson" "table_row"
    When I click on "Apply" "button"
    And I should see "Setting changes"
    And I should see "Unchanged settings"
    And I click on "Continue" "button"
    Then I should see "Imported preset" in the "Site admin presets table" "table"
    And I navigate to "Advanced features" in site administration
    And the following fields match these values:
      | Enable portfolios | 1 |
    And I navigate to "Plugins > Activity modules > Lesson" in site administration
    And the following fields match these values:
      | Popup window width | 900 |

  Scenario: Rename imported settings
    Given I should see "Import site admin preset"
    And I set the field "Name" to "Renamed preset"
    And I upload "admin/presets/tests/fixtures/import_settings_plugins.xml" file to "Select file" filemanager
    And I click on "Import" "button"
    And I should not see "Imported preset"
    And I should see "Renamed preset"
    When I click on "Apply" "button"
    And I click on "Continue" "button"
    Then I should not see "Imported preset" in the "Site admin presets table" "table"
    And I should see "Renamed preset" in the "Site admin presets table" "table"

  Scenario: Import settings from an invalid XML file
    Given I set the field "Name" to "Renamed preset"
    And I upload "admin/presets/tests/fixtures/invalid_xml_file.xml" file to "Select file" filemanager
    When I click on "Import" "button"
    Then I should see "Wrong file"
    And I should not see "Setting changes"
    And I navigate to "Site admin presets" in site administration
    And I should not see "Renamed preset"

  Scenario: Import unexisting settings category
    Given I set the field "Name" to "Renamed preset"
    And I upload "admin/presets/tests/fixtures/unexisting_category.xml" file to "Select file" filemanager
    When I click on "Import" "button"
    Then I should see "No valid settings"
    And I should not see "Setting changes"
    And I navigate to "Site admin presets" in site administration
    And I should not see "Renamed preset"

  Scenario: Import one unexisting setting
    Given the following config values are set as admin:
      | debug | 0 |
      | debugdisplay | 0 |
    And I set the field "Name" to "Renamed preset"
    And I upload "admin/presets/tests/fixtures/import_settings_with_unexisting_setting.xml" file to "Select file" filemanager
    When I click on "Import" "button"
    And I should see "Setting changes"
    And I should see "Enable portfolios" in the "core" "table_row"
    And I should not see "No valid settings"
    And I click on "Apply" "button"
    And I should see "Setting changes"
    And I click on "Continue" "button"
    Then I should see "Renamed preset" in the "Site admin presets table" "table"
    And I navigate to "Advanced features" in site administration
    And the following fields match these values:
      | Enable portfolios | 1 |
