@core @core_admin @core_ai
Feature: An administrator can manage AI subsystem settings
  In order to alter the user experience
  As an admin
  I can manage AI subsystem settings

  @javascript
  Scenario: An administrator can control the enabled state of AI Provider plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable OpenAI API provider" admin switch "on"
    And I should see "OpenAI API provider enabled."
    And I reload the page
    And I should see "Disable OpenAI API provider"
    And I toggle the "Disable OpenAI API provider" admin switch "off"
    Then I should see "OpenAI API provider disabled."

  @javascript
  Scenario: An administrator can control the enabled state of AI placement plugins using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI placements" in site administration
    When I toggle the "Enable Text editor placement" admin switch "on"
    And I should see "Text editor placement enabled."
    And I reload the page
    And I should see "Disable Text editor placement"
    And I toggle the "Disable Text editor placement" admin switch "off"
    Then I should see "Text editor placement disabled."

  @javascript
  Scenario: Placement actions should be available when an Administrator enables AI Providers using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable OpenAI API provider" admin switch "on"
    And I should see "OpenAI API provider enabled."
    And the following config values are set as admin:
      | apikey | 123 | aiprovider_openai |
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "Text editor placement"
    Then I should not see "This action is unavailable."

  @javascript
  Scenario: Placement actions should not be available when an Administrator disables AI Providers using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable OpenAI API provider" admin switch "off"
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "Text editor placement"
    And I should see "This action is unavailable." in the table row containing "Generate text"
    Then I should see "This action is unavailable." in the table row containing "Generate image"

  @javascript
  Scenario: Placement actions should not be available for enabled Providers when an Administrator disables an Action using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable OpenAI API provider" admin switch "on"
    And I should see "OpenAI API provider enabled."
    And the following config values are set as admin:
      | apikey | 123 | aiprovider_openai |
    And I click on the "Settings" link in the table row containing "OpenAI API provider"
    And I toggle the "Generate text" admin switch "off"
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "Text editor placement"
    And I should see "This action is unavailable." in the table row containing "Generate text"
    Then I should not see "This action is unavailable." in the table row containing "Generate image"
