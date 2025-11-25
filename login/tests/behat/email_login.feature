@core
Feature: Login using email address
  Users should be able to access their site
  As a user
  I should be able to login using email

  Background:
    Given the following "users" exist:
      | username | password | firstname | lastname | email            |
      | testuser | test     | Test      | User     | user@example.com |

  Scenario Outline: A user can login using their email address
    Given the following config values are set as admin:
      | authloginviaemail | <authloginviaemail> |
    When I follow "Log in"
    And I set the field "Username" to "<login>"
    And I set the field "Password" to "test"
    And I press "Log in"
    Then I should see "<message>"

    Examples:
      | authloginviaemail | login            | message                         |
      | 0                 | testuser         | You are logged in as            |
      | 0                 | user@example.com | Invalid login, please try again |
      | 1                 | testuser         | You are logged in as            |
      | 1                 | user@example.com | You are logged in as            |
