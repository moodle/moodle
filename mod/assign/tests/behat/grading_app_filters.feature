@mod @mod_assign
Feature: In an assignment, teachers can change filters in the grading app
  In order to manage submissions more easily
  As a teacher
  I need to preserve filter settings between the grader app and grading table.

  @javascript
  Scenario: Set filters in the grading table and see them in the grading app
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | marker1 | Marker | 1 | marker1@example.com |
      | marker2 | Marker | 2 | marker2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | marker1 | C1 | teacher |
      | marker2 | C1 | teacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | Use marking workflow | Yes |
      | Use marking allocation | Yes |
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "allocatedmarker" to "Marker 1"
    And I set the field "workflowstate" to "In marking"
    And I set the field "Notify students" to "0"
    And I press "Save changes"
    And I press "OK"
    And I click on "Edit settings" "link"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I set the field "filter" to "Not submitted"
    And I set the field "markerfilter" to "Marker 1"
    And I set the field "workflowfilter" to "In marking"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    Then the field "filter" matches value "Not submitted"
    And the field "markerfilter" matches value "Marker 1"
    And the field "workflowfilter" matches value "In marking"

  @javascript
  Scenario: Set filters in the grading app and see them in the grading table
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | marker1 | Marker | 1 | marker1@example.com |
      | marker2 | Marker | 2 | marker2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | marker1 | C1 | teacher |
      | marker2 | C1 | teacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | Use marking workflow | Yes |
      | Use marking allocation | Yes |
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "allocatedmarker" to "Marker 1"
    And I set the field "workflowstate" to "In marking"
    And I set the field "Notify students" to "0"
    And I press "Save changes"
    And I press "OK"
    And I click on "Edit settings" "link"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I click on "[data-region=user-filters]" "css_element"
    And I set the field "filter" to "Not submitted"
    # The popup closes for some reason, so it needs to be reopened.
    And I click on "[data-region=user-filters]" "css_element"
    And I set the field "markerfilter" to "Marker 1"
    And I set the field "workflowfilter" to "In marking"
    And I click on "View all submissions" "link"
    Then the field "filter" matches value "Not submitted"
    And the field "markerfilter" matches value "Marker 1"
    And the field "workflowfilter" matches value "In marking"
