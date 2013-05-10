@core @core_backup
Feature: Duplicate activities
  In order to set up my course contents quickly
  As a teacher
  I need to duplicate activities inside the same course

  @javascript
  Scenario: Duplicate an activity
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Database" to section "1" and I fill the form with:
      | Name | Test database name |
      | Description | Test database description |
    When I click on "Duplicate" "link" in the "#section-1" "css_element"
    And I press "Continue"
    And I press "Edit the new copy"
    And I fill the moodle form with:
      | Name | Duplicated database name |
      | Description | Duplicated database description |
    And I press "Save and return to course"
    Then I should see "Test database name" in the "#section-1" "css_element"
    And I should see "Duplicated database name" in the "#section-1" "css_element"
    And "Test database name" "link" should appear before "Duplicated database name" "link"
