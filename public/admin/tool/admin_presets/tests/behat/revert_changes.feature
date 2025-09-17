@tool @tool_admin_presets @javascript
Feature: I can revert changes after a load

  Background: Apply Starter Moodle to revert it
    Given I log in as "admin"
    And I navigate to "Site admin presets" in site administration
    And I press "Review settings and apply" action in the "Moodle with all of the most popular features" report row
    And I should see "Setting changes"
    And I click on "Apply" "button"
    And I click on "Continue" "button"

  Scenario: Presets that haven't been applied can't be reverted
    Given I open the action menu in "Full" "table_row"
    Then I should not see "Show version history"

  Scenario: Presets that have been applied can be reverted
    # Checking applied settings before reverting them.
    Given I navigate to "Advanced features" in site administration
    And the field "Enable badges" matches value "0"
    And the field "Enable competencies" matches value "0"
    And I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And I should see "Enable Database"
    And I navigate to "Plugins > Availability restrictions > Manage restrictions" in site administration
    And I should see "Enable Restriction by grouping"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    And "Disable Logged in user" "icon" should not exist in the "Logged in user" "table_row"
    And I navigate to "Plugins > Question behaviours > Manage question behaviours" in site administration
    And "Enabled" "link" should not exist in the "Immediate feedback with CBM" "table_row"
    And I navigate to "Plugins > Question types > Manage question types" in site administration
    And "Enabled" "link" should not exist in the "Calculated multichoice" "table_row"
    And I navigate to "Site admin presets" in site administration
    And I press "Show version history" action in the "Moodle with all of the most popular features" report row
    When I click on "Restore this version" "link"
    And I navigate to "Advanced features" in site administration
    Then the field "Enable badges" matches value "1"
    And the field "Enable competencies" matches value "1"
    And I navigate to "Plugins > Activity modules > Manage activities" in site administration
    And I should see "Disable Database"
    And I navigate to "Plugins > Availability restrictions > Manage restrictions" in site administration
    And I should see "Disable Restriction by grouping"
    And I navigate to "Plugins > Blocks > Manage blocks" in site administration
    And I should see "Disable Logged in user"
    And I navigate to "Plugins > Question behaviours > Manage question behaviours" in site administration
    And "Enabled" "link" should exist in the "Immediate feedback with CBM" "table_row"
    And I navigate to "Plugins > Question types > Manage question types" in site administration
    And "Enabled" "link" should exist in the "Calculated multichoice" "table_row"
