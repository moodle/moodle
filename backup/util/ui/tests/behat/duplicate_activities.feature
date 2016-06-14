@core @core_backup
Feature: Duplicate activities
  In order to set up my course contents quickly
  As a teacher
  I need to duplicate activities inside the same course

  Scenario: Duplicate an activity
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Database" to section "1" and I fill the form with:
      | Name | Test database name |
      | Description | Test database description |
    And I duplicate "Test database name" activity
    And I wait until section "1" is available
    And I click on "Edit settings" "link" in the "Test database name" activity
    And I set the following fields to these values:
      | Name | Original database name |
    And I press "Save and return to course"
    And I click on "Edit settings" "link" in the "Test database name" activity
    And I set the following fields to these values:
      | Name | Duplicated database name |
      | Description | Duplicated database description |
    And I press "Save and return to course"
    Then I should see "Original database name" in the "Topic 1" "section"
    And I should see "Duplicated database name" in the "Topic 1" "section"
    And "Original database name" "link" should appear before "Duplicated database name" "link"
