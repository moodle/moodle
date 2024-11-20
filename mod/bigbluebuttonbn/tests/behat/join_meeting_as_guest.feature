@mod @mod_bigbluebuttonbn @javascript
Feature: Test the ability to run the full meeting lifecycle (start to end) for guest users

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
    And the following course exists:
      | name      | Test course |
      | shortname | C1          |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
      | teacher  | Teacher   | Teacher  | t.eacher@example.com  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | traverst | C1     | student        |
      | teacher  | C1     | editingteacher |
    And the following "activity" exists:
      | course       | C1                  |
      | activity     | bigbluebuttonbn     |
      | name         | Room recordings     |
      | idnumber     | Room recordings     |
      | moderators   | role:editingteacher |
      | wait         | 0                   |
      | guestallowed | 1                   |

  Scenario: Student users should be able to see the guest user information
    When I am on the "Room recordings" Activity page logged in as traverst
    Then I should not see "Add guests"

  Scenario: Teacher users should be able to see the guest user information
    When I am on the "Room recordings" Activity page logged in as teacher
    And I should see "Add guests"
    Then I click on "Add guests" "button"
    And I should see "Add guests to this meeting" in the ".modal-dialog" "css_element"

  Scenario: Guest users should be able to join a meeting as guest when the meeting is running.
    When I am on the "Room recordings" Activity page logged in as traverst
    And "Join session" "link" should exist
    And I click on "Join session" "link"
    And I switch to the main window
    And I log out
    And I close all opened windows
    And I am on the "Room recordings" "mod_bigbluebuttonbn > BigblueButtonBN Guest" page
    Then I should see "Guest username"
    And I should see "Password"
    And I set the field "username" to "Test Guest User"
    And I click on "Join meeting" "button"
    And I should see "Test Guest User"
    And I click on "Leave Meeting" "link"
    And I should see "C1: Room recordings"
