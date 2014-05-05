@block @block_login
Feature: Login from a block
    In order to make it easier to login
    As an user
    In need to login through a block

  Background:
    Given the following "users" exist:
      | username | password | firstname | lastname | email |
      | testuser | testpass | Test      | User     | student1@asd.com |
    And I log in as "admin"
    And I navigate to "Turn editing on" node in "Front page settings"
    And I add the "Login" block

  Scenario: Login block visible to non-logged in users
    Given I log out
    When I am on homepage
    Then "Login" "block" should exist

  Scenario: Login as student through login block
    Given I log out
    And I am on homepage
    When I set the field "Username" to "testuser"
    And I set the field "Password" to "testpass"
    And I click on "Log in" "button" in the "Login" "block"
    Then I should see "You are logged in as Test User"
    And "Login" "block" should not exist
