@core @core_admin
Feature: Enable multiple accounts to have the same email address
  In order to have multiple accounts registerd on the system with the same email address
  As an admin
  I need to enable multiple accounts to be registered with the same email address and verify it is applied

  Background:
    Given I log in as "admin"

  Scenario: Enable registration of multiple accounts with the same email address
    Given the following config values are set as admin:
      | allowaccountssameemail | 1 |
    When I navigate to "Users > Accounts > Add a new user" in site administration
    And I set the following fields to these values:
      | Username                        | testmultiemailuser1             |
      | Choose an authentication method | Manual accounts                 |
      | New password                    | test@User1                      |
      | First name                      | Test                            |
      | Surname                         | Multi1                          |
      | Email address                   | testmultiemailuser@example.com  |
    And I press "Create user"
    And I should see "Test Multi1"
    And I press "Add a new user"
    And I set the following fields to these values:
      | Username                        | testmultiemailuser2             |
      | Choose an authentication method | Manual accounts                 |
      | New password                    | test@User2                      |
      | First name                      | Test                            |
      | Surname                         | Multi2                          |
      | Email address                   | testmultiemailuser@example.com  |
    And I press "Create user"
    Then I should see "Test Multi2"
    And I should not see "This email address is already registered"

  Scenario: Disable registration of multiple accounts with the same email address
    Given the following config values are set as admin:
      | allowaccountssameemail | 0 |
    When I navigate to "Users > Accounts > Add a new user" in site administration
    And I set the following fields to these values:
      | Username                        | testmultiemailuser1             |
      | Choose an authentication method | Manual accounts                 |
      | New password                    | test@User1                      |
      | First name                      | Test                            |
      | Surname                         | Multi1                          |
      | Email address                   | testmultiemailuser@example.com  |
    And I press "Create user"
    And I should see "Test Multi1"
    And I press "Add a new user"
    And I set the following fields to these values:
      | Username                        | testmultiemailuser2             |
      | Choose an authentication method | Manual accounts                 |
      | New password                    | test@User2                      |
      | First name                      | Test                            |
      | Surname                         | Multi2                          |
      | Email address                   | testmultiemailuser@example.com  |
    And I press "Create user"
    Then I should see "This email address is already registered"