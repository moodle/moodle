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

  @javascript
  Scenario: I see a notification when I have both the premium and default accessibility checkers enabled
    When I click on "Enable Accessibility Checker" "link"
    Then I should see "Accessibility Checker enabled."
    And I should see "The TinyMCE Premium Accessibility Checker will override the default Accessibility Checker for users who have access to it."

  @javascript
  Scenario Outline: I can set service URL for the TinyMCE Premium plugins
    Given the following config values are set as admin:
      | apikey | "123456" | tiny_premium |
    And "Settings" "link" should exist in the "<plugin>" "table_row"
    When I click on "Settings" "link" in the "<plugin>" "table_row"
    Then I should see "The <plugin> plugin uses a server-side service to process data. You can choose to use the Tiny Cloud service, or connect to your own self-hosted instance using a service URL."
    And I should see "Use Tiny Cloud"
    And I should see "Use self-hosted service"
    And "Service URL" "field" should not be visible
    And I click on "Use self-hosted service" "radio"
    And "Service URL" "field" should be visible
    And I should see "Enter the URL of your self-hosted <plugin> service"
    And I set the field "Service URL" to "<serviceurl>"
    And I click on "Save changes" "button"
    And I should see "Changes saved"
    And I click on "Settings" "link" in the "<plugin>" "table_row"
    And "Service URL" "field" should be visible
    And the field "Service URL" matches value "<serviceurl>"

    Examples:
      | plugin                 | serviceurl                                 |
      | Enhanced Image Editing | http://moodle.test:8080/ephox-image-proxy  |
      | Link Checker           | http://moodle.test:8080/ephox-hyperlinking |
      | Spell Checker Pro      | http://moodle.test:8080/ephox-spelling     |
