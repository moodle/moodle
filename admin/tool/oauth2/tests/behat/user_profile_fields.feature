@tool @tool_oauth2 @javascript

Feature: OAuth2 user profile fields functionality
  In order to use them later for authentication or repository plugins
  As an administrator
  I need to be able to map data fields provided by an Oauth2 provider
  to custom user profile fields defined by an administrator.

  Background:
    Given the following "users" exist:
      | username            | firstname           | lastname | email                           |
      | userwithinformation | userwithinformation | 1        | userwithinformation@example.com |
    And I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I click on "Create a new profile field" "link"
    And I click on "Text input" "link"
    And I set the following fields to these values:
      | Short name                    | test_shortname  |
      | Name                          | test field name |
    And I click on "Save changes" "button"
    And I navigate to "Server > OAuth 2 services" in site administration

  Scenario: Verify custom user profile field mapping
    Given I press "Microsoft"
    And I should see "Create new service: Microsoft"
    And I set the following fields to these values:
      | Name                       | Testing service                           |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
    When I press "Save changes"
    Then I should see "Changes saved"
    And I should see "Testing service"
    And I click on "Configure user field mappings" "link" in the "Testing service" "table_row"
    And I click on "Create new user field mapping for issuer \"Testing service\"" "button"
    And I set the following fields to these values:
      | External field name | sub             |
      | Internal field name | test field name |
    And I click on "Save changes" "button"
    And I should see "test_shortname"
