@tool @tool_admin_presets @javascript
Feature: I can apply presets

  Background:
    Given I log in as "admin"

  Scenario: Default settings are equal to Full preset
    Given I navigate to "Site admin presets" in site administration
    And I should see "Full"
    And I open the action menu in "Full" "table_row"
    When I choose "Review settings and apply" in the open action menu
    Then I should not see "Setting changes"

  Scenario: Applying Starter Moodle preset changes status and settings
#   Checking the settings before applying Full Moodle preset.
    Given I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And "Hide" "icon" should exist in the "Chat" "table_row"
    And I navigate to "Plugins > Availability restrictions > Manage restrictions" in site administration
    And "Hide" "icon" should exist in the "Restriction by grouping" "table_row"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    And "Hide" "icon" should exist in the "Logged in user" "table_row"
    And I navigate to "Plugins > Course formats > Manage course formats" in site administration
    And "Disable" "icon" should exist in the "Social format" "table_row"
    And I navigate to "Plugins > Question behaviours > Manage question behaviours" in site administration
    And "Enabled" "icon" should exist in the "Immediate feedback with CBM" "table_row"
    And I navigate to "Plugins > Question types > Manage question types" in site administration
    And "Enabled" "icon" should exist in the "Calculated multichoice" "table_row"
    When I navigate to "Site admin presets" in site administration
    And I should see "Starter"
    And I open the action menu in "Starter" "table_row"
    And I choose "Review settings and apply" in the open action menu
    And I should see "Setting changes"
#   We are not testing all the settings, just one of each type.
    And I should see "Enable badges" in the "Setting changes" "table"
    And I should see "Enable competencies" in the "core_competency" "table_row"
    And I should see "Chat" in the "Setting changes" "table"
    And I should see "Restriction by grouping" in the "Setting changes" "table"
    And I should see "Logged in user" in the "Setting changes" "table"
    And I should see "Social format" in the "format" "table_row"
    And I should see "Immediate feedback with CBM" in the "Setting changes" "table"
    And I should see "Calculated multichoice" in the "Setting changes" "table"
    And I click on "Apply" "button"
    And I navigate to "Advanced features" in site administration
    And the field "Enable badges" matches value "0"
    And the field "Enable competencies" matches value "0"
    And I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And "Hide" "icon" should not exist in the "Chat" "table_row"
    And I navigate to "Plugins > Availability restrictions > Manage restrictions" in site administration
    And "Hide" "icon" should not exist in the "Restriction by grouping" "table_row"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    And "Hide" "icon" should not exist in the "Logged in user" "table_row"
    And I navigate to "Plugins > Course formats > Manage course formats" in site administration
    And "Disable" "icon" should not exist in the "Social format" "table_row"
    And I navigate to "Plugins > Question behaviours > Manage question behaviours" in site administration
    And "Enabled" "icon" should not exist in the "Immediate feedback with CBM" "table_row"
    And I navigate to "Plugins > Question types > Manage question types" in site administration
    And "Enabled" "icon" should not exist in the "Calculated multichoice" "table_row"

  Scenario: Applied exported settings
    Given I navigate to "Site admin presets" in site administration
    And I click on "Create preset" "button"
    And I set the field "Name" to "Current"
    And I click on "Create preset" "button"
    And I should see "Current"
    And I open the action menu in "Current" "table_row"
    When I choose "Review settings and apply" in the open action menu
    And I should not see "Setting changes"
    And I click on "Continue" "button"
    And the following config values are set as admin:
      | enabled | 0 | core_competency |
    And I open the action menu in "Current" "table_row"
    And I choose "Review settings and apply" in the open action menu
    Then I should see "Setting changes"
    And I should see "Enable competencies" in the "core_competency" "table_row"
    And I click on "Apply" "button"
    And I navigate to "Advanced features" in site administration
    And the field "Enable competencies" matches value "1"
