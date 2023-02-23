@mod @mod_bigbluebuttonbn @course
Feature: Test the module in group mode.

  Background:
      # groupmode 1 = separate groups, we force the group
      # groupmode 2 = visible group
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname      | shortname | category | groupmode | groupmodeforce |
      | Test Course 1 | C1        | 0        | 1         | 1              |
      | Test Course 2 | C2        | 0        | 2         | 1              |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
      | Group 1 | C2     | G1b      |
      | Group 2 | C2     | G2b      |
    And the following "users" exist:
      | username | firstname     | lastname | email                |
      | teacher1 | TeacherG1     | 1        | teacher1@example.com |
      | user1    | User1G1       | 1        | user1@example.com    |
      | user2    | User2G2       | 2        | user2@example.com    |
      | user3    | User3NoGgroup | 3        | user3@example.com    |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | user1    | C1     | student        |
      | user2    | C1     | student        |
      | user3    | C1     | student        |
      | teacher1 | C2     | editingteacher |
      | user1    | C2     | student        |
      | user2    | C2     | student        |
      | user3    | C2     | student        |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | user1    | G1    |
      | user2    | G2    |
      | teacher1 | G1b   |
      | user1    | G1b   |
      | user2    | G2b   |
    And the following "activities" exist:
      | activity        | name                        | intro                                   | course | idnumber         | type | recordings_imported |
      | bigbluebuttonbn | RoomRecordings              | Test Room Recording description         | C1     | bigbluebuttonbn1 | 0    | 0                   |
      | bigbluebuttonbn | RoomRecordingsVisibleGroups | Test Room Recording with visible groups | C2     | bigbluebuttonbn2 | 0    | 0                   |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity | RoomRecordings              |
      | activity | RoomRecordingsVisibleGroups |
    And the following "mod_bigbluebuttonbn > meetings" exist:
      | activity                    | group |
      | RoomRecordings              | G1    |
      | RoomRecordings              | G2    |
      | RoomRecordingsVisibleGroups | G1b   |
      | RoomRecordingsVisibleGroups | G2b   |
    And the following "mod_bigbluebuttonbn > recordings" exist:
      | bigbluebuttonbn             | name          | group |
      | RoomRecordings              | Recording G1  | G1    |
      | RoomRecordings              | Recording G2  | G2    |
      | RoomRecordingsVisibleGroups | Recording G1b | G1b   |
      | RoomRecordingsVisibleGroups | Recording G2b | G2b   |
    And the following "mod_bigbluebuttonbn > recordings" exist:
      | bigbluebuttonbn             | name                  |
      | RoomRecordings              | Recording No group    |
      | RoomRecordingsVisibleGroups | Recording No group C2 |

  @javascript
  Scenario: When I create a BBB activity as a teacher who cannot access all groups,
  I should only be able to select the group I belong on the main bigblue button page.
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    When I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as "teacher1"
    Then I should see "Separate groups: Group 1"

  @javascript
  Scenario: When I create a BBB activity as a teacher, I should only be able to specify individual "User" participants with whom I share a group with (or can view on the course participants screen).
    When I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as "teacher1"
    Then I should see "Group 1" in the "select[name='group']" "css_element"
    And I should see "Group 2" in the "select[name='group']" "css_element"

  @javascript
  Scenario Outline: When I view a BBB activity as a student in a course with separate groups, I should only be able to see Recordings from my group
    When I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as "<user>"
    Then I <G1> "Recording G1"
    And I <G2> "Recording G2"
    And I <NO> "Recording No group"

    Examples:
      | user  | G1             | G2             | NO             |
      | user1 | should see     | should not see | should not see |
      | user2 | should not see | should see     | should not see |
      | user3 | should not see | should not see | should not see |

  @javascript
  Scenario Outline: When I view a BBB activity as a student in a course with visible group set, I should be able to see Recordings from my group or
  the default meeting if I am not in a group.
    When I am on the "RoomRecordingsVisibleGroups" "bigbluebuttonbn activity" page logged in as "user3"
    And I select "<groupname>" from the "group" singleselect
    Then I <G1> "Recording G1b"
    And I <G2> "Recording G2b"
    And I <NO> "Recording No group C2"

    Examples:
      | groupname        | G1             | G2             | NO             |
      | All participants | should see     | should see     | should see     |
      | Group 1          | should see     | should not see | should not see |
      | Group 2          | should not see | should see     | should not see |

  @javascript
  Scenario Outline: When I view a BBB activity as a student in a course with visible group set, I should be able to join meeting if not I should not see the activity
    When I am on the "<Activity>" "bigbluebuttonbn activity" page logged in as "<user>"
    Then I should see "<Message>"

    Examples:
      | user  | Activity                    | Message                                                     |
      | user1 | RoomRecordings              | Join session                                                |
      | user2 | RoomRecordings              | Join session                                                |
      | user3 | RoomRecordings              | You do not have a role that is allowed to join this session |
      | user1 | RoomRecordingsVisibleGroups | Join session                                                |
      | user2 | RoomRecordingsVisibleGroups | Join session                                                |
      | user3 | RoomRecordingsVisibleGroups | Join session                                                |
