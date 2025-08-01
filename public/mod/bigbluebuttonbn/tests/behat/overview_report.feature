@mod @mod_bigbluebuttonbn @javascript
Feature: Testing overview integration in mod_bigbluebuttonbn
  In order to summarize the bigbluebuttonbn activities
  As a user
  I need to be able to see the bigbluebuttonbn overview

  Background:
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following "users" exist:
      | username        | firstname      | lastname |
      | student1        | Username       | 1        |
      | student2        | Username       | 2        |
      | student3        | Username       | 3        |
      | student4        | Username       | 4        |
      | student5        | Username       | 5        |
      | student6        | Username       | 6        |
      | student7        | Username       | 7        |
      | student8        | Username       | 8        |
      | teacher1        | Teacher        | T        |
      | editingteacher1 | EditingTeacher | T        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user            | course | role           | firstname | lastname |
      | student1        | C1     | student        | Username  | 1        |
      | student2        | C1     | student        | Username  | 2        |
      | student3        | C1     | student        | Username  | 3        |
      | teacher1        | C1     | teacher        | Username  | T        |
      | editingteacher1 | C1     | editingteacher | Username  | ET       |
    And the following "activities" exist:
      | activity        | name                      | intro                                   | course | idnumber         | type | recordings_imported | openingtime    | closingtime    | grade | moderators          |
      | bigbluebuttonbn | RoomRecordings            | Test Room Recording description         | C1     | bigbluebuttonbn1 | 0    | 0                   | 1 January 2024 |                | 100   | role:editingteacher |
      | bigbluebuttonbn | RoomOnly                  | Test Room Recording with visible groups | C1     | bigbluebuttonbn2 | 1    | 0                   |                | 1 January 2040 | 100   | role:editingteacher |
      | bigbluebuttonbn | RecordingOnly             | Test Room Recording with visible groups | C1     | bigbluebuttonbn3 | 2    | 0                   |                |                | 0     | role:editingteacher |
      | bigbluebuttonbn | RoomRecordingsNoUser      | Test Room Recording with visible groups | C1     | bigbluebuttonbn4 | 0    | 0                   | 1 January 2024 | 1 January 2040 | 0     | role:editingteacher |
      | bigbluebuttonbn | RoomRecordingsNoModerator | Test Room Recording with visible groups | C1     | bigbluebuttonbn5 | 0    | 0                   | 1 January 2024 | 1 January 2040 | 0     |                     |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity | RoomRecordings |
    And the following "mod_bigbluebuttonbn > recordings" exist:
      | bigbluebuttonbn | name        | description   | status |
      | RoomRecordings  | Recording 1 | Description 1 | 2      |
      | RoomRecordings  | Recording 2 | Description 2 | 2      |
      | RoomRecordings  | Recording 3 | Description 3 | 2      |
      | RoomRecordings  | Recording 4 | Description 4 | 0      |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "editingteacher1"
    And I turn editing mode on
    And I give the grade "90.00" to the user "Username 1" for the grade item "RoomRecordings"
    And I give the grade "100.00" to the user "Username 2" for the grade item "RoomOnly"
    And I click on "Save changes" "button"
    And I log out

  Scenario: The bigbluebuttonbn overview report should generate log events
    Given I am on the "Course 1" "course > activities > bigbluebuttonbn" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'bigbluebuttonbn'"

  Scenario: Teachers can see relevant columns in the bigbluebuttonbn overview
    Given I am on the "Course 1" "course > activities > bigbluebuttonbn" page logged in as "editingteacher1"
    When I should not see "Grade" in the "bigbluebuttonbn_overview_collapsible" "region"
    Then the following should exist in the "Table listing all BigBlueButton activities" table:
      | Name                      | Opens                            | Closes                           | Instance type        | Recordings | Actions |
      | RoomRecordings            | Monday, 1 January 2024, 12:00 AM | -                                | Room with recordings | 3          | View    |
      | RoomOnly                  | -                                | Sunday, 1 January 2040, 12:00 AM | Room only            | -          | View    |
      | RecordingOnly             | -                                | -                                | Recordings only      | 0          | View    |
      | RoomRecordingsNoUser      | Monday, 1 January 2024, 12:00 AM | Sunday, 1 January 2040, 12:00 AM | Room with recordings | 0          | View    |
      | RoomRecordingsNoModerator | Monday, 1 January 2024, 12:00 AM | Sunday, 1 January 2040, 12:00 AM |                      |            |         |

  Scenario: Students can see relevant columns in the bigbluebuttonbn overview
    Given I am on the "Course 1" "course > activities > bigbluebuttonbn" page logged in as "student1"
    Then the following should exist in the "Table listing all BigBlueButton activities" table:
      | Name                      | Opens                            | Closes                           | Grade |
      | RoomRecordings            | Monday, 1 January 2024, 12:00 AM | -                                | 90.00 |
      | RoomOnly                  | -                                | Sunday, 1 January 2040, 12:00 AM | -     |
      | RecordingOnly             | -                                | -                                |       |
      | RoomRecordingsNoUser      | Monday, 1 January 2024, 12:00 AM | Sunday, 1 January 2040, 12:00 AM |       |
      | RoomRecordingsNoModerator | Monday, 1 January 2024, 12:00 AM | Sunday, 1 January 2040, 12:00 AM |       |
    And I should not see "Instance type" in the "bigbluebuttonbn_overview_collapsible" "region"
    And I should not see "Actions" in the "bigbluebuttonbn_overview_collapsible" "region"
