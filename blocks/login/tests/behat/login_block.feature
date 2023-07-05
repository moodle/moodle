@block @block_login
Feature: Login from a block
    In order to make it easier to login
    As an user
    In need to login through a block

  Background:
    Given the following "users" exist:
      | username | password | firstname | lastname | email |
      | testuser | testpass | Test      | User     | student1@example.com |
    And the following "blocks" exist:
      | blockname | contextlevel | reference | pagetypepattern | defaultregion |
      | login     | System       | 1         | site-index      | side-pre      |

  Scenario: Login block visible to non-logged in users
    When I am on homepage
    Then "Login" "block" should exist

  Scenario: Login as student through login block
    Given I am on homepage
    When I set the field "Username" to "testuser"
    And I set the field "Password" to "testpass"
    And I click on "Log in" "button" in the "Login" "block"
    Then I should see "You are logged in as Test User"
    And "Login" "block" should not exist
