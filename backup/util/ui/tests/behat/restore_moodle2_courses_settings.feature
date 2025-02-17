@core @core_backup
Feature: Restore Moodle 2 course backups with different user data settings
  In order to decide upon including user data during backup and restore of courses
  As a teacher and an admin
  I need to be able to set and override backup and restore settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name            | description            |
      | data1    | text | Test field name | Test field description |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | user     | Test field name |
      | data1    | student1 | Student entry   |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |
    And I log in as "admin"
    And I backup "Course 1" course using this options:
      | Initial      | Include enrolled users | 1               |
      | Confirmation | Filename               | test_backup.mbz |

  @javascript
  Scenario: Restore a backup with user data
    # "User data" marks the user data field for the section
    # "-" marks the user data field for the data activity
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 1 |
      | Schema | User data | 1 |
      | Schema | - | 1 |
    Then I should see "Test database name"
    When I click on "Test database name" "link" in the "region-main" "region"
    Then I should see "Student entry"

  @javascript
  Scenario: Restore a backup without user data for data activity
    # "User data" marks the user data field for the section
    # "-" marks the user data field for the data activity
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 1 |
      | Schema | User data | 1 |
      | Schema | - | 0 |
    Then I should see "Test database name"
    When I click on "Test database name" "link" in the "region-main" "region"
    Then I should not see "Student entry"

  @javascript
  Scenario: Restore a backup without user data for section and data activity
    # "User data" marks the user data field for the section
    # "-" marks the user data field for the data activity
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 1 |
      | Schema | - | 0 |
      | Schema | User data | 0 |
    Then I should see "Test database name"
    When I click on "Test database name" "link" in the "region-main" "region"
    Then I should not see "Student entry"

  @javascript
  Scenario: Restore a backup without user data for section
    # "User data" marks the user data field for the section
    # "-" marks the user data field for the data activity
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 1 |
      | Schema | - | 1 |
      | Schema | User data | 0 |
    Then I should see "Test database name"
    When I click on "Test database name" "link" in the "region-main" "region"
    Then I should not see "Student entry"

  @javascript
  Scenario: Restore a backup with user data with local config for including users set to 0
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 0 |
    Then I should see "Test database name"
    When I click on "Test database name" "link" in the "region-main" "region"
    Then I should not see "Student entry"

  @javascript
  Scenario: Restore a backup with user data with site config for including users set to 0
    Given I navigate to "Courses > Backups > General restore defaults" in site administration
    And I set the field "s_restore_restore_general_users" to ""
    And I press "Save changes"
    And I am on the "Course 1" "restore" page
    # "User data" marks the user data field for the section
    # "-" marks the user data field for the data activity
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 1 |
      | Schema | User data | 1 |
      | Schema | - | 1 |
    Then I should see "Test database name"
    When I click on "Test database name" "link" in the "region-main" "region"
    Then I should see "Student entry"

  @javascript
  Scenario: Restore a backup with user data with local and site config config for including users set to 0
    Given I navigate to "Courses > Backups > General restore defaults" in site administration
    And I set the field "s_restore_restore_general_users" to ""
    And I press "Save changes"
    And I am on the "Course 1" "restore" page
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 0 |
    Then I should see "Test database name"
    When I click on "Test database name" "link" in the "region-main" "region"
    Then I should not see "Student entry"
