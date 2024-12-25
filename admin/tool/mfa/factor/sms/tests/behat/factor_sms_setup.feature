@tool @tool_mfa @factor_sms
Feature: Set up SMS factor in user preferences
  In order check the SMS factor verification
  As an admin
  I want to setup and enable the SMS factor for the current user

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > SMS > Manage SMS gateways" in site administration
    And I follow "Create new SMS gateway"
    And I set the following fields to these values:
      | SMS gateway provider | AWS           |
      | Gateway name         | Dummy gateway |
      | Access key           | key123        |
      | Secret access key    | secret456     |
    And I press "Save changes"
    And the following config values are set as admin:
      | enabled | 1 | tool_mfa |
      | lockout | 3 | tool_mfa |
    And the following config values are set as admin:
      | enabled    | 1      | factor_sms |
      | weight     | 100    | factor_sms |
      | duration   | 1800   | factor_sms |
    And I navigate to "Plugins > Admin tools > Multi-factor authentication" in site administration
    And I follow "Edit settings for the SMS factor"
    And I set the field "SMS gateway" to "Dummy gateway (AWS)"
    And I press "Save changes"
    And I should see "Changes saved"
    When I follow "Preferences" in the user menu
    And I click on "Multi-factor authentication preferences" "link"
    And I click on "Set up" "button"

  Scenario: Phone number setup form validation
    Given I set the field "Mobile number" to "++5555sss"
    And I press "Send code"
    And I should see "The phone number you provided is not in a valid format."
    And I set the field "Mobile number" to "0123456789"
    And I press "Send code"
    And I should see "The phone number you provided is not in a valid format."
    And I set the field "Mobile number" to "786-307-3615"
    And I press "Send code"
    And I should see "The phone number you provided is not in a valid format."
    When I set the field "Mobile number" to "649709233"
    And I press "Send code"
    Then I should see "The phone number you provided is not in a valid format."

  Scenario: Edit phone number
    Given I set the field "Mobile number" to "+34649709233"
    And I press "Send code"
    And I click on "Edit phone number" "link"
    And I should see "Mobile number"
    When I set the field "Mobile number" to "+34649709232"
    And I press "Send code"
    Then I should see "Enter code"

  Scenario: Code setup form validation
    Given I set the field "Mobile number" to "+34649709233"
    And I press "Send code"
    And I should see "Enter code"
    When I set the field "Enter code" to "555556"
    And I click on "Save" "button"
    And I should see "Wrong code. Try again"
    And I set the field "Enter code" to "ddddd5"
    And I click on "Save" "button"
    Then I should see "Wrong code. Try again"
