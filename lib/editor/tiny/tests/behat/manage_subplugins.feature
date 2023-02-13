@core @core_admin
Feature: An administrator can manage TinyMCE subplugins
    In order to alter the user experience
    As an admin
    I can manage TinyMCE subplugins

  @javascript
  Scenario: An administrator can control the enabled state of TinyMCE subplugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > General settings" in site administration
    When I click on "Disable the Tiny equation editor plugin" "link"
    Then I should see "The Tiny equation editor plugin has been disabled"
    And "Disable the Tiny equation editor plugin" "link" should not exist
    But "Enable the Tiny equation editor plugin" "link" should exist
    When I click on "Enable the Tiny equation editor plugin" "link"
    Then I should see "The Tiny equation editor plugin has been enabled"
    And "Enable the Tiny equation editor plugin" "link" should not exist
    But "Disable the Tiny equation editor plugin" "link" should exist

  Scenario: An administrator can control the enabled state of TinyMCE subplugins without JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > General settings" in site administration
    When I click on "Disable the Tiny equation editor plugin" "link"
    Then I should see "The Tiny equation editor plugin has been disabled"
    And "Disable the Tiny equation editor plugin" "link" should not exist
    But "Enable the Tiny equation editor plugin" "link" should exist
    When I click on "Enable the Tiny equation editor plugin" "link"
    Then I should see "The Tiny equation editor plugin has been enabled"
    And "Enable the Tiny equation editor plugin" "link" should not exist
    But "Disable the Tiny equation editor plugin" "link" should exist
