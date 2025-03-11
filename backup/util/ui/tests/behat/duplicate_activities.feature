@core @core_backup
Feature: Duplicate activities
  In order to set up my course contents quickly
  As a teacher
  I need to duplicate activities inside the same course

  Scenario: Duplicate an activity
    Given the following "course" exists:
      | fullname     | Course 1 |
      | shortname    | C1       |
      | category     | 0        |
      | initsections | 1        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro                     | course | idnumber   | section |
      | data     | Test database name | Test database description | C1     | database1  | 1       |
    And the following config values are set as admin:
      | backup_import_activities | 0 | backup |
    And the following "core_badges > Badges" exist:
      | name            | course | description       | image                        | status | type |
      | My course badge | C1     | Badge description | badges/tests/behat/badge.png | active | 2    |
    And the following "core_badges > Criterias" exist:
      | badge             | role           |
      | My course badge   | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I duplicate "Test database name" activity
    And I should see "Test database name (copy)"
    And I wait until section "1" is available
    And I click on "Edit settings" "link" in the "Test database name" activity
    And I set the following fields to these values:
      | Name | Original database name |
    And I press "Save and return to course"
    And I click on "Edit settings" "link" in the "Test database name (copy)" activity
    And I set the following fields to these values:
      | Name | Duplicated database name |
      | Description | Duplicated database description |
    And I press "Save and return to course"
    Then I should see "Original database name" in the "Section 1" "section"
    And I should see "Duplicated database name" in the "Section 1" "section"
    And "Original database name" "link" should appear before "Duplicated database name" "link"
    # Check that badges are not duplicated. If they are duplicated, they will appear as "Not available".
    And I navigate to "Badges" in current page administration
    And the following should not exist in the "reportbuilder-table" table:
      | Name             | Badge status  |
      | My course badge  | Not available |
