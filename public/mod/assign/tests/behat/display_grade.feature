@mod @mod_assign @assign_grade
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

  @javascript
  Scenario: Update the grade for an assignment with penalty
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
    And I enable grade penalties for assignment
    And the following "activity" exists:
      | activity                             | assign                      |
      | course                               | C1                          |
      | name                                 | Test assignment name        |
      | intro                                | Test assignment description |
      | grade                                | 100                         |
      | duedate                              | ##yesterday##               |
      | gradepenalty                         | 1                           |
      | assignsubmission_onlinetext_enabled  | 1                           |
      | submissiondrafts                     | 0                           |
    # Add a submission.
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                        |
      | Test assignment name  | student1  | I'm the student first submission  |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I change window size to "large"
    And I go to "Student 1" "Test assignment name" activity advanced grading page
    When I set the field "Grade out of 100" to "90"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    Then the "data-bs-original-title" attribute of ".penalty-indicator-icon" "css_element" should contain "Late penalty applied -10.00 marks"
    And I follow "View all submissions"
    And "Student 1" row "Grade" column of "submissions" table should contain "90.00"
    And "Student 1" row "Final grade" column of "submissions" table should contain "80.00"
    And the "data-bs-original-title" attribute of ".penalty-indicator-icon" "css_element" should contain "Late penalty applied -10.00 marks"
    # Override the grade.
    And I am on the "Course 1" "grades > Grader report > View" page
    And the following should exist in the "user-grades" table:
      | -1-                | -2-                   | -3-       | -4-      |
      | Student 1          | student10@example.com | 80        | 80       |
    And the "data-bs-original-title" attribute of ".penalty-indicator-icon" "css_element" should contain "Late penalty applied -10.00 marks"
    And I turn editing mode on
    And I set the following fields to these values:
      | Student 1 Test assignment name grade | 100 |
    And I click on "Save changes" "button"
    And I turn editing mode off
    And the following should exist in the "user-grades" table:
      | -1-                | -2-                   | -3-       | -4-      |
      | Student 1          | student10@example.com | 100       | 100      |
    And ".penalty-indicator-icon" "css_element" should not exist
    And I go to "Student 1" "Test assignment name" activity advanced grading page
    And ".penalty-indicator-icon" "css_element" should not exist
    And I follow "View all submissions"
    And ".penalty-indicator-icon" "css_element" should not exist
