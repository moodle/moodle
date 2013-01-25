@auth
Feature: Authentication
  In order to validate my credentials in the system
  As a moodle user
  I need to log into the system

  Scenario: Login with the predefined admin user
    Given I log in as "admin"

  Scenario: Logout
    Given I log in as "admin"
    When I log out
    Then I should see "You are not logged in"
