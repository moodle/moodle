@core @core_backup
Feature: Restore Moodle 2 course backups
  In order to continue using my stored course contents
  As a teacher and an admin
  I need to restore them inside other Moodle courses or in new courses

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | numsections | coursedisplay | initsections |
      | Course 1 | C1        | 0        | topics | 15          | 1             | 1            |
      | Course 2 | C2        | 0        | topics | 5           | 0             | 1            |
      | Course 3 | C3        | 0        | topics | 2           | 0             | 1            |
      | Course 4 | C4        | 0        | topics | 20          | 0             | 1            |
      | Course 5 | C5        | 0        | topics | 15          | 1             | 0            |
    And the following "activities" exist:
      | activity | course | idnumber | name               | intro                | section | externalurl           |
      | assign   | C3     | assign1  | Test assign name   | Assign description   | 1       |                       |
      | data     | C3     | data1    | Test database name | Database description | 2       |                       |
      | forum    | C1     | 0001     | Test forum name    |                      | 1       |                       |
      | url      | C1     | url1     | Test URL name      | Test URL description | 3       | http://www.moodle.org |
      | forum    | C5     | 0005     | Test forum name    |                      | 1       |                       |
      | url      | C5     | url5     | Test URL name      | Test URL description | 3       | http://www.moodle.org |
    And the following "blocks" exist:
      | blockname        | contextlevel | reference | pagetypepattern | defaultregion |
      | activity_modules | Course       | C1        | course-view-*   | side-pre      |
      | activity_modules | Course       | C5        | course-view-*   | side-pre      |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Restore a course in another existing course
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
    Then I should see "Course 2"
    And I should see "Activities" in the "Activities" "block"
    And I should see "Test forum name"

  @javascript
  Scenario: Restore a course in a new course
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 1 restored in a new course |
    Then I should see "Course 1 restored in a new course"
    And I should see "Activities" in the "Activities" "block"
    And I should see "Test forum name"
    And I should see "Section 15"
    And I should not see "Section 16"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "id_format" matches value "Custom sections"
    And I press "Cancel"

  @javascript
  Scenario: Restore a backup into the same course
    When I backup "Course 3" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
      | Schema | Test database name | 0 |
      | Schema | Section 2 | 0 |
    Then I should see "Course 2"
    And I should see "Test assign name"
    And I should not see "Test database name"

  @javascript
  Scenario: Restore a backup into the same course removing it's contents before that
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And the following "activity" exists:
      | activity | forum                              |
      | course   | C1                                 |
      | section  | 1                                  |
      | name     | Test forum post backup name        |
    And I am on the "Course 1" "restore" page
    And I merge "test_backup.mbz" backup into the current course after deleting it's contents using this options:
      | Schema | Section 3 | 0 |
    Then I should see "Course 1"
    And I should not see "Section 3"
    And I should not see "Test forum post backup name"
    And I should see "Activities" in the "Activities" "block"
    And I should see "Test forum name"

  @javascript
  Scenario: Restore a backup into a new course changing the course format afterwards
    Given I backup "Course 5" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    When I restore "test_backup.mbz" backup into a new course using this options:
    Then I should see "New section"
    And I should see "Test forum name"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "id_format" matches value "Custom sections"
    And I set the following fields to these values:
      | id_startdate_day   | 1               |
      | id_startdate_month | January         |
      | id_startdate_year  | 2020            |
      | id_format          | Weekly sections |
      | id_enddate_enabled | 0               |
    And I press "Save and display"
    And I should see "1 January - 7 January"
    And I should see "Test forum name"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "id_format" matches value "Weekly sections"
    And I set the following fields to these values:
      | id_format | Social |
    And I press "Save and display"
    And I should see "An open forum for chatting about anything you want to"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "id_format" matches value "Social"
    And I press "Cancel"

  @javascript
  Scenario: Restore a backup in an existing course retaining the backup course settings
    Given I hide section "3"
    And I hide section "7"
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
      | Schema | Overwrite course configuration | Yes |
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "id_format" matches value "Custom sections"
    And the field "Course layout" matches value "Show one section per page"
    And the field "Course short name" matches value "C1_1"
    And I press "Cancel"
    And section "3" should be visible
    And section "7" should be hidden
    And section "15" should be visible
    And I should see "Section 15"
    And I should not see "Section 16"
    And I should see "Test URL name" in the "Section 3" "section"
    And I should see "Test forum name" in the "Section 1" "section"

  @javascript
  Scenario: Restore a backup in an existing course keeping the target course settings
    Given I hide section "3"
    And I hide section "7"
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
      | Schema | Overwrite course configuration | No |
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "id_format" matches value "Custom sections"
    And the field "Course short name" matches value "C2"
    And the field "Course layout" matches value "Show all sections on one page"
    And I press "Cancel"
    And section "3" should be visible
    And section "7" should be hidden
    And section "15" should be visible
    And I should see "Section 15"
    And I should not see "Section 16"
    And I should see "Test URL name" in the "Section 3" "section"
    And I should see "Test forum name" in the "Section 1" "section"

  @javascript
  Scenario: Restore a backup in an existing course deleting contents and retaining the backup course settings
    Given I hide section "3"
    And I hide section "7"
    When I backup "Course 1" course using this options:
      | Initial |  Include enrolled users | 0 |
      | Confirmation | Filename | test_backup.mbz |
    And I am on the "Course 2" "restore" page
    And I merge "test_backup.mbz" backup into the current course after deleting it's contents using this options:
      | Schema | Overwrite course configuration | Yes |
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "id_format" matches value "Custom sections"
    And the field "Course layout" matches value "Show one section per page"
    And the field "Course short name" matches value "C1_1"
    And I press "Cancel"
    And section "3" should be hidden
    And section "7" should be hidden
    And section "15" should be visible
    And I should see "Section 15"
    And I should not see "Section 16"
    And I should see "Test URL name" in the "Section 3" "section"
    And I should see "Test forum name" in the "Section 1" "section"

  @javascript
  Scenario: Restore a backup in an existing course deleting contents and keeping the current course settings
    Given I hide section "3"
    And I hide section "7"
    When I backup "Course 1" course using this options:
      | Initial |  Include enrolled users | 0 |
      | Confirmation | Filename | test_backup.mbz |
    And I am on the "Course 2" "restore" page
    And I merge "test_backup.mbz" backup into the current course after deleting it's contents using this options:
      | Schema | Overwrite course configuration | No |
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "id_format" matches value "Custom sections"
    And the field "Course short name" matches value "C2"
    And the field "Course layout" matches value "Show all sections on one page"
    And I press "Cancel"
    And section "3" should be hidden
    And section "7" should be hidden
    And section "15" should be visible
    And I should see "Section 15"
    And I should not see "Section 16"
    And I should see "Test URL name" in the "Section 3" "section"
    And I should see "Test forum name" in the "Section 1" "section"

  @javascript
  Scenario: Restore a backup in an existing course deleting contents decreasing the number of sections
    Given I hide section "3"
    And I hide section "7"
    When I backup "Course 1" course using this options:
      | Initial |  Include enrolled users | 0 |
      | Confirmation | Filename | test_backup.mbz |
    And I am on the "Course 4" "restore" page
    And I merge "test_backup.mbz" backup into the current course after deleting it's contents using this options:
      | Schema | Overwrite course configuration | No |
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "id_format" matches value "Custom sections"
    And the field "Course short name" matches value "C4"
    And the field "Course layout" matches value "Show all sections on one page"
    And I press "Cancel"
    And section "3" should be hidden
    And section "7" should be hidden
    And section "15" should be visible
    And I should see "Section 15"
    And I should not see "Section 16"
    And I should see "Test URL name" in the "Section 3" "section"
    And I should see "Test forum name" in the "Section 1" "section"

  @javascript
  Scenario: Restore a backup with override permission
    Given the following "permission overrides" exist:
      | capability         | permission | role           | contextlevel | reference |
      | enrol/manual:enrol | Allow      | teacher        | Course       | C1        |
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings | Include permission overrides | 1 |
    Then I am on the "Course 1 copy 1" "permissions" page
    And I should see "Non-editing teacher (1)"
    And I set the field "Advanced role override" to "Non-editing teacher (1)"
    And "enrol/manual:enrol" capability has "Allow" permission

  @javascript
  Scenario: Restore a backup without override permission
    Given the following "permission overrides" exist:
      | capability         | permission | role           | contextlevel | reference |
      | enrol/manual:enrol | Allow      | teacher        | Course       | C1        |
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings | Include permission overrides | 0 |
    Then I am on the "Course 1 copy 1" "permissions" page
    And I should see "Non-editing teacher (0)"

  @javascript @core_badges
  Scenario Outline: Restore course badges
    Given the following "core_badges > Badges" exist:
      | name                                      | course | description       | image                        | status | type |
      | Published course badge                    | C1     | Badge description | badges/tests/behat/badge.png | active | 2    |
      | Unpublished course badge                  | C1     | Badge description | badges/tests/behat/badge.png | 0      | 2    |
      | Unpublished without criteria course badge | C1     | Badge description | badges/tests/behat/badge.png | 0      | 2    |
    And the following "core_badges > Criterias" exist:
      | badge                    | role           |
      | Published course badge   | editingteacher |
      | Unpublished course badge | editingteacher |
    And I backup "Course 1" course using this options:
      | Initial      | Include badges                   | 1                   |
      | Initial      | Include activities and resources | <includeactivities> |
      | Initial      | Include enrolled users           | 0                   |
      | Initial      | Include blocks                   | 0                   |
      | Initial      | Include files                    | 0                   |
      | Initial      | Include filters                  | 0                   |
      | Initial      | Include calendar events          | 0                   |
      | Initial      | Include question bank            | 0                   |
      | Initial      | Include groups and groupings     | 0                   |
      | Initial      | Include competencies             | 0                   |
      | Initial      | Include custom fields            | 0                   |
      | Initial      | Include calendar events          | 0                   |
      | Initial      | Include content bank content     | 0                   |
      | Initial      | Include legacy course files      | 0                   |
      | Confirmation | Filename                         | test_backup.mbz     |
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Settings | Include badges | 1 |
    And I navigate to "Badges" in current page administration
    Then I should see "Published course badge"
    And I should see "Unpublished course badge"
    And I should see "Unpublished without criteria course badge"
    # If activities were included, the criteria have been restored too; otherwise no criteria have been set up for badges.
    And I <shouldornotsee> "Criteria for this badge have not been set up yet" in the "Published course badge" "table_row"
    And I <shouldornotsee> "Criteria for this badge have not been set up yet" in the "Unpublished course badge" "table_row"
    And I should see "Criteria for this badge have not been set up yet" in the "Unpublished without criteria course badge" "table_row"

    Examples:
      | includeactivities | shouldornotsee |
      | 0                 | should see     |
      | 1                 | should not see |
