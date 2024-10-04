@tool @tool_oauth2 @external @javascript
Feature: OAuth2 email verification
  In order to make sure administrators understand the ramifications of email verification
  As an administrator
  I should see email verifications notifications when configuring an Oauth2 provider.

  Background:
    Given I log in as "admin"
    And I change window size to "large"
    And I navigate to "Server > OAuth 2 services" in site administration

  Scenario: Create, edit and delete standard service for Google toggling email verification.
    Given I press "Google"
    And I should see "Create new service: Google"
    And I set the following fields to these values:
      | Name                       | Testing service                           |
      | Client ID                  | thisistheclientid                         |
      | Client secret              | supersecret                               |
    Then I should not see "I understand that disabling email verification can be a security issue."
    And I click on "Require email verification" "checkbox"
    And I should see "I understand that disabling email verification can be a security issue."
    And I click on "I understand that disabling email verification can be a security issue." "checkbox"
    And I press "Save changes"
    And I should see "Changes saved"
    And I click on "Edit" "link" in the "Testing service" "table_row"
    And I press "Save changes"
    And I should see "Required"
    And I click on "Require email verification" "checkbox"
    And I press "Save changes"
    And I should see "Changes saved"
