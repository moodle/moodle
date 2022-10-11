@tool @tool_messageinbound
Feature: Incoming mail configuration
  In order to receive email from external
  As a Moodle administrator
  I need to set mail configuration

  Background:
    Given I log in as "admin"

  Scenario: Incoming mail server settings without OAuth 2 Service setup yet
    Given I navigate to "Server > Email > Incoming mail configuration" in site administration
    And "OAuth 2 Service" "select" should not exist

  Scenario: Incoming mail server settings with OAuth 2 Service setup
    Given I navigate to "Server > OAuth 2 services" in site administration
    And I press "Google"
    And I should see "Create new service: Google"
    And I set the following fields to these values:
      | Name          | Testing service   |
      | Client ID     | thisistheclientid |
      | Client secret | supersecret       |
    And I press "Save changes"
    When I navigate to "Server > Email > Incoming mail configuration" in site administration
    Then "OAuth 2 Service" "select" should exist
    And I should see "Testing service" in the "OAuth 2 Service" "select"
