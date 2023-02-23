@core @core_admin @core_user
Feature: Allowing multiple accounts to have the same email address
  In order to manage user accounts
  As an admin
  I need to be able to set whether to allow multiple accounts with the same email or not

  Scenario Outline: Create a user with the same email as an existing user
    Given the following config values are set as admin:
      | allowaccountssameemail | <allowsameemail> |
    And the following "users" exist:
      | username  | firstname | lastname | email           |
      | s1        | John      | Doe      | s1@example.com  |
    When I log in as "admin"
    And I navigate to "Users > Accounts > Add a new user" in site administration
    And I set the following fields to these values:
      | Username      | s2      |
      | First name    | Jane    |
      | Last name     | Doe     |
      | Email address | <email> |
      | New password  | test    |
    And I press "Create user"
    Then I should <expect> "This email address is already registered."

    Examples:
      | allowsameemail | email          | expect  |
      | 0              | s1@example.com | see     |
      | 0              | S1@EXAMPLE.COM | see     |
      | 1              | s1@example.com | not see |
      | 1              | S1@EXAMPLE.COM | not see |

  Scenario Outline: Update a user with the same email as an existing user
    Given the following config values are set as admin:
      | allowaccountssameemail | <allowsameemail> |
    And the following "users" exist:
      | username  | firstname | lastname | email           |
      | s1        | John      | Doe      | s1@example.com  |
      | s2        | Jane      | Doe      | s2@example.com  |
    When I am on the "s2" "user > editing" page logged in as "admin"
    And I set the field "Email address" to "<email>"
    And I press "Update profile"
    Then I should <expect> "This email address is already registered."

    Examples:
      | allowsameemail | email          | expect  |
      | 0              | s1@example.com | see     |
      | 0              | S1@EXAMPLE.COM | see     |
      | 1              | s1@example.com | not see |
      | 1              | S1@EXAMPLE.COM | not see |
      | 0              | S2@EXAMPLE.COM | not see |
      | 1              | S2@EXAMPLE.COM | not see |

  Scenario Outline: Update own user profile with the same email as an existing user
    Given the following config values are set as admin:
      | allowaccountssameemail | <allowsameemail> |
    And the following "users" exist:
      | username  | firstname | lastname | email           |
      | s1        | John      | Doe      | s1@example.com  |
      | s2        | Jane      | Doe      | s2@example.com  |
    When I log in as "s2"
    And I open my profile in edit mode
    And I set the field "Email address" to "<email>"
    And I press "Update profile"
    Then I should <expect> "This email address is already registered."

    Examples:
      | allowsameemail | email          | expect  |
      | 0              | s1@example.com | see     |
      | 0              | S1@EXAMPLE.COM | see     |
      | 1              | s1@example.com | not see |
      | 1              | S1@EXAMPLE.COM | not see |
      | 0              | S2@EXAMPLE.COM | not see |
      | 1              | S2@EXAMPLE.COM | not see |
