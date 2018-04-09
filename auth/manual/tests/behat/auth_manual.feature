@auth @auth_manual
Feature: Test manual authentication works.
  In order to check manual authentication
  As a teacher
  I need to go on login page and enter username and password.

  Background:
    Given the following "users" exist:
      | username |
      | teacher1 |

  @javascript
  Scenario: Check login works with javascript.
    Given I am on homepage
    And I expand navigation bar
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    When I set the field "Username" to "teacher1"
    And I set the field "Password" to "teacher1"
    When I press "Log in"
    Then I should see "You are logged in as"

  Scenario: Check login works without javascript.
    Given I am on homepage
    And I click on "Log in" "link" in the ".logininfo" "css_element"
    When I set the field "Username" to "teacher1"
    And I set the field "Password" to "teacher1"
    When I press "Log in"
    Then I should see "You are logged in as"
