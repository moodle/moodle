@core @core_admin
Feature: An administrator can manage TinyMCE subplugins
    In order to alter the user experience
    As an admin
    I can manage TinyMCE subplugins

  @javascript
  Scenario: An administrator can control the enabled state of TinyMCE subplugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > General settings" in site administration
    When I click on "Disable Equation editor" "link"
    Then I should see "Equation editor disabled."
    And "Disable Equation editor" "link" should not exist
    But "Enable Equation editor" "link" should exist
    When I click on "Enable Equation editor" "link"
    Then I should see "Equation editor enabled."
    And "Enable Equation editor" "link" should not exist
    But "Disable Equation editor" "link" should exist

  Scenario: An administrator can control the enabled state of TinyMCE subplugins without JavaScript
    Given I am logged in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > General settings" in site administration
    When I click on "Disable Equation editor" "link"
    Then I should see "Equation editor disabled."
    And "Disable Equation editor" "link" should not exist
    But "Enable Equation editor" "link" should exist
    When I click on "Enable Equation editor" "link"
    Then I should see "Equation editor enabled."
    And "Enable Equation editor" "link" should not exist
    But "Disable Equation editor" "link" should exist
