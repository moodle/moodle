@core @core_admin
Feature: Plugins overview page
  In order to manage plugin and settings
  As an admin
  I can access the plugins overview page

  Background:
    Given I am logged in as "admin"
    And I navigate to "Plugins > Plugins overview" in site administration

  Scenario: An administrator can view plugins overview page
    Then "Plugins overview" "text" should exist
    And "Check for available updates" "button" should exist
    And "All plugins" "link" should exist
    And "Additional plugins" "link" should exist

  Scenario: An administrator can access list of activities when activity modules cog icon is pressed
    When I click on "Settings" "link" in the "Activity modules" "table_row"
    Then "Activities" "text" should exist
    And "Manage activities" "text" should exist in the ".breadcrumb" "css_element"
    And "Activity modules" "link" should exist in the ".breadcrumb" "css_element"

  Scenario: An administrator can access plugin settings
    When I click on "Settings" "link" in the "Assignment" "table_row"
    Then "Assignment settings" "text" should exist
    And "Assignment settings" "text" should exist in the ".breadcrumb" "css_element"
    And "Assignment" "link" should exist in the ".breadcrumb" "css_element"
    And "Activity modules" "link" should exist in the ".breadcrumb" "css_element"

  Scenario: Plugins with dependencies cannot be uninstalled
    When I navigate to "Plugins > Plugins overview" in site administration
    Then "Uninstall" "link" should not exist in the "Database" "table_row"
    And "Required by: filter_data" "text" should exist in the "Database" "table_row"

  Scenario: Cancelling plugin uninstall does not uninstall the selected plugin
    When I click on "Uninstall" "link" in the "Assignment" "table_row"
    Then "Uninstalling Assignment" "text" should exist
    And "You are about to uninstall the plugin Assignment. This will completely delete everything in the database associated with this plugin, including its configuration, log records, user files managed by the plugin etc. There is no way back and Moodle itself does not create any recovery backup. Are you SURE you want to continue?" "text" should exist
    And I click on "Cancel" "button"
    And "Uninstall" "link" should exist in the "Assignment" "table_row"
