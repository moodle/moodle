@tool @tool_availabilityconditions
Feature: Manage availability conditions
  In order to control availability restrictions
  As an administrator
  I need to see the list of restrictions and hide or show them

  @javascript
  Scenario: Display list of availability conditions
    # Check the report doesn't show when not enabled.
    Given the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I log in as "admin"
    And I turn editing mode on
    And I add the "Administration" block if not present
    And the following config values are set as admin:
      | enableavailability | 0 |
    And I expand "Site administration" node
    When I expand "Plugins" node
    Then I should not see "Availability restrictions"

    # Enable it and check I can now see and click on it.
    And the following config values are set as admin:
      | enableavailability | 1 |
    And I am on homepage
    And I navigate to "Plugins > Availability restrictions > Manage restrictions" in site administration

    # Having clicked on it, I should also see the list of plugins.
    And I should see "Restriction by date"
    And I should see "Restriction by grades"

  @javascript
  Scenario: Hide and show conditions
    # Get to the right page
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activity" exists:
      | activity | page |
      | course   | C1   |
      | name     | P1   |
    And I log in as "admin"
    When I navigate to "Plugins > Availability restrictions > Manage restrictions" in site administration

    # Check the icon is there (it should be a Hide icon, meaning is currently visible).
    Then "Hide" "icon" should exist in the "Restriction by date" "table_row"

    # Click the icon. It should toggle to disabled.
    And I toggle the "Disable Restriction by date" admin switch "off"
    And I should see "Restriction by date disabled."

    # Toggle it back to visible (title=Hide).
    And I toggle the "Enable Restriction by date" admin switch "on"
    And I should see "Restriction by date enabled."

    # OK, toggling works. Set the grade one to Hide and we'll go see if it actually worked.
    And I toggle the "Disable Restriction by grade" admin switch "off"
    And I am on the "P1" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And "Add restriction..." "dialogue" should be visible
    And "Date" "button" should exist in the "Add restriction..." "dialogue"
    And "Grade" "button" should not exist in the "Add restriction..." "dialogue"
