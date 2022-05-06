@mod @mod_bigbluebuttonbn @javascript
Feature: I can edit a bigbluebutton instance
When a bibluebutton instance has been created I can edit it

  Background:  Make sure that a course is created
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following config values are set as admin:
      | bigbluebuttonbn_voicebridge_editable | 1 |
    And the following "courses" exist:
      | fullname    | shortname   | category |
      | Test course | Test course | 0        |

  Scenario Outline: Add a mod_bigbluebuttonbn instance with Room with recordings
    Then I log in as "admin"
    And I am on "Test course" course homepage with editing mode on
    When I add a "BigBlueButton" to section "1" and I fill the form with:
      | name                   | <activityname> |
      | Instance type          | <instancetype> |
      | Room name              | <activityname> |
    Given I am on the "<activityname>" "bigbluebuttonbn activity" page logged in as admin
    And I click on "Settings" "link"
    And the field "Instance type" matches value "<instancetype>"
    And I <shouldseerole> "Role assigned during live session"
    And I expand all fieldsets
    And I <shouldseesession> "The session may be recorded."
    Examples:
      | activityname            | instancetype                  | shouldseerole  | shouldseesession |
      | Activity with recording | Room with recordings          | should see     | should see       |
      | Activity only           | Room only            | should see     | should see       |
      | Recordings only         | Recordings only               | should not see | should not see   |
