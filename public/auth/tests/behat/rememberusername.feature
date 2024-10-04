@core @core_auth
Feature: Test the 'remember username' feature works.
  In order for users to easily log in to the site
  As a user
  I need the site to remember my username when the feature is enabled

  Background:
    Given the following "users" exist:
      | username |
      | teacher1 |

  # Given the user has logged in and selected 'Remember username', when they log in again, then their username should be remembered.
  Scenario: Check that 'remember username' works without javascript for teachers.
    # Log in the first time with $CFG->rememberusername set to Yes.
    Given the following config values are set as admin:
      | rememberusername | 1 |
    And I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    And I set the field "Username" to "teacher1"
    And I set the field "Password" to "teacher1"
    And I press "Log in"
    And I log out
    # Log out and check that the username was remembered.
    When I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    Then the field "username" matches value "teacher1"

  # Given the user has logged in before and selected 'Remember username', when they log in again and unset 'Remember username', then
  # their username should be forgotten for future log in attempts.
  Scenario: Check that 'remember username' unsetting works without javascript for teachers.
    # Log in the first time with $CFG->rememberusername set to Optional.
    Given the following config values are set as admin:
      | rememberusername | 2 |
    And I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    And I set the field "Username" to "teacher1"
    And I set the field "Password" to "teacher1"
    And I press "Log in"
    And I log out
    # Log in again, the username should have been remembered.
    When I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    Then the field "username" matches value "teacher1"
    And I set the field "Password" to "teacher1"
    And I press "Log in"
    And I log out
    And the following config values are set as admin:
      | rememberusername | 0 |
    # Check username has been forgotten.
    And I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    Then the field "username" matches value ""
