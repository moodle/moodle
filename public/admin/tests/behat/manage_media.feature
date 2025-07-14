@core @core_admin
Feature: An administrator can manage Media plugins
    In order to alter the user experience
    As an admin
    I can manage media plugins

  @javascript
  Scenario: An administrator can control the enabled state of media plugins using JavaScript
    The state change should be reflected in the UI and persist across page reloads
    Given I am logged in as "admin"
    And I navigate to "Plugins > Media players > Manage media players" in site administration
    When I toggle the "Disable YouTube" admin switch "off"
    And I should see "YouTube disabled."
    And I reload the page
    Then I should see "Enable YouTube"
    And I toggle the "Enable YouTube" admin switch "on"
    And I should see "YouTube enabled."
    And I reload the page
    Then I should see "Disable YouTube"
