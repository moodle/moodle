@core @core_group
Feature: Backup and restore a course containing groups
  In order to transfer groups to another course
  As a teacher
  I want to backup and restore a course retaining the groups

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion | numsections |
      | Course 1 | C1        | topics | 1                | 3           |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | Teacher  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "groups" exist:
      | name                                      | course | idnumber | visibility | participation |
      | Visible/Participation                     | C1     | VP       | 0          | 1             |
      | Only visible to members/Participation     | C1     | MP       | 1          | 1             |
      | Only see own membership                   | C1     | O        | 2          | 0             |
      | Not visible                               | C1     | N        | 3          | 0             |
      | Visible/Non-Participation                 | C1     | VN       | 0          | 0             |
      | Only visible to members/Non-Participation | C1     | MN       | 1          | 0             |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |
    And I log in as "admin"
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Restored course |

  @javascript
  Scenario Outline: Check restored groups
    Given I am on the "Restored course copy 1" "groups" page logged in as teacher1
    When I set the field "Groups" to "<group>"
    And I press "Edit group settings"
    Then the following fields match these values:
      | Group ID number              | <idnumber>      |
      | Group membership visibility             | <visibility>    |
      | Show group in dropdown menu for activities in group mode | <participation> |

    Examples:
      | group                                     | idnumber | visibility | participation |
      | Visible/Participation                     | VP       | 0          | 1             |
      | Only visible to members/Participation     | MP       | 1          | 1             |
      | Only see own membership                   | O        | 2          | 0             |
      | Not visible                               | N        | 3          | 0             |
      | Visible/Non-Participation                 | VN       | 0          | 0             |
      | Only visible to members/Non-Participation | MN       | 1          | 0             |
