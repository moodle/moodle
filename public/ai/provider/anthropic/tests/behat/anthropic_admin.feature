@aiprovider_anthropic
Feature: Configure Anthropic Claude API provider
  In order to use the Anthropic Claude API for AI features
  As an admin
  I can create and configure an Anthropic Claude provider instance

  @javascript
  Scenario: An administrator can create an Anthropic Claude provider instance
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I click on "Create a new provider instance" "link"
    And I select "Anthropic Claude API provider" from the "Choose AI provider plugin" singleselect
    And I set the following fields to these values:
      | Name for instance | Anthropic test instance |
      | Anthropic API key | test_api_key_123        |
    And I click on "Create instance" "button"
    Then I should see "Anthropic test instance AI provider instance created"
    And I should see "Anthropic test instance"

  @javascript
  Scenario: An administrator can enable and disable an Anthropic Claude provider instance
    Given the following "core_ai > ai providers" exist:
      | provider             | name             | enabled | apikey           |
      | aiprovider_anthropic | Anthropic test   | 0       | test_api_key_123 |
    And I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    When I toggle the "Enable Anthropic test" admin switch "on"
    Then I should see "Anthropic test enabled."
    And I reload the page
    And I should see "Disable Anthropic test"
    When I toggle the "Disable Anthropic test" admin switch "off"
    Then I should see "Anthropic test disabled."

  @javascript
  Scenario: Anthropic Claude provider only shows text actions (no image generation)
    Given the following "core_ai > ai providers" exist:
      | provider             | name           | enabled | apikey           |
      | aiprovider_anthropic | Anthropic test | 1       | test_api_key_123 |
    And I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Settings" link in the table row containing "Anthropic test"
    Then I should see "Generate text"
    And I should see "Summarise text"
    And I should see "Explain text"
    And I should not see "Generate image"

  @javascript
  Scenario: An administrator can configure Anthropic Claude action settings
    Given the following "core_ai > ai providers" exist:
      | provider             | name           | enabled | apikey           |
      | aiprovider_anthropic | Anthropic test | 1       | test_api_key_123 |
    And I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Settings" link in the table row containing "Anthropic test"
    And I click on the "Settings" link in the table row containing "Generate text"
    Then the "AI model" select box should contain "Claude Haiku 4.5"
    And the "AI model" select box should contain "Claude Sonnet 4.5"
    And the "AI model" select box should contain "Claude Sonnet 5"
    And the "AI model" select box should contain "Claude Opus 4.5"
    And the "AI model" select box should contain "Claude Opus 4.8"
    And the "AI model" select box should contain "Claude Opus 5"
    And the "AI model" select box should contain "Custom"
    When I set the following fields to these values:
      | AI model   | Claude Opus 4.8 |
      | Max tokens | 4096            |
    And I click on "Save changes" "button"
    Then I should see "Generate text action settings updated"
    When I click on the "Settings" link in the table row containing "Generate text"
    Then the field "AI model" matches value "Claude Opus 4.8"
    And the field "Max tokens" matches value "4096"

  @javascript
  Scenario: The temperature setting is only offered for Claude models that accept it
    Given the following "core_ai > ai providers" exist:
      | provider             | name           | enabled | apikey           |
      | aiprovider_anthropic | Anthropic test | 1       | test_api_key_123 |
    And I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Settings" link in the table row containing "Anthropic test"
    And I click on the "Settings" link in the table row containing "Generate text"
    When I set the field "AI model" to "Claude Haiku 4.5"
    Then I should see "Temperature"
    When I set the field "AI model" to "Claude Sonnet 4.5"
    Then I should see "Temperature"
    When I set the field "AI model" to "Claude Opus 4.5"
    Then I should see "Temperature"
    When I set the field "AI model" to "Claude Opus 4.8"
    Then I should not see "Temperature"
    When I set the field "AI model" to "Claude Opus 5"
    Then I should not see "Temperature"
    When I set the field "AI model" to "Claude Sonnet 5"
    Then I should not see "Temperature"
    And I should see "Max tokens"

  @javascript
  Scenario: An administrator can point an action at an unlisted Claude model
    Given the following "core_ai > ai providers" exist:
      | provider             | name           | enabled | apikey           |
      | aiprovider_anthropic | Anthropic test | 1       | test_api_key_123 |
    And I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Settings" link in the table row containing "Anthropic test"
    And I click on the "Settings" link in the table row containing "Generate text"
    Then I should not see "Custom model name"
    When I set the field "AI model" to "Custom"
    Then I should see "Custom model name"
    When I set the field "Custom model name" to "claude-not-bundled-yet"
    And I click on "Save changes" "button"
    Then I should see "Generate text action settings updated"
    When I click on the "Settings" link in the table row containing "Generate text"
    Then the field "AI model" matches value "Custom"
    And the field "Custom model name" matches value "claude-not-bundled-yet"

  @javascript
  Scenario: A custom model name is required when the custom model option is selected
    Given the following "core_ai > ai providers" exist:
      | provider             | name           | enabled | apikey           |
      | aiprovider_anthropic | Anthropic test | 1       | test_api_key_123 |
    And I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Settings" link in the table row containing "Anthropic test"
    And I click on the "Settings" link in the table row containing "Generate text"
    When I set the field "AI model" to "Custom"
    And I click on "Save changes" "button"
    Then I should see "Required"
    And "Save changes" "button" should exist

  @javascript
  Scenario: An administrator can delete an Anthropic Claude provider instance
    Given the following "core_ai > ai providers" exist:
      | provider             | name           | enabled | apikey           |
      | aiprovider_anthropic | Anthropic test | 0       | test_api_key_123 |
    And I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Delete" link in the table row containing "Anthropic test"
    And "Delete AI provider instance" "dialogue" should be visible
    And I click on "Delete" "button" in the "Delete AI provider instance" "dialogue"
    Then I should see "Anthropic test AI provider instance deleted"
