@core @core_admin @core_ai @core_ai_admin
Feature: An administrator can manage AI subsystem settings
  In order to alter the user experience
  As an admin
  I can manage AI subsystem settings

  @javascript
  Scenario: An administrator can create AI provider plugin instances using JavaScript
    Given I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I should see "Nothing to display"
    When I click on "Create a new provider instance" "link"
    And I select "OpenAI API provider" from the "Choose AI provider plugin" singleselect
    And I set the following fields to these values:
        | Name for instance     | OpenAI API provider test|
        | OpenAI API key        | 123                     |
        | OpenAI organization ID| abc                     |
    And I click on "Create instance" "button"
    And I should see "OpenAI API provider test AI provider instance created"
    And I should see "OpenAI API provider test"
    And I click on "Create a new provider instance" "link"
    And I select "Azure AI API provider" from the "Choose AI provider plugin" singleselect
    And I set the following fields to these values:
        | Name for instance    | Azure AI provider test                         |
        | Azure AI API key     | 123                                            |
        | Azure AI API endpoint| https://api.cognitive.microsofttranslator.com/ |
    And I click on "Create instance" "button"
    And I should see "Azure AI provider test AI provider instance created"
    And I should see "Azure AI provider test"

  @javascript
  Scenario: An administrator can enable AI provider plugin instances using JavaScript
    Given the following "core_ai > ai providers" exist:
      |provider         | name             | enabled | apikey | orgid |
      |aiprovider_openai| OpenAI API test  | 0       | 123    | abc   |
    And the following "core_ai > ai providers" exist:
      |provider           | name             | enabled | apikey | endpoint                                       |
      |aiprovider_azureai | Azure AI API test| 0       | 123    | https://api.cognitive.microsofttranslator.com/ |
    And  I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I should see "OpenAI API test"
    And I should see "Azure AI API test"
    And I toggle the "Enable OpenAI API test" admin switch "on"
    And I should see "OpenAI API test enabled."
    And I toggle the "Enable Azure AI API test" admin switch "on"
    And I should see "Azure AI API test enabled."
    And I reload the page
    And I should see "Disable OpenAI API test"
    And I should see "Disable Azure AI API test"
    And I toggle the "Disable OpenAI API test" admin switch "off"
    And I should see "OpenAI API test disabled."
    And I toggle the "Disable Azure AI API test" admin switch "off"
    Then I should see "Azure AI API test disabled."

  @javascript
  Scenario: An administrator can configure AI provider plugin instance
  action settings using JavaScript
    Given the following "core_ai > ai providers" exist:
      |provider         | name             | enabled | apikey | orgid |
      |aiprovider_openai| OpenAI API test  | 0       | 123    | abc   |
    And  I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Settings" link in the table row containing "OpenAI API test"
    And I should see "Configure provider instance"
    And I click on the "Settings" link in the table row containing "Generate text"
    And I should see "Generate text action settings"
    And I set the field "AI model" to "Custom"
    And I set the following fields to these values:
      | Custom model name | gpt-3                                               |
      | API endpoint      | https://api.openai.com/v1/engines/gpt-3/completions |
    And I click on "Save changes" "button"
    Then I should see "Generate text action settings updated"

  @javascript
  Scenario: An administrator can delete AI provider plugin instances using JavaScript
    Given the following "core_ai > ai providers" exist:
      |provider         | name             | enabled | apikey | orgid |
      |aiprovider_openai| OpenAI API test  | 0       | 123    | abc   |
    And I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Delete" link in the table row containing "OpenAI API test"
    And "Delete AI provider instance" "dialogue" should be visible
    And I click on "Delete" "button" in the "Delete AI provider instance" "dialogue"
    Then I should see "OpenAI API test AI provider instance deleted"

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
  Scenario: Placement actions should be available when an Administrator enables AI providers using JavaScript
    Given the following "core_ai > ai providers" exist:
      |provider         | name             | enabled | apikey | orgid |
      |aiprovider_openai| OpenAI API test  | 1       | 123    | abc   |
    And  I am logged in as "admin"
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "Text editor placement"
    Then I should not see "This action is unavailable."

  @javascript
  Scenario: Placement actions should not be available when an Administrator disables AI providers using JavaScript
    Given the following "core_ai > ai providers" exist:
      |provider         | name             | enabled | apikey | orgid |
      |aiprovider_openai| OpenAI API test  | 1       | 123    | abc   |
    And  I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I toggle the "Disable OpenAI API test" admin switch "off"
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "Text editor placement"
    And I should see "This action is unavailable." in the table row containing "Generate text"
    Then I should see "This action is unavailable." in the table row containing "Generate image"

  @javascript
  Scenario: Placement actions should not be available for enabled Providers when an Administrator disables an Action using JavaScript
    Given the following "core_ai > ai providers" exist:
      |provider         | name             | enabled | apikey | orgid |
      |aiprovider_openai| OpenAI API test  | 1       | 123    | abc   |
    And  I am logged in as "admin"
    And I navigate to "AI > AI providers" in site administration
    And I click on the "Settings" link in the table row containing "OpenAI API test"
    And I toggle the "Generate text" admin switch "off"
    And I navigate to "AI > AI placements" in site administration
    And I click on the "Settings" link in the table row containing "Text editor placement"
    And I should see "This action is unavailable." in the table row containing "Generate text"
    Then I should not see "This action is unavailable." in the table row containing "Generate image"

  @javascript
  Scenario: An administrator can control the enabled state of AI placement actions using JavaScript
    Given the following "core_ai > ai providers" exist:
      | provider          | name            | enabled | endpoint               |
      | aiprovider_ollama | Ollama API test | 1       | http://localhost:11434 |
    And I am logged in as "admin"
    And I navigate to "AI > AI placements" in site administration
    When I click on the "Settings" link in the table row containing "Text editor placement"
    Then I should see "Generate text" in the "flexible" "table"
    And I should see "Generate image" in the "flexible" "table"
    And I should not see "This action is unavailable. No AI providers are configured for this action." in the "Generate text" "table_row"
    And I should see "This action is unavailable. No AI providers are configured for this action." in the "Generate image" "table_row"
    And I toggle the "Generate text" admin switch "off"
    And I should see "Generate text disabled."
