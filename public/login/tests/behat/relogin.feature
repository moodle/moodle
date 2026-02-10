@core
Feature: Relogin prevention
  User will be informed when trying to login again while already logged in
  As a user
  I should be informed that I am already logged in

  Background:
    Given the following "users" exist:
      | username | password | firstname | lastname | email            |
      | testuser | test     | Test      | User     | user@example.com |

  Scenario: A logged in user tries to access the login page again
    Given I follow "Log in"
    And I set the field "Username" to "testuser"
    And I set the field "Password" to "test"
    And I press "Log in"
    And I should see "You are logged in as"
    When I visit "/login"
    Then I should see "You are already logged in as Test User, you need to log out before logging in as different user." in the "Log out?" "dialogue"
    And "Go back to site" "button" should exist in the "Log out?" "dialogue"
    And "Log out" "button" should exist in the "Log out?" "dialogue"
