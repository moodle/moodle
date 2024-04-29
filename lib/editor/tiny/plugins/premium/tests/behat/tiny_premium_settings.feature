@editor @editor_tiny
Feature: Check the features of the TinyMCE Premium settings
  In order to use TinyMCE Premium features
  As an admin
  I need TinyMCE Premium settings to be configured correctly

  Background:
    Given I am logged in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > TinyMCE Premium" in site administration

  @javascript
  Scenario: I can see a warning banner when I enable a TinyMCE premium plugin without an API key set
    When I click on "Enable Advanced Table" "link"
    Then I should see "Advanced Table enabled."
    And I should see "Enabled TinyMCE Premium plugins will not be available until an API key is added."

  @javascript
  Scenario: I cannot see a warning banner when I enable a TinyMCE premium plugin with an API key set
    Given the following config values are set as admin:
      | apikey | "123456" | tiny_premium |
    When I click on "Enable Advanced Table" "link"
    Then I should see "Advanced Table enabled."
    And I should not see "Enabled TinyMCE Premium plugins will not be available until an API key is added."
