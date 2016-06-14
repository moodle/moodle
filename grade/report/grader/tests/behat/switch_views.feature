@gradereport @gradereport_grader
Feature: We can change what we are viewing on the grader report
  In order to check the expected results are displayed
  As a teacher
  I need to assign grades and check that they display correctly in the gradebook when switching between views.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name 1 |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name 2 |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name 1"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | This is a submission for assignment 1 |
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I follow "Course 1"
    And I follow "Test assignment name 2"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | This is a submission for assignment 2 |
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment name 1"
    And I give the grade "90.00" to the user "Student 1" for the grade item "Test assignment name 2"
    And I press "Save changes"
    And I turn editing mode off

  @javascript
  Scenario: View and minimise the grader report containing hidden activities
    And I follow "Course 1"
    And I turn editing mode on
    And I open "Test assignment name 2" actions menu
    And I click on "Hide" "link" in the "Test assignment name 2" activity
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I select "Grader report" from the "Grade report" singleselect
    And I should see "Test assignment name 1"
    And I should see "Test assignment name 2"
    And I should see "Course total"
    And the following should exist in the "user-grades" table:
      | -1-                | -4-       | -5-       | -6-       |
      | Student 1          | 80        | 90        | 170       |
    And I click on "Change to aggregates only" "link"
    And I should not see "Test assignment name 1"
    And I should not see "Test assignment name 2"
    And I should see "Course total"
    And the following should exist in the "user-grades" table:
      | -1-                | -4-       |
      | Student 1          | 170       |
    And I click on "Change to grades only" "link"
    And I should see "Test assignment name 1"
    And I should see "Test assignment name 2"
    And I should not see "Course total"
    And the following should exist in the "user-grades" table:
      | -1-                | -4-       | -5-       |
      | Student 1          | 80        | 90        |

  @javascript
  Scenario: View and minimise the grader report containing hidden activities without the 'moodle/grade:viewhidden' capability
    And I follow "Course 1"
    And I turn editing mode on
    And I open "Test assignment name 2" actions menu
    And I click on "Hide" "link" in the "Test assignment name 2" activity
    And I log out
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability | permission |
      | moodle/grade:viewhidden | Prevent |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I select "Grader report" from the "Grade report" singleselect
    And I should see "Test assignment name 1"
    And I should see "Test assignment name 2"
    And I should see "Course total"
    And the following should exist in the "user-grades" table:
      | -1-                | -4-       | -5-       | -6-       |
      | Student 1          | 80        | -         | 80        |
    And I click on "Change to aggregates only" "link"
    And I should not see "Test assignment name 1"
    And I should not see "Test assignment name 2"
    And I should see "Course total"
    And the following should exist in the "user-grades" table:
      | -1-                | -4-       |
      | Student 1          | 80        |
    And I click on "Change to grades only" "link"
    And I should see "Test assignment name 1"
    And I should see "Test assignment name 2"
    And I should not see "Course total"
    And the following should exist in the "user-grades" table:
      | -1-                | -4-       | -5-       |
      | Student 1          | 80        | -         |
