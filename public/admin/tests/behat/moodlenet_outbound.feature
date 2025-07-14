@core @core_admin
Feature: MoodleNet outbound configuration
  In order to send activity/resource to MoodleNet
  As a Moodle administrator
  I need to set outbound configuration

  Background:
    Given I log in as "admin"

  Scenario: Share to MoodleNet experimental flag
    Given I navigate to "Development > Experimental" in site administration
    Then "Enable sharing to MoodleNet" "field" should exist
    And the field "Enable sharing to MoodleNet" matches value "0"

  Scenario: Outbound configuration without experimental flag enable yet
    Given I navigate to "MoodleNet" in site administration
    Then I should not see "MoodleNet outbound settings"

  Scenario: Outbound configuration without OAuth 2 service setup yet
    Given the following config values are set as admin:
      | enablesharingtomoodlenet | 1 |
    When I navigate to "MoodleNet" in site administration
    Then I should see "MoodleNet outbound settings"
    And I click on "MoodleNet outbound settings" "link"
    And the field "OAuth 2 service" matches value "None"
    And I should see "Select a MoodleNet OAuth 2 service to enable sharing to that MoodleNet site. If the service doesn't exist yet, you will need to create it."
    And I click on "create" "link"
    And I should see "OAuth 2 services"

  Scenario: Outbound configuration with OAuth 2 service setup
    Given a MoodleNet mock server is configured
    And the following config values are set as admin:
      | enablesharingtomoodlenet | 1 |
    And I navigate to "Server > OAuth 2 services" in site administration
    And I press "Custom"
    And I should see "Create new service: Custom"
    And I set the following fields to these values:
      | Name          | Testing custom service   |
      | Client ID     | thisistheclientid |
      | Client secret | supersecret       |
    And I press "Save changes"
    When I navigate to "MoodleNet > MoodleNet outbound settings" in site administration
    Then the field "OAuth 2 service" matches value "None"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I press "MoodleNet"
    And I should see "Create new service: MoodleNet"
    And I change the MoodleNet field "Service base URL" to mock server
    And I press "Save changes"
    And I navigate to "MoodleNet > MoodleNet outbound settings" in site administration
    And the "OAuth 2 service" "field" should be enabled
    And I should see "MoodleNet" in the "OAuth 2 service" "select"
    And I should not see "Testing custom service" in the "OAuth 2 service" "select"
