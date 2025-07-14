@tool @tool_mfa @factor_sms
Feature: Set up SMS factor when relevant gateway is not configured
  In order configure the SMS factor
  As an admin
  I want to be directed to the SMS gateway setup when necessary

  Scenario: Configuring gateways from the SMS factor settings
    Given I log in as "admin"
    And the following config values are set as admin:
      | enabled | 1 | tool_mfa |
      | lockout | 3 | tool_mfa |
    And the following config values are set as admin:
      | enabled  | 1    | factor_sms |
      | weight   | 100  | factor_sms |
      | duration | 1800 | factor_sms |
    When I navigate to "Plugins > Admin tools > Multi-factor authentication" in site administration
    And I follow "Edit settings for the SMS factor"
    Then I should see "To use SMS as an authentication factor, you first need to set up an SMS gateway."
    And I should see "set up an SMS gateway"
    And I follow "set up an SMS gateway"
    And I should see "Create new SMS gateway"
    And I set the following fields to these values:
      | SMS gateway provider      | AWS               |
      | Gateway name              | First AWS gateway |
      | Default country code      | 61                |
      | Access key                | key123            |
      | Secret access key         | secret456         |
      | Amazon API gateway region | ap-southeast-2    |
    And I press "Save changes"
    And I should see "SMS"
    And the "SMS gateway" select box should contain "First AWS gateway (AWS)"
    And I follow "create a new gateway"
    And I should see "Create new SMS gateway"
    And I set the following fields to these values:
      | SMS gateway provider      | AWS               |
      | Gateway name              | Second one        |
      | Default country code      | 1                 |
      | Access key                | key1234           |
      | Secret access key         | secret4567        |
      | Amazon API gateway region | ap-southeast-2    |
    And I press "Save changes"
    And I should see "SMS"
    And the "SMS gateway" select box should contain "First AWS gateway (AWS)"
    And the "SMS gateway" select box should contain "Second one (AWS)"
