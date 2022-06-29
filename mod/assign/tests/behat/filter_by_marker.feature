@mod @mod_assign
Feature: In an assignment, teachers can filter displayed submissions by assigned marker
  In order to manage submissions more easily
  As a teacher
  I need to view submissions allocated to markers.

  @javascript
  Scenario: Allocate markers to submissions and filter by marker
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | marker1 | Marker | 1 | marker1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | marker1 | C1 | teacher |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | submissiondrafts                    | 0                       |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | markingworkflow                     | 1                       |
      | markingallocation                   | 1                       |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "allocatedmarker" to "Marker 1"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I click on "Edit settings" "link"

    When I am on the "Test assignment name" Activity page
    And I follow "View all submissions"
    And I set the field "markerfilter" to "Marker 1"
    Then I should see "Student 1"
    And I should not see "Student 2"
    And I set the field "markerfilter" to "No marker"
    And I should not see "Student 1"
    And I should see "Student 2"
    And I set the field "markerfilter" to "No filter"
    And I should see "Student 1"
    And I should see "Student 2"
