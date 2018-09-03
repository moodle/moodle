@core @core_auth
Feature: Test the 'remember username' feature works.
  In order to see my saved username on the login form
  As a user
  I need to have logged in once before and clicked 'Remember username'

  Background:
    Given the following "users" exist:
      | username |
      | teacher1 |

  # Given the user has logged in and selected 'Remember username', when they log in again, then their username should be remembered.
  Scenario: Check that 'remember username' works without javascript for teachers.
    # Log in the first time and check the 'remember username' box.
    Given I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    And I set the field "Username" to "teacher1"
    And I set the field "Password" to "teacher1"
    And I set the field "Remember username" to "1"
    And I press "Log in"
    And I log out
    # Log out and check that the username was remembered.
    When I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    Then the field "username" matches value "teacher1"
    And the field "Remember username" matches value "1"

  # Given the user has logged in before and selected 'Remember username', when they log in again and unset 'Remember username', then
  # their username should be forgotten for future log in attempts.
  Scenario: Check that 'remember username' unsetting works without javascript for teachers.
    # Log in the first time and check the 'remember username' box.
    Given I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    And I set the field "Username" to "teacher1"
    And I set the field "Password" to "teacher1"
    And I set the field "Remember username" to "1"
    And I press "Log in"
    And I log out
    # Log in again, unsetting the 'remember username' field.
    When I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    And I set the field "Password" to "teacher1"
    And I set the field "Remember username" to "0"
    And I press "Log in"
    And I log out
    # Check username has been forgotten.
    Then I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    Then the field "username" matches value ""
    And the field "Remember username" matches value "0"
