@core
Feature: Login using email address
  Users should be able to access their site
  As a user
  I should be able to login using email

  Background:
    Given the following "users" exist:
      | username | password | firstname | lastname | email            |
      | testuser | test     | Test      | User     | user@example.com |

  # The icon assertions below check for specific FontAwesome CSS class names (.fa-user, .fa-envelope,
  # .fa-lock). This intentionally couples the test to the current icon implementation so that any
  # change to the icon library or glyph choice is caught by CI rather than silently ignored.
  Scenario Outline: A user can login using their email address
    Given the following config values are set as admin:
      | authloginviaemail | <authloginviaemail> |
    When I am on homepage
    Then "<usernameiconselector>" "css_element" should exist
    And "<otherusernameiconselector>" "css_element" should not exist
    And ".login-form-password .login-input-icon .fa-lock" "css_element" should exist
    And I set the field "Username" to "<login>"
    And I set the field "Password" to "test"
    And I press "Log in"
    Then I should see "<message>"

    Examples:
      | authloginviaemail | usernameiconselector                                | otherusernameiconselector                           | login            | message              |
      | 0                 | .login-form-username .login-input-icon .fa-user     | .login-form-username .login-input-icon .fa-envelope | testuser         | You are logged in as |
      | 0                 | .login-form-username .login-input-icon .fa-user     | .login-form-username .login-input-icon .fa-envelope | user@example.com | Unable to log in     |
      | 1                 | .login-form-username .login-input-icon .fa-envelope | .login-form-username .login-input-icon .fa-user     | testuser         | You are logged in as |
      | 1                 | .login-form-username .login-input-icon .fa-envelope | .login-form-username .login-input-icon .fa-user     | user@example.com | You are logged in as |
