@auth @auth_oauth2 @javascript
Feature: OAuth2 settings test functionality
  In order to use them later for authentication
  As an administrator
  I need to be able to test configured OAuth2 login services.

  Background:
    Given I log in as "admin"
    And I change window size to "large"

  Scenario: Test oAuth2 authentication settings with no configured service.
    Given I navigate to "Plugins > Authentication > Manage authentication" in site administration
    And I click on "Test settings" "link" in the "OAuth 2" "table_row"
    Then I should see "There are no configured OAuth2 providers"

  Scenario: Test oAuth2 authentication settings for a configured service.
    Given I navigate to "Server > OAuth 2 services" in site administration
    And I press "Google"
    And I set the following fields to these values:
      | Name                       | Testing service                           |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
    And I press "Save changes"
    And I navigate to "Plugins > Authentication > Manage authentication" in site administration
    And I click on "Test settings" "link" in the "OAuth 2" "table_row"
    Then I should see "Testing service"
