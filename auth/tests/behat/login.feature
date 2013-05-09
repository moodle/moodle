@core @core_auth
Feature: Authentication
  In order to validate my credentials in the system
  As a user
  I need to log into the system

  Scenario: Login with the predefined admin user
    Given I log in as "admin"

  Scenario: Login as an existing admin user filling the form
    Given the following "users" exists:
      | username | password | firstname | lastname | email |
      | testuser | testuser | Test | User | moodle@moodlemoodle.com |
    And I am on homepage
    When I follow "Login"
    And I fill in "Username" with "testuser"
    And I fill in "Password" with "testuser"
    And I press "Login"
    Then I should see "You are logged in as"

  Scenario: Login as an unexisting user filling the form
    Given the following "users" exists:
      | username | password | firstname | lastname | email |
      | testuser | testuser | Test | User | moodle@moodlemoodle.com |
    And I am on homepage
    When I follow "Login"
    And I fill in "Username" with "testuser"
    And I fill in "Password" with "unexisting"
    And I press "Login"
    Then I should see "Invalid login, please try again"

  Scenario: Logout
    Given I log in as "admin"
    When I log out
    Then I should see "You are not logged in"
