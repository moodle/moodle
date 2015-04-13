@tool @tool_availabilityconditions
Feature: Manage availability conditions
  In order to control availability restrictions
  As an administrator
  I need to see the list of restrictions and hide or show them

  @javascript
  Scenario: Display list of availability conditions
    # Check the report doesn't show when not enabled.
    Given I log in as "admin"
    And I expand "Site administration" node
    When I expand "Plugins" node
    Then I should not see "Availability restrictions"

    # Enable it and check I can now see and click on it.
    And the following config values are set as admin:
      | enableavailability | 1 |
    And I am on homepage
    And I navigate to "Manage restrictions" node in "Site administration > Plugins > Availability restrictions"

    # Having clicked on it, I should also see the list of plugins.
    And I should see "Restriction by date"
    And I should see "Restriction by grades"

  @javascript
  Scenario: Hide and show conditions
    # Get to the right page
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following config values are set as admin:
      | enableavailability | 1 |
    And I log in as "admin"
    And I am on site homepage
    When I navigate to "Manage restrictions" node in "Site administration > Plugins > Availability restrictions"

    # Check the icon is there (it should be a Hide icon, meaning is currently visible).
    Then "input[title=Hide]" "css_element" should exist in the "Restriction by date" "table_row"

    # Click the icon. It should toggle to hidden (title=Show).
    And I click on "input[title=Hide]" "css_element" in the "Restriction by date" "table_row"
    And "input[title=Show]" "css_element" should exist in the "Restriction by date" "table_row"

    # Toggle it back to visible (title=Hide).
    And I click on "input[title=Show]" "css_element" in the "Restriction by date" "table_row"
    And "input[title=Hide]" "css_element" should exist in the "Restriction by date" "table_row"

    # OK, toggling works. Set the grade one to Hide and we'll go see if it actually worked.
    And I click on "input[title=Hide]" "css_element" in the "Restriction by grade" "table_row"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And "Add restriction..." "dialogue" should be visible
    And "Date" "button" should exist in the "Add restriction..." "dialogue"
    And "Grade" "button" should not exist in the "Add restriction..." "dialogue"
