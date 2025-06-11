@tool @tool_messageinbound
Feature: Incoming mail configuration
  In order to receive email from external
  As a Moodle administrator
  I need to set mail configuration

  Background:
    Given I log in as "admin"

  Scenario: Incoming mail server settings without OAuth 2 service setup yet
    Given I navigate to "Server > Email > Incoming mail configuration" in site administration
    And "OAuth 2 service" "select" should not exist

  Scenario: Incoming mail server settings with OAuth 2 service setup
    Given I navigate to "Server > OAuth 2 services" in site administration
    And I press "Google"
    And I should see "Create new service: Google"
    And I set the following fields to these values:
      | Name          | Testing service   |
      | Client ID     | thisistheclientid |
      | Client secret | supersecret       |
    And I press "Save changes"
    When I navigate to "Server > Email > Incoming mail configuration" in site administration
    Then "OAuth 2 service" "select" should exist
    And I should see "Testing service" in the "OAuth 2 service" "select"

  @javascript
  Scenario: Check character limitations of mailbox name
    When I navigate to "Server > Email > Incoming mail configuration" in site administration
    And I set the field "Mailbox name" to "frogfrogfrogfrog"
    Then I should see "Maximum of 15 characters"
    And the "disabled" attribute of "form#adminsettings button[type='submit']" "css_element" should contain "true"
    And I set the field "Mailbox name" to "frogfrogfrogfro"
    And I should not see "Maximum of 15 characters"
    And the "disabled" attribute of "form#adminsettings button[type='submit']" "css_element" should not be set
