@mod @mod_assign
Feature: Assignment submission count includes submissions from active students in the course
  In order to show accurate submission counts
  As a teacher
  I should see submissions from active students in the course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name         | submissiondrafts | assignsubmission_onlinetext_enabled |
      | assign   | C1     | Assignment 1 | 0                | 1                                   |
    And the following "mod_assign > submissions" exist:
      | assign       | user     | onlinetext              |
      | Assignment 1 | student1 | Submission by student 1 |
      | Assignment 1 | student2 | Submission by student 2 |
      | Assignment 1 | student3 | Submission by student 3 |

  @javascript
  Scenario: Submission count excludes submissions from unenrolled students
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I navigate to course participants
    And I click on "Unenrol" "icon" in the "student3" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    And I should not see "Student 3" in the "participants" "table"
    When I am on the "Course 1" "course > activities > assign" page
    Then the following should exist in the "Table listing all Assignment activities" table:
      | Name         | Submissions |
      | Assignment 1 | 2 of 2      |
    And I am on "Course 1" course homepage
    And I follow "Assignment 1"
    And I should see "2" in the "Submitted" "table_row"
