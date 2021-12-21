@mod @mod_bigbluebuttonbn @course
Feature: Test the module in group mode.

  Background:
    # 1 = separate groups, we force the group
    Given a BigBlueButton mock server is configured
    And the following "courses" exist:
      | fullname      | shortname | category | groupmode | groupmodeforce |
      | Test Course 1 | C1        | 0        | 1         | 1              |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | TeacherG1 | 1        | teacher1@example.com |
      | teacher2 | TeacherG2 | 2        | teacher2@example.com |
      | user1    | User1G1   | 1        | user1@example.com    |
      | user2    | User2G2   | 2        | user2@example.com    |
      | user3    | User3NoG2 | 3        | user3@example.com    |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
      | user1    | C1     | student        |
      | user2    | C1     | student        |
      | user3    | C1     | student        |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | teacher2 | G2    |
      | user1    | G1    |
      | user2    | G2    |
    And the following "activities" exist:
      | activity        | name           | intro                           | course | idnumber         | type | recordings_imported |
      | bigbluebuttonbn | RoomRecordings | Test Room Recording description | C1     | bigbluebuttonbn1 | 0    | 0                   |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity       | RoomRecordings |
    And the following "mod_bigbluebuttonbn > meetings" exist:
      | activity       | group |
      | RoomRecordings | G1    |
      | RoomRecordings | G2    |
    And the following "mod_bigbluebuttonbn > recordings" exist:
      | bigbluebuttonbn | name               | group |
      | RoomRecordings  | Recording G1       | G1    |
      | RoomRecordings  | Recording G2       | G2    |
    And the following "mod_bigbluebuttonbn > recordings" exist:
      | bigbluebuttonbn | name               |
      | RoomRecordings  | Recording No group |

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
  Scenario Outline: When I view a BBB activity as a student, I should only be able to see Recordings from my group
    When I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as "<user>"
    Then I <G1> "Recording G1"
    And I <G2> "Recording G2"
    And I <NO> "Recording No group"

    Examples:
      | user  | G1             | G2             | NO             |
      | user1 | should see     | should not see | should not see |
      | user2 | should not see | should see     | should not see |
      | user3 | should not see | should not see | should see     |
