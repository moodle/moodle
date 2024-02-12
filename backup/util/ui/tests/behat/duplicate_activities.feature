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
