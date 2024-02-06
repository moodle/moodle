@tool @tool_mfa @factor_sms
Feature: Login user with sms authentication factor
  In order to login using SMS factor authentication
  As an user
  I need to be able to login

  Background:
    Given I log in as "admin"
    And the following config values are set as admin:
      | enabled | 1 | tool_mfa |
      | lockout | 3 | tool_mfa |
    And the following config values are set as admin:
      | enabled | 1 | factor_sms |
    # Set up user SMS factor in user preferences.
    When I follow "Preferences" in the user menu
    And I click on "Multi-factor authentication preferences" "link"
    And I click on "Set up SMS" "button"
    And I set the field "Mobile number" to "+34649709233"
    And I press "Send code"
    And I set the field "Enter code" with valid code
    Then I press "Save"

  Scenario: Revoke factor
    Given I click on "Revoke" "link"
    And I should see "Are you sure you want to revoke factor?"
    And I press "Revoke"
    And I should see "successfully revoked"
    When I log out
    And I log in as "admin"
    Then I should see "Unable to authenticate"

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
