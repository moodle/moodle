@mod @mod_assign
Feature: Group assignment submissions
  In order to allow students to work collaboratively on an assignment
  As a teacher
  I need to group submissions in groups

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student0 | Student   | 0        | student0@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student0 | C1     | student        |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |

  @javascript
  Scenario: Confirm that group submissions are removed from the timeline
    Given the following "activity" exists:
      | activity                            | assign               |
      | course                              | C1                   |
      | name                                | Test assignment name |
      | assignsubmission_onlinetext_enabled | 1                    |
      | teamsubmission                      | 1                    |
      | duedate                             | ##tomorrow##         |
      | requiresubmissionstatement          | 1                    |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
    # Student1 checks the assignment is visible in the timeline
    When I am on the "Homepage" page logged in as student1
    Then I should see "Test assignment name" in the "Timeline" "block"
    # Student2 checks the assignment is visible in the timeline
    And I am on the "Homepage" page logged in as student2
    And I should see "Test assignment name" in the "Timeline" "block"
    # Student2 submits the assignment
    And I am on the "Test assignment name" Activity page
    And I press "Add submission"
    And I set the field "Online text" to "Assignment submission text"
    And I press "Save changes"
    And I should see "Draft (not submitted)" in the "Submission status" "table_row"
    And I press "Submit assignment"
    And I should see "This submission is the work of my group, except where we have acknowledged the use of the works of other people."
    And I press "Continue"
    And I should see "Confirm submission"
    And I should see "You are required to agree to this statement before you can submit."
    And I set the field "submissionstatement" to "1"
    And I press "Continue"
    And I should see "Submitted for grading" in the "Submission status" "table_row"
    # Student2 checks the timeline again
    And I am on the "Homepage" page
    And I should not see "Test assignment name" in the "Timeline" "block"
    # Student1 checks the timeline again
    And I am on the "Homepage" page logged in as student1
    And I should not see "Test assignment name" in the "Timeline" "block"

  @javascript
  Scenario: Switch between group modes
    Given the following "activity" exists:
      | activity         | assign                      |
      | course           | C1                          |
      | name             | Test assignment name        |
      | submissiondrafts | 0                           |
      | teamsubmission   | 1                           |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I follow "View all submissions"
    Then I should see "Default group" in the "Student 0" "table_row"
    And I should see "Default group" in the "Student 1" "table_row"
    And I should see "Default group" in the "Student 2" "table_row"
    And I should see "Default group" in the "Student 3" "table_row"
    And I am on the "Test assignment name" "assign activity editing" page
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and return to course"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and display"
    And the following "group members" exist:
      | user     | group |
      | student0 | G1    |
      | student1 | G1    |
    And I am on the "Test assignment name" "assign activity" page
    And I follow "View all submissions"
    And I set the field "Separate groups" to "Group 1"
    And I should see "Group 1" in the "Student 0" "table_row"
    And I should see "Group 1" in the "Student 1" "table_row"
    And I should not see "Student 2"
    And I set the field "Separate groups" to "All participants"
    And I should see "Group 1" in the "Student 0" "table_row"
    And I should see "Group 1" in the "Student 1" "table_row"
    And I should see "Default group" in the "Student 2" "table_row"
    And I should see "Default group" in the "Student 3" "table_row"

  Scenario: Confirm that the grading status changes for each group member
    Given the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
    And the following "activity" exists:
      | activity                            | assign                      |
      | course                              | C1                          |
      | name                                | Test assignment name        |
      | submissiondrafts                    | 0                           |
      | assignsubmission_onlinetext_enabled | 1                           |
      | assignsubmission_file_enabled       | 0                           |
      | teamsubmission                      | 1                           |
      | preventsubmissionnotingroup         | 0                           |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I follow "View all submissions"
    Then "Student 1" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 2" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 3" row "Status" column of "generaltable" table should not contain "Submitted for grading"
    And "Student 4" row "Status" column of "generaltable" table should not contain "Submitted for grading"
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student3  | I'm the student's first submission  |
    And I am on the "Test assignment name" Activity page
    And I follow "View all submissions"
    And "Student 1" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 2" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 3" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 4" row "Status" column of "generaltable" table should contain "Submitted for grading"

  @javascript
  Scenario: Confirm that group submissions can be reopened
    Given the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
    And the following "activity" exists:
      | activity                            | assign                      |
      | course                              | C1                          |
      | name                                | Test assignment name        |
      | submissiondrafts                    | 0                           |
      | assignsubmission_onlinetext_enabled | 1                           |
      | assignsubmission_file_enabled       | 0                           |
      | teamsubmission                      | 1                           |
      | attemptreopenmethod                 | manual                      |
      | requireallteammemberssubmit         | 0                           |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50.0 |
      | Apply grades and feedback to entire group | 1 |
    And I press "Save changes"
    And I set the following fields to these values:
      | Allow another attempt | 1 |
    And I press "Save changes"
    When I am on the "Test assignment name" "assign activity" page
    And I follow "View all submissions"
    Then "Student 1" row "Status" column of "generaltable" table should contain "Reopened"
    And "Student 2" row "Status" column of "generaltable" table should contain "Reopened"

  Scenario: Confirm groups and submission counts are correct
    Given the following "groups" exist:
      | name    | course | idnumber |
      | Group 2 | C1     | G2       |
      | Group 3 | C1     | G3       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G2    |
      | student3 | G3    |
    And the following "groupings" exist:
      | name       | course | idnumber |
      | Grouping 1 | C1     | GG1      |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG1      | G2    |
      # Groupmode 1 = Separate Groups
    And the following "activity" exists:
      | activity                             | assign                       |
      | course                               | C1                           |
      | name                                 | Test assignment name         |
      | submissiondrafts                     | 0                            |
      | assignsubmission_onlinetext_enabled  | 1                            |
      | assignsubmission_file_enabled        | 0                            |
      | teamsubmission                       | 1                            |
      | attemptreopenmethod                  | manual                       |
      | requireallteammemberssubmit          | 0                            |
      | groupmode                            | 1                            |
      | teamsubmissiongroupingid             | GG1                          |
      | submissiondrafts                     | 0                            |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |
      | Test assignment name  | student2  | I'm the student's first submission  |
      | Test assignment name  | student3  | I'm the student's first submission  |
    And I am on the "Test assignment name" Activity page logged in as admin
    And I should see "3" in the "Groups" "table_row"
    And I should see "3" in the "Submitted" "table_row"
    When I select "Group 1" from the "Separate groups" singleselect
    Then I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"
    And I select "Group 2" from the "Separate groups" singleselect
    And I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"
    And I select "Group 3" from the "Separate groups" singleselect
    And I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"

  Scenario: Confirm that the submission status changes for each group member
    Given the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
    And the following "activity" exists:
      | activity                             | assign                       |
      | course                               | C1                           |
      | name                                 | Test assignment name         |
      | submissiondrafts                     | 1                            |
      | assignsubmission_onlinetext_enabled  | 1                            |
      | assignsubmission_file_enabled        | 0                            |
      | teamsubmission                       | 1                            |
      | attemptreopenmethod                  | manual                       |
      | requireallteammemberssubmit          | 0                            |
      # Groupmode 0 = No Groups
      | groupmode                            | 0                            |
      | preventsubmissionnotingroup          | 0                            |
      | submissiondrafts                     | 0                            |
      | teamsubmission                       | 1                            |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |
    And the following "blocks" exist:
      | blockname        | contextlevel | reference | pagetypepattern | defaultregion |
      | activity_modules | Course       | C1        | course-view-*   | side-pre      |
    And I am on the "C1" Course page logged in as student1
    And I click on "Assignments" "link" in the "Activities" "block"
    And I should see "Submitted for grading"
    And I am on the "C1" Course page logged in as student2
    And I click on "Assignments" "link" in the "Activities" "block"
    And I should see "Submitted for grading"
    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I follow "View all submissions"
    Then "Student 1" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 2" row "Status" column of "generaltable" table should contain "Submitted for grading"

  @javascript @_file_upload
  Scenario: Student can submit or edit group assignment depending on 'requireallteammemberssubmit' setting
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode   |
      | Course 2 | C2        | 0        | 2           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |
      | student2 | C2     | student        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C2     | CG1      |
    And the following "group members" exist:
      | user     | group |
      | student1 | CG1   |
      | student2 | CG1   |
    And the following "activities" exist:
      | activity | course | name     | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes | submissiondrafts | teamsubmission | requireallteammemberssubmit |
      | assign   | C2     | Assign 1 | 1                                   | 1                             | 1                              | 2097152                            | 1                | 1              | 1                           |
      | assign   | C2     | Assign 2 | 1                                   | 1                             | 1                              | 2097152                            | 0                | 1              | 0                           |
    # Submit an assignment with 'requireallteammemberssubmit' setting enabled
    When I am on the "Assign 1" "assign activity" page logged in as student1
    Then I should see "Group 1"
    And I should not see "Student 2"
    And I press "Add submission"
    And I set the field "Online text" to "student1 submission"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    # Confirm that Submission status remains as draft and all students appear because 'Submit assignment' was not yet clicked
    And I should see "Draft (not submitted)" in the "Submission status" "table_row"
    And I should see "Users who need to submit: Student 1, Student 2"
    And I press "Submit assignment"
    And I press "Continue"
    # Confirm that Submission status remains as draft and only student2 appears because student2 has not yet submitted assignment
    And I am on the "Assign 1" "assign activity" page logged in as student2
    And I should see "Draft (not submitted)" in the "Submission status" "table_row"
    And I should see "Users who need to submit: Student 2"
    And I press "Edit submission"
    And I set the field "Online text" to "student2 updated submission"
    And I delete "empty.txt" from "File submissions" filemanager
    And I upload "lib/tests/fixtures/tabfile.csv" file to "File submissions" filemanager
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    # Confirm that Submission status is now Submitted for grading and all changes made by student2 is reflected on assignment
    And I am on the "Assign 1" "assign activity" page logged in as student1
    And I should see "Submitted for grading" in the "Submission status" "table_row"
    And I should see "student2 updated submission" in the "Online text" "table_row"
    And I should see "tabfile.csv" in the "File submissions" "table_row"
    And I should not see "student1 submission" in the "Online text" "table_row"
    And I should not see "empty.txt" in the "File submissions" "table_row"
    # Submit an assignment with 'requireallteammemberssubmit' disabled
    And I am on the "Assign 2" "assign activity" page logged in as student1
    And I should see "Group 1"
    And I should not see "Student 2"
    And I press "Add submission"
    And I set the field "Online text" to "student1 submission"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    # Confirm that Submission status is immediately set to Submitted for grading for all students after student1 submits assignments
    And I am on the "Assign 2" "assign activity" page logged in as student2
    And I should see "Submitted for grading" in the "Submission status" "table_row"
    And I should not see "Users who need to submit"

  Scenario: Group submission does not use non-participation groups
    Given the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group A | C1     | CG1      | 0             |
    And the following "group members" exist:
      | group  | user     |
      | CG1    | student1 |
    And the following "activity" exists:
      | activity         | assign                      |
      | course           | C1                          |
      | name             | Test assignment name        |
      | submissiondrafts | 0                           |
      | teamsubmission   | 1                           |
      | groupmode        | 1                           |
    When I am on the "Test assignment name" Activity page logged in as student1
    Then I should see "Default group"
    And I should not see "Group A"
