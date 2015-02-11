@core @core_grades
Feature: We can set the grade to pass value
  In order to set the grade to pass value
  As a teacher
  I assign a grade to pass to an activity while editing the activity.
  I need to ensure that the grade to pass is visible in the gradebook.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1 | weeks | 5 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test Assignment 1 |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |

  @javascript
  Scenario: Validate that switching the type of grading used correctly disables grade to pass
    When I follow "Test Assignment 1"
    And I follow "Edit settings"
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Point"
    Then the "Grade to pass" "field" should be enabled
    And I set the field "grade[modgrade_type]" to "None"
    Then the "Grade to pass" "field" should be disabled
    And I press "Save and return to course"

  @javascript
  Scenario: Create an activity with a Grade to pass value greater than the maximum grade
    When I follow "Test Assignment 1"
    And I follow "Edit settings"
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Point"
    And I set the field "grade[modgrade_point]" to "50"
    And I press "Save and display"
    And I follow "Edit settings"
    And I expand all fieldsets
    And I set the field "Grade to pass" to "100"
    And I press "Save and display"
    Then I should see "The grade to pass is greater than the grade"
    And I press "Cancel"

  @javascript
  Scenario: Set a valid grade to pass for an assignment and workshop activity
    When I follow "Test Assignment 1"
    And I follow "Edit settings"
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "point"
    And I set the field "grade[modgrade_point]" to "50"
    And I set the field "Grade to pass" to "25"
    And I press "Save and display"
    And I follow "View gradebook"
    And I turn editing mode on
    And I click on "Edit  assign Test Assignment 1" "link"
    Then I should see "Edit grade item"
    Then the field "Grade to pass" matches value "25"
    And I follow "Course 1"
    And I add a "Workshop" to section "1" and I fill the form with:
      | Workshop name | Test Workshop 1 |
      | Description | Test workshop |
      | grade | 80 |
      | Grade to pass for submission | 40 |
      | gradinggrade | 20 |
      | Grade to pass for assessment | 10 |
    And I follow "Grades"
    And I click on "Edit  workshop Test Workshop 1 (submission)" "link"
    Then the field "Grade to pass" matches value "40"
