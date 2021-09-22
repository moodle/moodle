@tool_behat
Feature: Verify that the behat login and logout steps work as expected
  In order to use behat login and log out steps
  As a test writer
  I need to verify that login and logout happen when the steps are used

  Scenario: Log in as a user using the step
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
    When I log in as "traverst1"
    Then I should see "Thomas Travers"

  @javascript
  Scenario: Log in as a user using the step (javascript)
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
    When I log in as "traverst1"
    Then I should see "Thomas Travers"

  Scenario: Log out using the log out step
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
    And I am logged in as traverst1
    When I log out
    Then I should not see "Thomas Travers"
    And I should see "You are not logged in"

  @javascript
  Scenario: Log out using the log out step (javascript)
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
    And I am logged in as traverst1
    When I log out
    Then I should not see "Thomas Travers"
    And I should see "You are not logged in"

  Scenario: Log in step should automatically log user out if already logged in
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
      | emeryj    | Jane      | Emery    |
    And I am logged in as traverst1
    When I log in as "emeryj"
    Then I should not see "Thomas Travers"
    And I should see "Jane Emery"

  @javascript
  Scenario: Log in step should automatically log user out if already logged in (javascript)
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
      | emeryj    | Jane      | Emery    |
    And I am logged in as traverst1
    When I log in as "emeryj"
    Then I should not see "Thomas Travers"
    And I should see "Jane Emery"

  Scenario: I am on page logged in as should redirect to correct page
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
    And the following "course" exists:
      | fullname  | Life, the Universe, and Everything |
      | shortname | hhgttg                             |
    When I am on the hhgttg Course page logged in as traverst1
    Then I should see "Thomas Travers"
    And I should see "Life, the Universe, and Everything"

  @javascript
  Scenario: I am on page logged in as should redirect to correct page (javascript)
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
    And the following "course" exists:
      | fullname  | Life, the Universe, and Everything |
      | shortname | hhgttg                             |
    When I am on the hhgttg Course page logged in as traverst1
    Then I should see "Thomas Travers"
    And I should see "Life, the Universe, and Everything"

  Scenario: I am on page logged in as should redirect to correct page when automatically logging a user out
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
      | emeryj    | Jane      | Emery    |
    And the following "course" exists:
      | fullname  | Life, the Universe, and Everything |
      | shortname | hhgttg                             |
    And I am logged in as emeryj
    When I am on the hhgttg Course page logged in as traverst1
    Then I should see "Thomas Travers"
    And I should see "Life, the Universe, and Everything"

  @javascript
  Scenario: I am on page logged in as should redirect to correct page when automatically logging a user out (javacript)
    Given the following "users" exist:
      | username  | firstname | lastname |
      | traverst1 | Thomas    | Travers  |
      | emeryj    | Jane      | Emery    |
    And the following "course" exists:
      | fullname  | Life, the Universe, and Everything |
      | shortname | hhgttg                             |
    And I am logged in as emeryj
    When I am on the hhgttg Course page logged in as traverst1
    Then I should see "Thomas Travers"
    And I should see "Life, the Universe, and Everything"
