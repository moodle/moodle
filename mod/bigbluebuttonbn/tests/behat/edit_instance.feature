@mod @mod_bigbluebuttonbn @javascript
Feature: I can edit a bigbluebutton instance
  In order to edit a room activity with recordings
  As a user
  I need to add three room activities to an existent course

  Background:  Make sure that a course is created
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following config values are set as admin:
      | bigbluebuttonbn_voicebridge_editable | 1 |
    And the following "courses" exist:
      | fullname    | shortname   | category |
      | Test course | Test course | 0        |

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
