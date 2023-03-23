@mod @mod_bigbluebuttonbn
Feature: Guest access allows external users to connect to a meeting

  Background:
    Given a BigBlueButton mock server is configured
    And I accept dpa and enable bigbluebuttonbn plugin
    And the following "courses" exist:
      | fullname      | shortname | category |
      | Test Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity        | name                    | intro                           | course | idnumber         | type | recordings_imported | guestallowed |
      | bigbluebuttonbn | RoomRecordings          | Test Room Recording description | C1     | bigbluebuttonbn1 | 0    | 0                   | 0            |
      | bigbluebuttonbn | RoomRecordingsWithGuest | Test Room with guest            | C1     | bigbluebuttonbn1 | 0    | 0                   | 1            |

  @javascript
  Scenario Outline: I need to enable guest access to see the instance parameters
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | <guestaccess> |
    When I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    Then I <result> "Guest access"
    Then I log out
    Examples:
      | guestaccess | result         |
      | 1           | should see     |
      | 0           | should not see |

  @javascript
  Scenario: I should see Guest settings on the module form
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
    When I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    Then I should see "Guest access"
    Then I click on "Expand all" "link"
    Then I should see "Allow guest access"
    And I should not see "Meeting link"
    And I should not see "Meeting password"
    When I set the field "Allow guest access" to "1"
    Then I should see "Guests joining must be admitted by a moderator"
    And I should see "Meeting link"
    And I should see "Meeting password"
    And I should see "Copy link"
    And I should see "Copy password"
    Then I log out

  @javascript
  Scenario: I should be able to use the guest link and see the popup dialog
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
    When I am on the "RoomRecordingsWithGuest" "bigbluebuttonbn activity" page logged in as "admin"
    Then I click on "Add guests" "button"
    And I should see "Meeting link"
    And I should see "Meeting password"
    And I should see "Copy link"
    And I should see "Copy password"

  @javascript
  Scenario: I should see errors when submitting the guest access form with erroneous values
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
    And I am on the "RoomRecordingsWithGuest" "bigbluebuttonbn activity" page logged in as "admin"
    And I click on "Add guests" "button"
    And I set the field "Guest emails" to "123"
    When I click on "OK" "button" in the "Add guests to this meeting" "dialogue"
    Then I should see "Invalid email: 123"

  @javascript
  Scenario: I should be able to invite guest to the meeting
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
    And I am on the "RoomRecordingsWithGuest" "bigbluebuttonbn activity" page logged in as "admin"
    And I click on "Add guests" "button"
    And I set the field "Guest emails" to "testuser@email.com"
    And  I click on "OK" "button" in the "Add guests to this meeting" "dialogue"
    Then I should see "An invitation will be sent to testuser@email.com."
    Then I log out

  Scenario: I should be able to invite guest to the meeting even if forcelogin is set
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
      | forcelogin                          | 1 |
    When I am on the "RoomRecordingsWithGuest" "mod_bigbluebuttonbn > BigblueButtonBN Guest" page
    Then I should see "C1: RoomRecordingsWithGuest"
