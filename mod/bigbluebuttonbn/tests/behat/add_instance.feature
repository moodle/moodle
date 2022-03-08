@mod @mod_bigbluebuttonbn @javascript
Feature: bigbluebuttonbn instance
  In order to create a room activity with recordings
  As a user
  I need to add three room activities to an existent course

  Background:  Make sure that a course is created
    Given a BigBlueButton mock server is configured
    And the following "courses" exist:
      | fullname    | shortname   | category |
      | Test course | Test course | 0        |

  Scenario: Add a mod_bigbluebuttonbn instance with Room with recordings
    Given I am on the "Test course" "course" page logged in as "admin"
    And I am on "Test course" course homepage with editing mode on
    When I add a "BigBlueButton" to section "1" and I fill the form with:
      | name                   | BBB Instance name             |
      | Instance type          | Room with recordings          |
      | Room name              | BBB Instance name             |
    And I am on the "Test course" course page
    Then I should see "BBB Instance name"
    And I am on the "BBB Instance name" "bigbluebuttonbn activity" page
    And I should see "This room is ready. You can join the session now."
    And I should see "Join session"
    And I should see "Recordings"

  Scenario: Add a mod_bigbluebuttonbn instance with Room only
    Given I am on the "Test course" "course" page logged in as "admin"
    And I am on "Test course" course homepage with editing mode on
    When I add a "BigBlueButton" to section "1" and I fill the form with:
      | Instance type          | Room only          |
      | Room name              | BBB Instance name  |
    And I am on the "Test course" course page
    Then I should see "BBB Instance name"
    And I am on the "BBB Instance name" "bigbluebuttonbn activity" page
    And I should see "This room is ready. You can join the session now."
    And I should see "Join session"
    And I should not see "Recordings"

  Scenario: Add a mod_bigbluebuttonbn instance with Recordings only
    Given I am on the "Test course" "course" page logged in as "admin"
    And I am on "Test course" course homepage with editing mode on
    When I add a "BigBlueButton" to section "1" and I fill the form with:
      | Instance type          | Recordings only   |
      | Room name              | BBB Instance name |
    And I am on the "Test course" course page
    Then I should see "BBB Instance name"
    And I am on the "BBB Instance name" "bigbluebuttonbn activity" page
    And I should not see "This room is ready. You can join the session now."
    And I should not see "Join session"
    And I should see "Recordings"

  Scenario Outline: Add an activity and check that required settings are available for the three types of instance types
    Given I am on the "Test course" "course" page logged in as "admin"
    And I am on "Test course" course homepage with editing mode on
    And I add a "BigBlueButton" to section "1"
    When  I select "<type>" from the "Instance type" singleselect
    Then I should see "Restrict access"

    Examples:
      | type                          |
      | Room with recordings          |
      | Room only                     |
      | Recordings only               |
