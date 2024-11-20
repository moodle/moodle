@core @core_admin
Feature: Outgoing mail configuration
  In order to send email from Moodle
  As a Moodle administrator
  I need to set mail configuration

  Background:
    Given I log in as "admin"

  Scenario: SMTP Auth Type without OAuth 2 service setup yet
    Given I navigate to "Server > Email > Outgoing mail configuration" in site administration
    And I should not see "XOAUTH2" in the "SMTP Auth Type" "select"
    And I should see "LOGIN" in the "SMTP Auth Type" "select"
    And I should see "PLAIN" in the "SMTP Auth Type" "select"

  Scenario: SMTP Auth Type with OAuth 2 service setup
    Given I navigate to "Server > OAuth 2 services" in site administration
    And I press "Google"
    And I should see "Create new service: Google"
    And I set the following fields to these values:
      | Name          | Testing service   |
      | Client ID     | thisistheclientid |
      | Client secret | supersecret       |
    And I press "Save changes"
    When I navigate to "Server > Email > Outgoing mail configuration" in site administration
    Then I should see "XOAUTH2" in the "SMTP Auth Type" "select"
    And I should see "LOGIN" in the "SMTP Auth Type" "select"
    And I should see "PLAIN" in the "SMTP Auth Type" "select"
    And I should see "Testing service" in the "OAuth 2 service" "select"
