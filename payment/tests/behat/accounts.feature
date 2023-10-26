@core @core_payment
Feature: Manage payment accounts

  @javascript
  Scenario: Creating and editing payment account
    When I log in as "admin"
    And I navigate to "Payments > Payment accounts" in site administration
    And I follow "Manage payment gateways"
    Then "Australian Dollar" "text" should exist in the "PayPal" "table_row"
    And I follow "Payment accounts"
    And I press "Create payment account"
    And I set the field "Account name" to "TestAccount"
    And I press "Save changes"
    And I should see "PayPal" in the "TestAccount" "table_row"
    And I open the action menu in "TestAccount" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "Account name" to "NewName"
    And I press "Save changes"
    And I should see "PayPal" in the "NewName" "table_row"
    And I should not see "TestAccount"
    And I log out

  @javascript
  Scenario: Configuring gateways on payment accounts
    Given the following "core_payment > payment accounts" exist:
      | name           |
      | Account1       |
      | Account2       |
    When I log in as "admin"
    And I navigate to "Payments > Payment accounts" in site administration
    Then I should see "Not available" in the "Account1" "table_row"
    And I click on "PayPal" "link" in the "Account1" "table_row"
    And I set the field "Brand name" to "Test paypal"
    And I set the following fields to these values:
      | Brand name | Test paypal |
      | Client ID  | Test        |
      | Secret     | Test        |
      | Enable     | 1           |
    And I press "Save changes"
    And I should see "PayPal" in the "Account1" "table_row"
    And I should not see "Not available" in the "Account1" "table_row"
    And I log out

  @javascript
  Scenario: Deleting payment accounts
    Given the following "core_payment > payment accounts" exist:
      | name           |
      | Account1       |
      | Account2       |
    When I log in as "admin"
    And I navigate to "Payments > Payment accounts" in site administration
    And I open the action menu in "Account1" "table_row"
    And I choose "Delete or archive" in the open action menu
    And I click on "Yes" "button" in the "Confirmation" "dialogue"
    Then I should not see "Account1"
    And I should see "Account2"
    And I log out

  @javascript
  Scenario: Archiving and restoring accounts
    Given the following "users" exist:
      | username |
      | user1    |
    And the following "core_payment > payment accounts" exist:
      | name           |
      | Account1       |
      | Account2       |
    And the following "core_payment > payments" exist:
      | account  | component | amount | user  |
      | Account1 | test      | 10     | user1 |
      | Account1 | test      | 15     | user1 |
    When I log in as "admin"
    And I navigate to "Payments > Payment accounts" in site administration
    And I open the action menu in "Account1" "table_row"
    And I choose "Delete or archive" in the open action menu
    And I click on "Yes" "button" in the "Confirmation" "dialogue"
    Then I should not see "Account1"
    And I should see "Account2"
    And I follow "Show archived"
    And I should see "Archived" in the "Account1" "table_row"
    And I open the action menu in "Account1" "table_row"
    And I choose "Restore" in the open action menu
    And I should not see "Archived" in the "Account1" "table_row"
    And I log out
