@mod @mod_assign
Feature: As a teacher in course with relative dates mode enabled
I should be able to create an assignment with a due date relative to the course start date
So that students can enter the course at any time and have a fixed period in which to submit the assignment

  Scenario: As a student the due date for submitting my assignment is relative to my course start date
    Given the following config values are set as admin:
      | enablecourserelativedates | 1 |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode | relativedatesmode | startdate |
      | Course 1 | C1 | 0 | 1 | 1 | ##first day of -4 months##                     |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student1@example.com |
    And the following "course enrolments" exist:
     # Two students, one started 4 months ago and one this month.
      | user | course | role | timestart |
      | teacher1 | C1 | editingteacher | ##first day of last month## |
      | student1 | C1 | student | ##first day of -4 months##        |
      | student2 | C1 | student | ##first day of this month##        |
     # One assignment, valid for 2 months.
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    | assignsubmission_onlinetext_enabled | timeopen | duedate |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign0     | 1                                   |##first day of -4 months## | ##last day of -3 months## |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "Assignment is overdue by:" in the "Time remaining" "table_row"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should not see "Assignment is overdue by:" in the "Time remaining" "table_row"
    And I log out

  Scenario: As a teacher, I should see the relative dates when reviewing assignment submissions
    Given the following config values are set as admin:
      | enablecourserelativedates | 1 |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode | relativedatesmode | startdate |
      | Course 1 | C1 | 0 | 1 | 1 | ##first day of 4 months ago##                     |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student1@example.com |
    And the following "course enrolments" exist:
     # Two students, one started 4 months ago and one this month.
      | user | course | role | timestart |
      | teacher1 | C1 | editingteacher | ##first day of 4 months ago## |
      | student1 | C1 | student | ##first day of 4 months ago##        |
      | student2 | C1 | student | ##first day of this month##        |
     # One assignment, valid for 2 months.
    And the following "activities" exist:
      | activity   | name                   | intro                         | course | idnumber    | assignsubmission_onlinetext_enabled | timeopen | duedate |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign0     | 1                                   |##first day of 4 months ago## | ##last day of 3 months ago## |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "after course start" in the "Due date" "table_row"
    And I should see "Calculated for each student" in the "Time remaining" "table_row"
    When I navigate to "View all submissions" in current page administration
    Then I should see "No submission" in the "Student 1" "table_row"
    And I should see "Assignment is overdue by:" in the "Student 1" "table_row"
    And I should see "No submission" in the "Student 2" "table_row"
    And I should not see "Assignment is overdue by:" in the "Student 2" "table_row"
    And I log out
