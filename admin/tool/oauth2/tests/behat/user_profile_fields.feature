@tool @tool_oauth2 @javascript

Feature: OAuth2 user profile fields functionality
  In order to use them later for authentication or repository plugins
  As an administrator
  I need to be able to map data fields provided by an Oauth2 provider
  to custom user profile fields defined by an administrator.

  Background:
    Given the following "custom profile fields" exist:
    | datatype | shortname      | name            | locked  |
    | text     | unlocked_field | Unlocked field  | 0       |
    | text     | locked_field   | Locked field    | 1       |
    And I log in as "admin"
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

    # Create unlocked field
    And I click on "Create new user field mapping for issuer \"Testing service\"" "button"
    And I set the following fields to these values:
      | External field name | External unlocked             |
      | Internal field name | Unlocked field |
    And I click on "Save changes" "button"
    And I should see "unlocked_field"

    # Create locked field
    And I click on "Create new user field mapping for issuer \"Testing service\"" "button"
    And I set the following fields to these values:
      | External field name | External locked      |
      | Internal field name | Locked field |
    And I click on "Save changes" "button"
    And I should see "locked_field"
