@mod @mod_bigbluebuttonbn
Feature: Recording safe formats are role-aware
  As a teacher I can see all playback formats
  As a student I can only see safe playback formats

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following config values are set as admin:
      | bigbluebuttonbn_recording_safe_formats | video,presentation |
    And the following "courses" exist:
      | fullname      | shortname | category |
      | Test Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity        | name           | intro             | course | idnumber         | type | recordings_imported |
      | bigbluebuttonbn | RoomRecordings | Test description  | C1     | bigbluebuttonbn1 | 0    | 0                   |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity | RoomRecordings |
    And activity "RoomRecordings" has processed recording "Recording multi-format" with playback formats:
      | type         |
      | video        |
      | presentation |
      | podcast      |
      | settings     |

  Scenario: Student only sees formats from safe list
    Then user "student1" should see playback formats "video,presentation" for activity "RoomRecordings"

  Scenario: Teacher sees all formats
    Then user "teacher1" should see playback formats "video,presentation,podcast,settings" for activity "RoomRecordings"
