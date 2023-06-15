@mod @mod_assign
Feature: Set availability dates for an assignment
    In order to control when a student can upload an assignment
    As a teacher
    I need be able to set availability dates for an assignment

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
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

  Scenario: Student cannot submit an assignment prior to the 'allow submissions from' date
    Given the following "activity" exists:
      | activity                            | assign            |
      | course                              | C1                |
      | name                                | Assignment name   |
      | assignsubmission_onlinetext_enabled | 1                 |
      | assignsubmission_file_enabled       | 0                 |
      | submissiondrafts                    | 0                 |
      | allowsubmissionsfromdate            | ##tomorrow noon## |
    When I am on the "Assignment name" Activity page logged in as student1
    Then "Add submission" "button" should not exist
    And I should see "This assignment will accept submissions from"
    And I should see "##tomorrow noon##%A, %d %B %Y, %I:%M %p##"

  @javascript
  Scenario: Student can see the assignment's due date in the course calendar
    Given the following "activity" exists:
      | activity                            | assign                                     |
      | course                              | C1                                         |
      | name                                | Assignment name                            |
      | assignsubmission_onlinetext_enabled | 1                                          |
      | assignsubmission_file_enabled       | 0                                          |
      | submissiondrafts                    | 0                                          |
      | allowsubmissionsfromdate            | ##first day of this month noon##           |
      | duedate                             | ##first day of this month noon +24 hours## |
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | calendar_month | Course       | C1        | course-view-*   | site-post     |
    When I am on the "C1" Course page logged in as student1
    And I hover over day "2" of this month in the calendar
    Then I should see "C1: Assignment name is due"

  Scenario: Student can submit an assignment before the due date
    Given the following "activity" exists:
      | activity                            | assign                         |
      | course                              | C1                             |
      | name                                | Assignment name                |
      | assignsubmission_onlinetext_enabled | 1                              |
      | assignsubmission_file_enabled       | 0                              |
      | submissiondrafts                    | 0                              |
      | allowsubmissionsfromdate            | ##now##                        |
      | duedate                             | ##+2 days 5 hours 30 minutes## |
    When I am on the "Assignment name" Activity page logged in as student1
    And I should see "##+2 days 5 hours 30 minutes##%A, %d %B %Y##" in the "Due date" "table_row"
    And I should see "2 days 5 hours" in the "Time remaining" "table_row"
    And "Add submission" "button" should exist
    And I press "Add submission"
    And I set the field "Online text" to "This is my submission"
    And I press "Save changes"
    And I should see "Submitted for grading" in the "Submission status" "table_row"

    And I am on the "Assignment name" Activity page logged in as teacher1
    And I should see "1" in the "Submitted" "table_row"
    And I navigate to "View all submissions" in current page administration
    And I should see "Submitted for grading" in the "Student 1" "table_row"

  Scenario: Student can submit an assignment after the due date and the submission is marked as late
    Given the following "activity" exists:
      | activity                            | assign                            |
      | course                              | C1                                |
      | name                                | Assignment name                   |
      | assignsubmission_onlinetext_enabled | 1                                 |
      | assignsubmission_file_enabled       | 0                                 |
      | submissiondrafts                    | 0                                 |
      | allowsubmissionsfromdate            | ##3 days ago##                    |
      | duedate                             | ##2 days 5 hours 30 minutes ago## |
      | cutoffdate                          | ##tomorrow noon##                 |
    When I am on the "Assignment name" Activity page logged in as student1
    And I should see "##2 days 5 hours 30 minutes ago##%A, %d %B %Y##" in the "Due date" "table_row"
    And I should see "Assignment is overdue by: 2 days 5 hours" in the "Time remaining" "table_row"
    And "Add submission" "button" should exist
    And I press "Add submission"
    And I set the field "Online text" to "This is my submission"
    And I press "Save changes"
    And I should see "Submitted for grading" in the "Submission status" "table_row"
    And I should see "Assignment was submitted 2 days 5 hours late" in the "Time remaining" "table_row"

    And I am on the "Assignment name" Activity page logged in as teacher1
    And I should see "1" in the "Submitted" "table_row"
    And I navigate to "View all submissions" in current page administration
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "2 days 5 hours late" in the "Student 1" "table_row"

  Scenario: Student cannot submit an assignment after the cut-off date
    Given the following "activity" exists:
      | activity                           | assign                            |
      | course                             | C1                                |
      | name                               | Assignment name                   |
      | assignsubmission_file_enabled      | 1                                 |
      | assignsubmission_file_maxfiles     | 1                                 |
      | assignsubmission_file_maxsizebytes | 0                                 |
      | submissiondrafts                   | 0                                 |
      | allowsubmissionsfromdate           | ##3 days ago##                    |
      | duedate                            | ##2 days 5 hours 30 minutes ago## |
      | cutoffdate                         | ##yesterday noon##                |
    When I am on the "Assignment name" Activity page logged in as student1
    Then "Add submission" "button" should not exist

    And I am on the "Assignment name" Activity page logged in as teacher1
    And I should see "0" in the "Submitted" "table_row"
    And I navigate to "View all submissions" in current page administration
    And I should see "No submission" in the "Student 1" "table_row"
    And I should see "Assignment is overdue by: 2 days 5 hours" in the "Student 1" "table_row"
