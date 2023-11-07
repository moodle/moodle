@mod @mod_bigbluebuttonbn @javascript
Feature: I can edit a bigbluebutton instance
  As a user I can edit a BigbluebuttonBN instance

  Background:  Make sure that a course is created
    Given a BigBlueButton mock server is configured
    And I accept dpa and enable bigbluebuttonbn plugin
    And the following config values are set as admin:
      | bigbluebuttonbn_voicebridge_editable | 1 |
    And the following "courses" exist:
      | fullname    | shortname   | category | enablecompletion |
      | Test course | Test course | 0        | 1                |

  Scenario Outline: Add a mod_bigbluebuttonbn instance with Room with recordings
    Given the following "activities" exist:
      | activity        | course      | name           | type           |
      | bigbluebuttonbn | Test course | <activityname> | <instancetype> |
    When I am on the "<activityname>" "bigbluebuttonbn activity" page logged in as admin
    And I click on "Settings" "link"
    Then the field "Instance type" matches value "<instancetype>"
    And I <shouldseerole> "Role assigned during live session"
    And I expand all fieldsets
    And I <shouldseesession> "The session may be recorded."
    Examples:
      | activityname            | instancetype | shouldseerole  | shouldseesession |
      | Activity with recording | 0            | should see     | should see       |
      | Activity only           | 1            | should see     | should see       |
      | Recordings only         | 2            | should not see | should not see   |

  Scenario: When the activity completion are locked, all the completion settings, including
  the one specific to BigBlueButtonBN are disabled
    Given the following "activities" exist:
      | activity        | course      | name           | type |
      | bigbluebuttonbn | Test course | RoomRecordings | 0    |
    And the following config values are set as admin:
      | bigbluebuttonbn_config_experimental_features | 1 |
      | bigbluebuttonbn_meetingevents_enabled        | 1 |
    And the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student1   | 1        |
    And the following "course enrolments" exist:
      | user     | course      | role           |
      | student1 | Test course | student |
    And I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Completion tracking         | Show activity as complete when conditions are met |
      | Require view                | 1                                                 |
    And I press "Save and return to course"
    And I log out
    # Then I visit the page first to make sure that completion settings are locked.
    And I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as "student1"
    And I log out
    And I am on the "RoomRecordings" "bigbluebuttonbn activity editing" page logged in as "admin"
    When I expand all fieldsets
    Then I should see "Completion options locked"
    And the "Require view" "field" should be disabled
    And the "completionattendanceenabled" "field" should be disabled
    And the "Chats" "field" should be disabled
