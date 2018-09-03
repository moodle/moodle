@tool @tool_monitor
Feature: Enable/disable managment of the event monitor
  In order to manage event monitoring
  As an admin
  I need to enable/disable it

  Scenario: Tool is disabled by default.
    Given I log in as "admin"
    When I navigate to "Event monitoring rules" node in "Site administration > Reports"
    Then I should see "Event monitoring is currently disabled"
    And I should see "Enable"
    And I should not see "Add a new rule"
    And I click on "Enable" "link"
    And I should see "Event monitoring is currently enabled"
    And I should see "Disable"
    And I should see "Add a new rule"
    And I click on "Disable" "link"
    And I should see "Event monitoring is currently disabled"
    And I should not see "Add a new rule"
