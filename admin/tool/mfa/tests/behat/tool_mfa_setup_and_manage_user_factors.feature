@tool @tool_mfa
Feature: Set up and manage user factors
  In order to set up or manage my user factor
  As a user
  I need to configure the user factor settings in my preferences

  Background:
    Given I log in as "admin"
    And the following config values are set as admin:
      | enabled | 1 | tool_mfa |

  Scenario: I see the correct buttons for factor setup and management displayed
    Given the following config values are set as admin:
      | enabled | 1 | factor_email    |
    And the following config values are set as admin:
      | enabled | 1 | factor_webauthn |
    And the following config values are set as admin:
      | enabled | 1 | factor_totp     |
    And the following "tool_mfa > User factors" exist:
      | username | factor   | label                |
      | admin    | email    | test@test.com        |
      | admin    | webauthn | MacBook              |
    And I follow "Preferences" in the user menu
    When I click on "Multi-factor authentication preferences" "link"
    # This is the only factor not yet set up.
    Then I should not see "Active" in the "#factor-card-totp" "css_element"
    # The following factors are already set up.
    And I should see "Active" in the "#factor-card-email" "css_element"
    And I should see "Active" in the "#factor-card-webauthn" "css_element"
    And I click on "Set up authenticator app" "button"
    And I should see "Set up authenticator app"
    And I click on "Cancel" "button"
    And I click on "Manage security key" "button"
    And I should see "Manage security key"

  @javascript
  Scenario: I can revoke a factor only when there is more than one active factor
    Given the following config values are set as admin:
      | enabled | 1 | factor_webauthn |
    And the following config values are set as admin:
      | enabled | 1 | factor_sms     |
    And the following "tool_mfa > User factors" exist:
    | username | factor   | label                |
    | admin    | sms      | +409111222           |
    | admin    | webauthn | MacBook              |
    And I follow "Preferences" in the user menu
    And I click on "Multi-factor authentication preferences" "link"
    And I click on "Manage SMS" "button"
    And I click on "Remove" "button" in the "+409111222" "table_row"
    When I click on "Yes, remove" "button" in the "Remove '+409111222' SMS?" "dialogue"
    Then I should see "'SMS mobile phone - +409111222' successfully removed"
    # Now there is only one active factor left.
    And I click on "Manage security key" "button"
    And I should see "Replace" in the "MacBook" "table_row"
    And I should not see "Remove" in the "MacBook" "table_row"

  @javascript
  Scenario: I can replace a factor
    Given the following config values are set as admin:
      | enabled | 1 | factor_webauthn |
    And the following "tool_mfa > User factors" exist:
    | username | factor   | label                |
    | admin    | webauthn | MacBook              |
    And I follow "Preferences" in the user menu
    And I click on "Multi-factor authentication preferences" "link"
    And I click on "Manage security key" "button"
    And I click on "Replace" "button" in the "MacBook" "table_row"
    When I click on "Yes, replace" "button" in the "Replace 'MacBook' security key?" "dialogue"
    Then I should see "Replace security key"
