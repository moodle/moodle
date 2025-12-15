@tool @tool_oauth2 @external
Feature: Basic OAuth2 functionality
  In order to use them later for authentication or repository plugins
  As an administrator
  I need to add a manage customised OAuth2 services.

  Background:
    Given I log in as "admin"
    And I change window size to "large"
    And I navigate to "Server > OAuth 2 services" in site administration

  Scenario: Create, edit and delete standard service for Google
    Given I press "Google"
    And I should see "Create new service: Google"
    And I set the following fields to these values:
      | Name                       | Testing service                           |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
    When I press "Save changes"
    Then I should see "Changes saved"
    And I should see "Testing service"
    And "Allow login" "icon" should exist in the "Testing service" "table_row"
    And "Allow services" "icon" should exist in the "Testing service" "table_row"
    And "Service discovery successful" "icon" should exist in the "Testing service" "table_row"
    And I click on "Configure endpoints" "link" in the "Testing service" "table_row"
    And I should see "https://accounts.google.com/.well-known/openid-configuration" in the "discovery_endpoint" "table_row"
    And I should see "authorization_endpoint"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Configure user field mappings" "link" in the "Testing service" "table_row"
    And I should see "firstname" in the "given_name" "table_row"
    And I should see "middlename" in the "middle_name" "table_row"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Edit" "link" in the "Testing service" "table_row"
    And I set the following fields to these values:
      | Name                       | Testing service modified                 |
    And I press "Save changes"
    And I should see "Changes saved"
    And I should see "Testing service modified"
    And I click on "Delete" "link" in the "Testing service modified" "table_row"
    And I should see "Are you sure you want to delete the identity issuer \"Testing service modified\"?"
    And I press "Continue"
    And I should see "Identity issuer deleted"
    And I should not see "Testing service modified"

  Scenario: Create, edit and delete standard service for Microsoft
    Given I press "Microsoft"
    And I should see "Create new service: Microsoft"
    And I set the following fields to these values:
      | Name                       | Testing service                           |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
    When I press "Save changes"
    Then I should see "Changes saved"
    And I should see "Testing service"
    And "Allow login" "icon" should exist in the "Testing service" "table_row"
    And "Allow services" "icon" should exist in the "Testing service" "table_row"
    And "Service discovery successful" "icon" should exist in the "Testing service" "table_row"
    And I click on "Configure endpoints" "link" in the "Testing service" "table_row"
    And I should see "authorization_endpoint"
    And I should see "discovery_endpoint"
    And I should see "device_authorization_endpoint"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Configure user field mappings" "link" in the "Testing service" "table_row"
    And I should see "firstname" in the "given_name" "table_row"
    And I should see "lastname" in the "family_name" "table_row"
    And I should see "idnumber" in the "sub" "table_row"
    And I should see "email" in the "email" "table_row"
    And I should see "lang" in the "locale" "table_row"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Edit" "link" in the "Testing service" "table_row"
    And I set the following fields to these values:
      | Name                       | Testing service modified                 |
    And I press "Save changes"
    And I should see "Changes saved"
    And I should see "Testing service modified"
    And I click on "Delete" "link" in the "Testing service modified" "table_row"
    And I should see "Are you sure you want to delete the identity issuer \"Testing service modified\"?"
    And I press "Continue"
    And I should see "Identity issuer deleted"
    And I should not see "Testing service modified"

  Scenario: Create, edit and delete standard service for Facebook
    Given I press "Facebook"
    And I should see "Create new service: Facebook"
    And I set the following fields to these values:
      | Name                       | Testing service                           |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
    When I press "Save changes"
    Then I should see "Changes saved"
    And I should see "Testing service"
    And "Allow login" "icon" should exist in the "Testing service" "table_row"
    And "Allow services" "icon" should exist in the "Testing service" "table_row"
    And I should see "-" in the "Testing service" "table_row"
    And I click on "Configure endpoints" "link" in the "Testing service" "table_row"
    And I should see "authorization_endpoint"
    And I should not see "discovery_endpoint"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Configure user field mappings" "link" in the "Testing service" "table_row"
    And I should see "firstname" in the "first_name" "table_row"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Edit" "link" in the "Testing service" "table_row"
    And I set the following fields to these values:
      | Name                       | Testing service modified                 |
    And I press "Save changes"
    And I should see "Changes saved"
    And I should see "Testing service modified"
    And I click on "Delete" "link" in the "Testing service modified" "table_row"
    And I should see "Are you sure you want to delete the identity issuer \"Testing service modified\"?"
    And I press "Continue"
    And I should see "Identity issuer deleted"
    And I should not see "Testing service modified"

  @javascript
  Scenario: Create, edit and delete standard service for Nextcloud
    Given I press "Nextcloud"
    And I should see "Create new service: Nextcloud"
    And I set the following fields to these values:
      | Name                       | Testing service                           |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
    And I press "Save changes"
    And I should see "You must supply a value here."
    And I set the following fields to these values:
      | Service base URL           | https://dummy.local/nextcloud/            |
    When I press "Save changes"
    Then I should see "Changes saved"
    And I should see "Testing service"
    And "Do not allow login" "icon" should exist in the "Testing service" "table_row"
    And "Allow services" "icon" should exist in the "Testing service" "table_row"
    And I should see "-" in the "Testing service" "table_row"
    And I click on "Configure endpoints" "link" in the "Testing service" "table_row"
    And I should see "authorization_endpoint"
    And I should not see "discovery_endpoint"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Configure user field mappings" "link" in the "Testing service" "table_row"
    And I should see "username" in the "ocs-data-id" "table_row"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Edit" "link" in the "Testing service" "table_row"
    And I set the following fields to these values:
      | Name                       | Testing service modified                 |
    And I press "Save changes"
    And I should see "Testing service modified"
    And I click on "Delete" "link" in the "Testing service modified" "table_row"
    And I should see "Are you sure you want to delete the identity issuer \"Testing service modified\"?"
    And I press "Continue"
    And I should see "Identity issuer deleted"
    And I should not see "Testing service modified"

  Scenario: Create, edit and delete valid custom OIDC service
    Given I press "Custom"
    And I should see "Create new service: Custom"
    And I set the following fields to these values:
      | Name                       | Google custom                             |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
      | Service base URL           | https://accounts.google.com/              |
    When I press "Save changes"
    Then I should see "Changes saved"
    And I should see "Google custom"
    And "Do not allow login" "icon" should exist in the "Google custom" "table_row"
    And "Allow services" "icon" should exist in the "Google custom" "table_row"
    And "Service discovery successful" "icon" should exist in the "Google custom" "table_row"
    And the "src" attribute of "table.admintable th img" "css_element" should contain "favicon.ico"
    And I click on "Configure endpoints" "link" in the "Google custom" "table_row"
    And I should see "https://accounts.google.com/.well-known/openid-configuration" in the "discovery_endpoint" "table_row"
    And I should see "authorization_endpoint"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Configure user field mappings" "link" in the "Google custom" "table_row"
    And I should see "firstname" in the "given_name" "table_row"
    And I should see "middlename" in the "middle_name" "table_row"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Edit" "link" in the "Google custom" "table_row"
    And I set the following fields to these values:
      | Name                       | Google custom modified                     |
    And I press "Save changes"
    And I should see "Changes saved"
    And I should see "Google custom modified"
    And I click on "Delete" "link" in the "Google custom modified" "table_row"
    And I should see "Are you sure you want to delete the identity issuer \"Google custom modified\"?"
    And I press "Continue"
    And I should see "Identity issuer deleted"
    And I should not see "Google custom modified"

  Scenario: Create, edit and delete invalid custom OIDC service
    Given I press "Custom"
    And I should see "Create new service: Custom"
    And I set the following fields to these values:
      | Name                       | Invalid custom service                    |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
      | Service base URL           | http://dc.imsglobal.org/                 |
    When I press "Save changes"
    Then I should see "For security reasons only https connections are allowed, sorry"
    And I set the following fields to these values:
      | Service base URL           | https://dc.imsglobal.org/                 |
    And I press "Save changes"
    And I should see "Could not discover end points for identity issuer: Invalid custom service"
    And I should see "URL: https://dc.imsglobal.org/.well-known/openid-configuration"
    And "Allow services" "icon" should exist in the "Invalid custom service" "table_row"
    And "Do not allow login" "icon" should exist in the "Invalid custom service" "table_row"
    And I should see "-" in the "Invalid custom service" "table_row"
    And I click on "Configure endpoints" "link" in the "Invalid custom service" "table_row"
    And I should not see "discovery_endpoint"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Configure user field mappings" "link" in the "Invalid custom service" "table_row"
    And I should not see "given_name"
    And I should not see "middle_name"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Edit" "link" in the "Invalid custom service" "table_row"
    And I set the following fields to these values:
      | Name                       | Valid custom service                        |
      | Service base URL           | https://accounts.google.com/                |
    And I press "Save changes"
    And "Do not allow login" "icon" should exist in the "Valid custom" "table_row"
    And "Allow services" "icon" should exist in the "Valid custom" "table_row"
    And I should see "-" in the "Valid custom" "table_row"
    And I click on "Edit" "link" in the "Valid custom service" "table_row"
    And I set the following fields to these values:
      | Name                       | Invalid custom service                    |
      | Service base URL           | https://dc.imsglobal.org/                 |
    And I press "Save changes"
    And I should see "-" in the "Invalid custom service" "table_row"
    And I click on "Delete" "link" in the "Invalid custom service" "table_row"
    And I should see "Are you sure you want to delete the identity issuer \"Invalid custom service\"?"
    And I press "Continue"
    And I should see "Identity issuer deleted"
    And I should not see "Invalid custom service"

  Scenario: Create, edit and delete empty custom OIDC service
    Given I press "Custom"
    And I should see "Create new service: Custom"
    And I set the following fields to these values:
      | Name                       | Empty custom service                      |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
    When I press "Save changes"
    And I should see "Changes saved"
    And I should see "Empty custom service"
    And "Allow services" "icon" should exist in the "Empty custom service" "table_row"
    And "Do not allow login" "icon" should exist in the "Empty custom service" "table_row"
    And I should see "-" in the "Empty custom service" "table_row"
    And I click on "Configure endpoints" "link" in the "Empty custom service" "table_row"
    And I should not see "discovery_endpoint"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Configure user field mappings" "link" in the "Empty custom service" "table_row"
    And I should not see "given_name"
    And I should not see "middle_name"
    And I navigate to "Server > OAuth 2 services" in site administration
    And I click on "Edit" "link" in the "Empty custom service" "table_row"
    # Check it works as expected too without slash at the end of the service base URL.
    And I set the following fields to these values:
      | Name                       | Valid custom service                      |
      | Service base URL           | https://accounts.google.com               |
    And I press "Save changes"
    And "Do not allow login" "icon" should exist in the "Valid custom" "table_row"
    And "Allow services" "icon" should exist in the "Valid custom" "table_row"
    And I should see "-" in the "Valid custom" "table_row"
    And I click on "Edit" "link" in the "Valid custom service" "table_row"
    And I set the following fields to these values:
      | Name                       | Invalid custom service                    |
      | Service base URL           | https://dc.imsglobal.org/                 |
    And I press "Save changes"
    And I should see "-" in the "Invalid custom service" "table_row"
    And I click on "Edit" "link" in the "Invalid custom service" "table_row"
    And I set the following fields to these values:
      | Name                       | Empty custom service                      |
      | Service base URL           |                                           |
    And I press "Save changes"
    And I should see "Changes saved"
    And I should see "Empty custom service"
    And I click on "Delete" "link" in the "Empty custom service" "table_row"
    And I should see "Are you sure you want to delete the identity issuer \"Empty custom service\"?"
    And I press "Continue"
    And I should see "Identity issuer deleted"
    And I should not see "Empty custom service"

  Scenario: Create a standard service for Google and test form and UI for login only, services only and both
    Given I press "Google"
    And I should see "Create new service: Google"
    # Create using 'Login page only' option.
    And I set the following fields to these values:
      | Name                       | Testing service                           |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
      | This service will be used  | Login page only                           |
    When I press "Save changes"
    Then I should see "Changes saved"
    And I should see "Testing service"
    And "Allow login" "icon" should exist in the "Testing service" "table_row"
    And "Do not allow services" "icon" should exist in the "Testing service" "table_row"
    And "Service discovery successful" "icon" should exist in the "Testing service" "table_row"
    # Change to 'Internal services only'.
    And I click on "Edit" "link" in the "Testing service" "table_row"
    And I set the following fields to these values:
      | This service will be used  | Internal services only                     |
    And I press "Save changes"
    And I should see "Changes saved"
    And "Do not allow login" "icon" should exist in the "Testing service" "table_row"
    And "Allow services" "icon" should exist in the "Testing service" "table_row"
    # Change to 'Login page and internal services' and add a display name.
    And I click on "Edit" "link" in the "Testing service" "table_row"
    And I set the following fields to these values:
      | This service will be used         | Login page and internal services     |
      | Name displayed on the login page  | Google new display name              |
    And I press "Save changes"
    And I should see "Changes saved"
    And "Allow login" "icon" should exist in the "Testing service" "table_row"
    And "Allow services" "icon" should exist in the "Testing service" "table_row"
    And I should see "Google new display name" in the "Testing service" "table_row"

  Scenario: Create a login page only custom OIDC service
    Given I press "Custom"
    And I should see "Create new service: Custom"
    And I set the following fields to these values:
      | Name                              | Empty custom service                      |
      | Client ID                         | thisistheclientid                         |
      | Client secret                     | supersecret                               |
      | This service will be used         | Login page only                           |
      | Name displayed on the login page  | Custom display name                       |
    When I press "Save changes"
    And I should see "Changes saved"
    And I should see "Empty custom service"
    And I should see "Custom display name" in the "Empty custom service" "table_row"
    And "Not configured" "icon" should exist in the "Empty custom service" "table_row"
    And "Do not allow services" "icon" should exist in the "Empty custom service" "table_row"
    And I click on "Configure endpoints" "link" in the "Empty custom service" "table_row"
    And I press "Create new endpoint for issuer \"Empty custom service\""
    And I set the following fields to these values:
      | Name | userinfo_endpoint |
      | URL  | https://example.com/userinfo |
    And I press "Save changes"
    And I navigate to "Server > OAuth 2 services" in site administration
    And "Allow login" "icon" should exist in the "Empty custom service" "table_row"
    And "Do not allow services" "icon" should exist in the "Empty custom service" "table_row"

  @javascript
  Scenario: Changes to "Authenticate token requests via HTTP headers" are saved
    Given I press "Custom"
    And I set the following fields to these values:
      | Name                              | Custom service                     |
      | Client ID                         | thisistheclientid                  |
      | Client secret                     | supersecret                        |
    And I press "Save changes"
    When I click on "Edit" "link" in the "Custom service" "table_row"
    And I click on "Authenticate token requests via HTTP headers" "checkbox"
    And I press "Save changes"
    And I click on "Edit" "link" in the "Custom service" "table_row"
    And the field "Authenticate token requests via HTTP headers" matches value "1"
    And I click on "Authenticate token requests via HTTP headers" "checkbox"
    And I press "Save changes"
    And I click on "Edit" "link" in the "Custom service" "table_row"
    Then the field "Authenticate token requests via HTTP headers" matches value ""
