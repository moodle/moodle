@mod @mod_bigbluebuttonbn
Feature: Test that meeting roles are sent to the server
  In order to ensure that start meeting capabilities are respected
  As a teacher
  I need meeting roles to be sent to the meeting

  Background:
    Given a BigBlueButton mock server is configured
    And I accept dpa and enable bigbluebuttonbn plugin

  @javascript
  Scenario Outline: Users should receive the appropriate role when joining the meeting
    Given the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role         |
      | traverst | C1     | <moodlerole> |
    And the following "activity" exists:
      | course     | C1              |
      | activity   | bigbluebuttonbn |
      | name       | Room recordings |
      | idnumber   | Room recordings |
      | moderators | <moderators>    |
      | viewers    | <viewers>       |
    When I am on the "Room recordings" Activity page logged in as traverst
    And I click on "Join session" "link"
    And I switch to "bigbluebutton_conference" window
    Then I should see "<meetingrole>" in the "attendeeRole" "mod_bigbluebuttonbn > Meeting field"

    Examples:
      | moderators          | viewers | moodlerole     | meetingrole |
      |                     |         | student        | VIEWER      |
      | role:editingteacher |         | editingteacher | MODERATOR   |
      | role:student        |         | editingteacher | VIEWER      |
      | role:student        |         | student        | MODERATOR   |
      | user:traverst       |         | student        | MODERATOR   |
