@mod @mod_assign
Feature: Relative assignment due dates
In order for students to be able to enter the course at any time and have a fixed period in which to submit the assignment
As a teacher in course with relative dates mode enabled
I should be able to create an assignment with a due date relative to the course start date

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
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
     # Two students, one started 4 months ago and one yesterday.
      | user | course | role | timestart |
      | teacher1 | C1 | editingteacher | ##first day of last month## |
      | student1 | C1 | student | ##first day of -4 months## |
      | student2 | C1 | student | ##yesterday## |
     # One assignment, valid for 2 months.
    And the following "activities" exist:
      | activity  | name                  | course  | assignsubmission_onlinetext_enabled  | timeopen                    | duedate                    |
      | assign    | Test assignment name  | C1      | 1                                    | ##first day of -4 months##  | ##last day of -3 months##  |

    When I am on the "Test assignment name" Activity page logged in as student1
    Then I should see "Assignment is overdue by:" in the "Time remaining" "table_row"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student2
    And I should not see "Assignment is overdue by:" in the "Time remaining" "table_row"

  Scenario: As a student the due date I see for submitting my assignment is relative to my course start date
    Given the following config values are set as admin:
      | enablecourserelativedates | 1 |
    And the following "courses" exist:
    # A course with start date set to 1 Jan 2021.
      | fullname | shortname  | category  | groupmode | relativedatesmode | startdate   |
      | Course 1 | C1         | 0         | 1         | 1                 | 1609459200  |
    And the following "users" exist:
      | username | firstname  | lastname  | email                 |
      | student1 | Student    | 1         | student1@example.com  |
    And the following "course enrolments" exist:
    # User's enrolment starts from 5 Jan 2021.
      | user      | course  | role    | timestart   |
      | student1  | C1      | student | 1609804800  |
    And the following "activities" exist:
    # The assignment's due date is 3 Jan 2021.
      | activity  | name                  | course  | assignsubmission_onlinetext_enabled | duedate     |
      | assign    | Test assignment name  | C1      | 1                                   | 1609632000  |

    When I am on the "Test assignment name" Activity page logged in as student1
    Then the activity date in "Test assignment name" should contain "Due: Thursday, 7 January 2021, 8:00"

  Scenario: As a teacher, I should see the relative dates when reviewing assignment submissions
    Given the following config values are set as admin:
      | enablecourserelativedates | 1 |
    And the following "courses" exist:
      | fullname  | shortname  | category  | groupmode  | relativedatesmode  | startdate                      |
      | Course 1  | C1         | 0         | 1          | 1                  | ##first day of 4 months ago##  |
    And the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
      | student2  | Student    | 2         | student2@example.com  |
    And the following "course enrolments" exist:
     # Two students, one started 4 months ago and one yesterday.
      | user      | course  | role            | timestart                      |
      | teacher1  | C1      | editingteacher  | ##first day of 4 months ago##  |
      | student1  | C1      | student         | ##first day of 4 months ago##  |
      | student2  | C1      | student         | ##yesterday##                  |
     # One assignment, valid for 2 months.
    And the following "activities" exist:
      | activity  | name                  | course  | assignsubmission_onlinetext_enabled  | timeopen                       | duedate                       |
      | assign    | Test assignment name  | C1      | 1                                    | ##first day of 4 months ago##  | ##last day of 3 months ago##  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And the activity date in "Test assignment name" should contain "after course start"
    And I should see "Calculated for each student" in the "Time remaining" "table_row"
    When I follow "View all submissions"
    Then I should see "No submission" in the "Student 1" "table_row"
    And I should see "Assignment is overdue by:" in the "Student 1" "table_row"
    And I should see "No submission" in the "Student 2" "table_row"
    And I should not see "Assignment is overdue by:" in the "Student 2" "table_row"
