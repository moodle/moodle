@mod @mod_assign
Feature: In an assignment, teachers can change filters in the grading app
  In order to manage submissions more easily
  As a teacher
  I need to preserve filter settings between the grader app and grading table.

  Background:
    Given the following "courses" exist:
      | fullname   | shortname | category | groupmode |
      | Course 1 & | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | marker1  | Marker    | 1        | marker1@example.com  |
      | marker2  | Marker    | 2        | marker2@example.com  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | marker1  | C1     | teacher        |
      | marker2  | C1     | teacher        |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name &  |
      | description                         | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | markingworkflow                     | 1                       |
      | markingallocation                   | 1                       |

  @javascript
  Scenario: Set filters in the grading table and see them in the grading app
    Given I am on the "Test assignment name &" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I should not see "Course 1 &amp;"
    And the "title" attribute of "a[title='Course: Course 1 &']" "css_element" should not contain "&amp;"
    And I should not see "Test assignment name &amp;"
    And I set the field "allocatedmarker" to "Marker 1"
    And I set the field "workflowstate" to "In marking"
    And I set the field "Notify student" to "0"
    And I press "Save changes"

    And I am on the "Test assignment name &" Activity page
    And I follow "View all submissions"
    And I set the field "filter" to "Not submitted"
    And I set the field "markerfilter" to "Marker 1"
    And I set the field "workflowfilter" to "In marking"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    Then the field "filter" matches value "Not submitted"
    And the field "markerfilter" matches value "Marker 1"
    And the field "workflowfilter" matches value "In marking"

  @javascript
  Scenario: Set filters in the grading app and see them in the grading table
    Given I am on the "Test assignment name &" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "allocatedmarker" to "Marker 1"
    And I set the field "workflowstate" to "In marking"
    And I set the field "Notify student" to "0"
    And I press "Save changes"

    And I am on the "Test assignment name &" Activity page
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I click on "[data-region=user-filters]" "css_element"
    And I set the field "filter" to "Not submitted"
    And I set the field "markerfilter" to "Marker 1"
    And I set the field "workflowfilter" to "In marking"
    And I click on "View all submissions" "link"
    Then the field "filter" matches value "Not submitted"
    And the field "markerfilter" matches value "Marker 1"
    And the field "workflowfilter" matches value "In marking"
