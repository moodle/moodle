@core @core_grades
Feature: We can change the grading type and maximum grade point values
  In order to verify that we can change the system-level maximum grade point value
  As an admin
  I need to modify the system maximum grade point and ensure that activities can use the full range.
  I need to ensure that using scales for activities still works correctly.
  I need to ensure that the maximum grade point value is enforced for new and existing activities.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | topics |
    Given the following "activities" exist:
      | activity | course | idnumber | name | intro |
      | assign | C1 | assign1| Test Assignment 1 | Test Assignment 1 |
    And I log in as "admin"
    And I navigate to "General settings" node in "Site administration > Grades"
    And I set the following fields to these values:
      | Grade point maximum | 900 |
      | Grade point default | 800 |
    And I press "Save changes"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Validate that switching the type of grading used correctly disables input form elements
    When I follow "Test Assignment 1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Point"
    Then the "Scale" "select" should be disabled
    And the "Maximum grade" "field" should be enabled
    And I set the field "grade[modgrade_type]" to "Scale"
    And the "Maximum grade" "field" should be disabled
    Then the "Scale" "select" should be enabled
    And I set the field "grade[modgrade_type]" to "None"
    Then the "Scale" "select" should be disabled
    And the "Maximum grade" "field" should be disabled
    And I press "Save and return to course"

  @javascript
  Scenario: Create an activity with a maximum grade point value less than the system maximum
    When I follow "Test Assignment 1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "point"
    And I set the field "grade[modgrade_point]" to "600"
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    Then the field "grade[modgrade_point]" matches value "600"
    And the "Scale" "select" should be disabled
    And I press "Save and return to course"

  @javascript
  Scenario: Create an activity with a scale as the grade type
    When I follow "Test Assignment 1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Scale"
    And I set the field "grade[modgrade_scale]" to "Separate and Connected ways of knowing"
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    Then the field "grade[modgrade_scale]" matches value "Separate and Connected ways of knowing"
    And the "Maximum grade" "field" should be disabled
    And I press "Save and return to course"

  @javascript
  Scenario: Create an activity with no grade as the grade type
    When I follow "Test Assignment 1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "None"
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    And the "Scale" "select" should be disabled
    And the "Maximum grade" "field" should be disabled
    And I press "Save and return to course"

  Scenario: Create an activity with a maximum grade point value higher than the system maximum
    When I follow "Test Assignment 1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Point"
    And I set the field "grade[modgrade_point]" to "20000"
    And I press "Save and display"
    Then I should see "Invalid grade value. This must be an integer between 1 and 900"
    And I press "Cancel"

  Scenario: Create an activity with a valid maximum grade point and then change the system maximum to be lower
    When I follow "Test Assignment 1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "point"
    And I set the field "grade[modgrade_point]" to "600"
    And I press "Save and display"
    And I navigate to "General settings" node in "Site administration > Grades"
    And I set the following fields to these values:
      | Grade point maximum | 100 |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Test Assignment 1"
    And I navigate to "Edit settings" in current page administration
    And I press "Save and display"
    Then I should see "Invalid grade value. This must be an integer between 1 and 100"
    And I press "Cancel"
