@mod @mod_bigbluebuttonbn @javascript
Feature: bigbluebuttonbn instance
  In order to create a room activity with recordings
  As a user
  I need to add three room activities to an existent course

  Background:  Make sure that a course is created
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname    | shortname   | category |
      | Test course | Test course | 0        |
    And the following "activities" exist:
      | activity        | course          | name                | type |
      | bigbluebuttonbn | Test course     | BBB Instance name   | 0    |
      | bigbluebuttonbn | Test course     | BBB Instance name 2 | 1    |
      | bigbluebuttonbn | Test course     | BBB Instance name 3 | 2    |
    And I am on the "Test course" "course" page logged in as "admin"

  Scenario: Add a mod_bigbluebuttonbn instance with Room with recordings
    When I am on the "BBB Instance name" "bigbluebuttonbn activity" page
    Then I should see "This room is ready. You can join the session now."
    And I should see "Join session"
    And I should see "Recordings"

  Scenario: Add a mod_bigbluebuttonbn instance with Room only
    When I am on the "BBB Instance name 2" "bigbluebuttonbn activity" page
    Then I should see "This room is ready. You can join the session now."
    And I should see "Join session"
    And I should not see "Recordings"

  Scenario: Add a mod_bigbluebuttonbn instance with Recordings only
    When I am on the "BBB Instance name 3" "bigbluebuttonbn activity" page
    And I should not see "This room is ready. You can join the session now."
    And I should not see "Join session"
    And I should see "Recordings"

  Scenario Outline: Add an activity and check that required settings are available for the three types of instance types
    When I turn editing mode on
    And I add a "BigBlueButton" to section "1"
    And  I select "<type>" from the "Instance type" singleselect
    Then I should see "Restrict access"

    Examples:
      | type                          |
      | Room with recordings          |
      | Room only                     |
      | Recordings only               |
