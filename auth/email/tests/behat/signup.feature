@auth @auth_email
Feature: User must accept policy when logging in and signing up
  In order to record user agreement to use the site
  As a user
  I need to be able to accept site policy during sign up

  Scenario: Accept policy on sign up, no site policy
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then I should not see "I understand and agree"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site (edit their profile).
    And I log in as "user1"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"

  Scenario: Accept policy on sign up, with site policy
    Given the following config values are set as admin:
      | registerauth    | email              |
      | passwordpolicy  | 0                  |
      | sitepolicy      | https://moodle.org |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then the field "I understand and agree" matches value "0"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
      | I understand and agree | 1            |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user is not asked to agree to site policy again after the next login.
    And I log in as "user1"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"

  Scenario Outline: Email validation during email registration
    Given the following config values are set as admin:
      | allowaccountssameemail | <allowsameemail> |
      | registerauth           | email              |
      | passwordpolicy         | 0                  |
    And the following "users" exist:
      | username | firstname | lastname | email          |
      | s1       | John      | Doe      | s1@example.com |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    And I set the following fields to these values:
      | Username      | s2      |
      | Password      | test    |
      | Email address | <email1> |
      | Email (again) | <email2> |
      | First name    | Jane    |
      | Surname       | Doe     |
    And I press "Create my new account"
    Then I should <expect> "This email address is already registered. Perhaps you created an account in the past?"
    And I should <expect2> "Invalid email address"

    Examples:
      | allowsameemail | email1         | email2         | expect  | expect2 |
      | 0              | s1@example.com | s1@example.com | see     | not see |
      | 0              | S1@EXAMPLE.COM | S1@EXAMPLE.COM | see     | not see |
      | 0              | s1@example.com | S1@EXAMPLE.COM | see     | not see |
      | 0              | s2@example.com | s1@example.com | not see | see     |
      | 1              | s1@example.com | s1@example.com | not see | not see |
      | 1              | S1@EXAMPLE.COM | S1@EXAMPLE.COM | not see | not see |
      | 1              | s1@example.com | S1@EXAMPLE.COM | not see | not see |
      | 1              | s1@example.com | s2@example.com | not see | see     |
