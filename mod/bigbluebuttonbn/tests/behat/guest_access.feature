@mod @mod_bigbluebuttonbn
Feature: Guest access allows external users to connect to a meeting

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
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
    And I log out
    Examples:
      | guestaccess | result         |
      | 1           | should see     |
      | 0           | should not see |

  @javascript
  Scenario Outline: I should see Guest settings on the module form
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
    When I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    And I should see "Guest access"
    And I click on "Expand all" "link" in the "region-main" "region"
    And I should see "Allow guest access"
    And I set the field "Allow guest access" to <guestaccess>
    Then I <result> "Guests joining must be admitted by a moderator"
    And I <seelink> "Meeting link"
    And I <seepassword> "Meeting password"
    And I <seecopylink> "Copy link"
    And I <seecopypw> "Copy password"
    And I log out

    Examples:
      | guestaccess | result         | seelink        | seepassword     | seecopylink    | seecopypw   |
      | "1"         | should see     | should see     | should see      | should see     | should see  |
      | "0"         | should not see | should not see | should not see  | should not see | should not see |

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
    When I am on the "RoomRecordingsWithGuest" "bigbluebuttonbn activity" page logged in as "admin"
    And I click on "Add guests" "button"
    And I set the field "Guest emails" to "testuser@email.com"
    And  I click on "OK" "button" in the "Add guests to this meeting" "dialogue"
    Then I should see "An invitation will be sent to testuser@email.com."
    And I log out

  Scenario: I should be able to invite guest to the meeting even if forcelogin is set
    Given the following config values are set as admin:
      | bigbluebuttonbn_guestaccess_enabled | 1 |
      | forcelogin                          | 1 |
    When I am on the "RoomRecordingsWithGuest" "mod_bigbluebuttonbn > BigblueButtonBN Guest" page
    Then I should see "C1: RoomRecordingsWithGuest"
