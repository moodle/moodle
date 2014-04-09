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
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | marker1 | Marker | 1 | marker1@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | marker1 | C1 | teacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | Use marking workflow | Yes |
      | Use marking allocation | Yes |
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I click on "Grade Student 1" "link" in the "Student 1" "table_row"
    And I set the field "allocatedmarker" to "Marker 1"
    And I click on "Save changes" "button"
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I set the field "markerfilter" to "Marker 1"
    Then I should see "Student 1"
    And I should not see "Student 2"
    And I set the field "markerfilter" to "No marker"
    And I should not see "Student 1"
    And I should see "Student 2"
    And I set the field "markerfilter" to "No filter"
    And I should see "Student 1"
    And I should see "Student 2"
