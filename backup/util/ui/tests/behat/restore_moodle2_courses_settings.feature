@core @core_backup
Feature: Restore Moodle 2 course backups with different user data settings
  In order to decide upon including user data during backup and restore of courses
  As a teacher and an admin
  I need to be able to set and override backup and restore settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Test field name |
      | Field description | Test field description |
    And I follow "Templates"
    And I wait until the page is ready
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I add an entry to "Test database name" database with:
      | Test field name | Student entry |
    And I press "Save and view"
    And I log out
    And I log in as "admin"
    And I backup "Course 1" course using this options:
      | Initial |  Include enrolled users | 1 |
      | Confirmation | Filename | test_backup.mbz |

  @javascript
  Scenario: Restore a backup with user data
    # "User data" marks the user data field for the section
    # "-" marks the user data field for the data activity
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 1 |
      | Schema | User data | 1 |
      | Schema | - | 1 |
    Then I should see "Test database name"
    When I follow "Test database name"
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
    When I follow "Test database name"
    Then I should not see "Student entry"

  @javascript
  Scenario: Restore a backup without user data for section and data activity
    # "User data" marks the user data field for the section
    # "-" marks the user data field for the data activity
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 1 |
      | Schema | User data | 0 |
      | Schema | - | 0 |
    Then I should see "Test database name"
    When I follow "Test database name"
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
    When I follow "Test database name"
    Then I should not see "Student entry"

  @javascript
  Scenario: Restore a backup with user data with local config for including users set to 0
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 0 |
    Then I should see "Test database name"
    When I follow "Test database name"
    Then I should not see "Student entry"

  @javascript
  Scenario: Restore a backup with user data with site config for including users set to 0
    Given I navigate to "Courses > Backups > General restore defaults" in site administration
    And I set the field "s_restore_restore_general_users" to ""
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "Restore" in current page administration
    # "User data" marks the user data field for the section
    # "-" marks the user data field for the data activity
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 1 |
      | Schema | User data | 1 |
      | Schema | - | 1 |
    Then I should see "Test database name"
    When I follow "Test database name"
    Then I should see "Student entry"

  @javascript
  Scenario: Restore a backup with user data with local and site config config for including users set to 0
    Given I navigate to "Courses > Backups > General restore defaults" in site administration
    And I set the field "s_restore_restore_general_users" to ""
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "Restore" in current page administration
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings |  Include enrolled users | 0 |
    Then I should see "Test database name"
    When I follow "Test database name"
    Then I should not see "Student entry"