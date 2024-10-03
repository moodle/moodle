@core @core_admin @core_ai
Feature: An administrator can manage AI subsystem settings
  In order to alter the user experience
  As an admin
  I can manage AI subsystem settings

  @javascript
  Scenario: An administrator can control the enabled state of AI Provider plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable OpenAI API Provider" admin switch "on"
    And I should see "OpenAI API Provider enabled."
    And I reload the page
    And I should see "Disable OpenAI API Provider"
    And I toggle the "Disable OpenAI API Provider" admin switch "off"
    Then I should see "OpenAI API Provider disabled."

  @javascript
  Scenario: An administrator can control the enabled state of AI Placement plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI placements" in site administration
    When I toggle the "Enable HTML Text Editor Placement" admin switch "on"
    And I should see "HTML Text Editor Placement enabled."
    And I reload the page
    And I should see "Disable HTML Text Editor Placement"
    And I toggle the "Disable HTML Text Editor Placement" admin switch "off"
    Then I should see "HTML Text Editor Placement disabled."

  @javascript
  Scenario: Placement actions should be available when an Administrator enables AI Providers using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable OpenAI API Provider" admin switch "on"
    And I should see "OpenAI API Provider enabled."
    And the following config values are set as admin:
      | apikey | 123 | aiprovider_openai |
      | orgid  | abc | aiprovider_openai |
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "HTML Text Editor Placement"
    Then I should not see "This action is unavailable."

  @javascript
  Scenario: Placement actions should not be available when an Administrator disables AI Providers using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable OpenAI API Provider" admin switch "off"
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "HTML Text Editor Placement"
    And I should see "This action is unavailable." in the table row containing "Generate text"
    Then I should see "This action is unavailable." in the table row containing "Generate image"

  @javascript
  Scenario: Placement actions should not be available for enabled Providers when an Administrator disables an Action using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable OpenAI API Provider" admin switch "on"
    And I should see "OpenAI API Provider enabled."
    And the following config values are set as admin:
      | apikey | 123 | aiprovider_openai |
      | orgid  | abc | aiprovider_openai |
    And I click on the "Settings" link in the table row containing "OpenAI API Provider"
    And I toggle the "Generate text" admin switch "off"
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "HTML Text Editor Placement"
    And I should see "This action is unavailable." in the table row containing "Generate text"
    Then I should not see "This action is unavailable." in the table row containing "Generate image"
