@tool @tool_mfa @factor_sms
Feature: Login user with sms authentication factor
  In order to login using SMS factor authentication
  As an user
  I need to be able to login

  Background:
    Given I log in as "admin"
    And the following "core_sms > sms_gateways" exist:
      | name          | classname              | enabled | config                                                                                                         |
      | Dummy gateway | smsgateway_aws\gateway | 1       | {"countrycode":"+61", "gateway":"aws_sns", "api_region":"ap-southeast-2", "api_key":"abc", "api_secret":"123"} |
    And the following config values are set as admin:
      | enabled | 1 | tool_mfa |
      | lockout | 3 | tool_mfa |
    And the following config values are set as admin:
      | enabled  | 1    | factor_sms |
      | weight   | 100  | factor_sms |
      | duration | 1800 | factor_sms |
    And I navigate to "Plugins > Admin tools > Multi-factor authentication" in site administration
    And I follow "Edit settings for the SMS mobile phone factor"
    And I set the field "SMS gateway" to "Dummy gateway (AWS)"
    And I press "Save changes"
    And I should see "Changes saved"
    # Set up user SMS factor in user preferences.
    When I follow "Preferences" in the user menu
    And I click on "Multi-factor authentication preferences" "link"
    And I click on "Set up" "button"
    And I set the field "Mobile number" to "+34649709233"
    And I press "Send code"
    And I set the field "Enter code" with valid code
    Then I press "Save"

  Scenario: Login user successfully with sms verification
    Given I log out
    And I log in as "admin"
    And I should see "2-step verification"
    And I should see "Enter code"
    When I set the field "Enter code" with valid code
    And I click on "Continue" "button"
    Then I am logged in as "admin"

  Scenario: Wrong code number end of possible attempts
    Given I log out
    And I log in as "admin"
    And I should see "2-step verification"
    And I should see "Enter code"
    When I set the field "Enter code" to "555556"
    And I click on "Continue" "button"
    And I should see "Wrong code."
    And I should see "You have 2 attempts left."
    And I set the field "Enter code" to "555553"
    And I click on "Continue" "button"
    And I should see "Wrong code."
    And I should see "1 attempts left."
    And I set the field "Enter code" to "555553"
    And I click on "Continue" "button"
    Then I should see "Unable to authenticate"
