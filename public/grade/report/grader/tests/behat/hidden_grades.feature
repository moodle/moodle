@gradereport @gradereport_grader
Feature: We don't show hidden grades for users without the 'moodle/grade:viewhidden' capability on grader report
  In order to show grader report in secure way
  As a teacher without the 'moodle/grade:viewhidden' capability
  I should not see hidden grades in the grader report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | name                   | intro                   | assignsubmission_onlinetext_enabled | submissiondrafts |
      | assign   | C1     | 1       | Test assignment name 1 | Submit your online text | 1                                   | 0                |
      | assign   | C1     | 1       | Test assignment name 2 | submit your online text | 1                                   | 0                |
      | assign   | C1     | 1       | Test assignment name 3 | submit your online text | 1                                   | 0                |
    # Hidden manual grade item.
    And the following "grade items" exist:
      | itemname     | grademin | grademax | course | hidden |
      | Manual grade | 20       | 40       | C1     | 1      |
    And the following "grade grades" exist:
      | gradeitem              | user     | grade |
      | Test assignment name 1 | student1 | 80    |
      | Test assignment name 1 | student2 | 70    |
      | Test assignment name 2 | student1 | 90    |
      | Test assignment name 2 | student2 | 60    |
      | Test assignment name 3 | student1 | 10    |
      | Test assignment name 3 | student2 | 50    |
      | Manual grade           | student1 | 30    |
      | Manual grade           | student2 | 40    |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    # Hide assignment 2 activity.
    And I open "Test assignment name 2" actions menu
    And I choose "Hide" in the open action menu
    And I navigate to "View > Grader report" in the course gradebook
    # Hide grade.
    And I click on grade menu "Test assignment name 1" for user "student1"
    And I choose "Hide" in the open action menu
    # Hide assignment 3 grade item.
    And I set the following settings for grade item "Test assignment name 3" of type "gradeitem" on "grader" page:
      | Hidden          | 1 |

  @javascript
  Scenario: View grader report containing hidden activities or grade items or grades
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And the following should exist in the "user-grades" table:
      | -1-                | -2-                  | -3-       | -4-       | -5-       | -6-       | -7-       |
      | Student 1          | student1@example.com | 80        | 90        | 10        | 30        | 210       |
      | Student 2          | student2@example.com | 70        | 60        | 50        | 40        | 220       |
    And I turn editing mode on

    And the field "Student 1 Test assignment name 1 grade" matches value "80"
    And the field "Student 1 Test assignment name 2 grade" matches value "90"
    And the field "Student 1 Test assignment name 3 grade" matches value "10"
    And the field "Student 1 Manual grade" matches value "30"
    And the field "Student 1 Course total grade" matches value "210"
    And "Hidden" "icon" should exist in the "Student 1" "table_row"

    And the field "Student 2 Test assignment name 1 grade" matches value "70"
    And the field "Student 2 Test assignment name 2 grade" matches value "60"
    And the field "Student 2 Test assignment name 3 grade" matches value "50"
    And the field "Student 2 Manual grade" matches value "40"
    And the field "Student 2 Course total grade" matches value "220"
    And "Hidden" "icon" should exist in the "Student 2" "table_row"

    # Remove the capability to see hidden grades.
    When the following "role capability" exists:
      | role                    | editingteacher  |
      | moodle/grade:viewhidden | prohibit        |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode off
    And the following should exist in the "user-grades" table:
      | -1-                | -2-                  | -3-       | -4-       | -5-       | -6-       | -7-       |
      | Student 1          | student1@example.com | -         | -         | -         | -         | -         |
      | Student 2          | student2@example.com | 70        | -         | -         | -         | 70        |
    And "Hidden" "icon" should not exist in the "Student 1" "table_row"
    And "Hidden" "icon" should not exist in the "Student 2" "table_row"
    And I turn editing mode on
    And the following should exist in the "user-grades" table:
      | -1-                | -2-                  | -3-       | -4-       | -5-       | -6-       |
      | Student 1          | student1@example.com | -         | -         | -         | -         |
    And the following should exist in the "user-grades" table:
      | -1-                | -2-                  |  -4-       | -5-       | -6-       |
      | Student 2          | student2@example.com |  -         | -         | -         |
    # Check how totals should behave!!!!!!!!!!!
    And the field "Student 1 Course total grade" matches value ""
    And the field "Student 2 Test assignment name 1 grade" matches value "70"
    And the field "Student 2 Course total grade" matches value "70"
    And "Hidden" "icon" should not exist in the "Student 1" "table_row"
    Then "Hidden" "icon" should not exist in the "Student 2" "table_row"
