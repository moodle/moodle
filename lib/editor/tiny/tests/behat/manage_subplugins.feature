@core @core_admin
Feature: An administrator can manage TinyMCE subplugins
    In order to alter the user experience
    As an admin
    I can manage TinyMCE subplugins

  @javascript
  Scenario: An administrator can control the enabled state of TinyMCE subplugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > General settings" in site administration
    When I toggle the "Disable Equation editor" admin switch "off"
    And I should see "Equation editor disabled."
    And I reload the page
    Then I should see "Enable Equation editor"
    And I toggle the "Enable Equation editor" admin switch "on"
    And I should see "Equation editor enabled."
    And I reload the page
    Then I should see "Disable Equation editor"
