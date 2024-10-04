@tool @tool_mfa
Feature: Manage factor plugins
  In order to manage different factors in MFA
  As an administrator
  I need to enable/disable or change the order of the factor plugins from MFA management page

  @javascript
  Scenario: Administrators can manage factor plugins from MFA managements page
    Given I am logged in as "admin"
    And I navigate to "Plugins > Admin tools > Multi-factor authentication > Manage multi-factor authentication" in site administration
    # Enable and disable Factor.
    When I toggle the "Enable Trust this device" admin switch "on"
    And I should see "Trust this device enabled."
    And I should see "Disable Trust this device" in the "Trust this device" "table_row"
    And I reload the page
    And I should see "Disable Trust this device"
    And I toggle the "Disable Trust this device" admin switch "off"
    And I should see "Trust this device disabled."
    And I should see "Enable Trust this device" in the "Trust this device" "table_row"
    # Ordering Factors.
    Then I toggle the "Enable Trust this device" admin switch "on"
    And I toggle the "Enable Grace period" admin switch "on"
    And I click on "Move up" "link" in the "Grace period" "table_row"
    And "Grace period" "table_row" should appear before "Trust this device" "table_row"
    And I click on "Move down" "link" in the "Grace period" "table_row"
    And "Grace period" "table_row" should appear after "Trust this device" "table_row"

  Scenario: Email factor is enabled by default
    Given I am logged in as "admin"
    When I navigate to "Plugins > Admin tools > Multi-factor authentication > Manage multi-factor authentication" in site administration
    Then I should see "Disable Email" in the "Email" "table_row"
