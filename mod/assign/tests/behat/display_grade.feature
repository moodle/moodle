@mod @mod_assign
Feature: Check that the assignment grade can be updated correctly
  In order to ensure that the grade is shown correctly in the grading table
  As a teacher
  I need to grade a student and ensure the grade is shown correctly

  @javascript
  Scenario: Update the grade for an assignment
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student10@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "groups" exist:
      | name     | course  | idnumber  |
      | Group 1  | C1      | G1        |
    And the following "activity" exists:
      | activity         | assign                      |
      | course           | C1                          |
      | name             | Test assignment name        |
      | intro            | Test assignment description |
      | markingworkflow  | 1                           |
      | submissiondrafts | 0                           |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    Then I change window size to "large"
    And I go to "Student 1" "Test assignment name" activity advanced grading page
    And I set the field "Grade out of 100" to "50"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    And "Student 1" row "Grade" column of "generaltable" table should contain "50.00"

  @javascript
  Scenario: Update the grade for a team assignment
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student10@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "activity" exists:
      | activity         | assign                      |
      | course           | C1                          |
      | name             | Test assignment name        |
      | intro            | Test assignment description |
      | markingworkflow  | 1                           |
      | submissiondrafts | 0                           |
      | teamsubmission   | 1                           |
      | groupmode        | 0                           |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I change window size to "large"
    And I go to "Student 1" "Test assignment name" activity advanced grading page
    And I change window size to "medium"
    And I set the field "Grade out of 100" to "50"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    Then "Student 1" row "Grade" column of "generaltable" table should contain "50.00"
