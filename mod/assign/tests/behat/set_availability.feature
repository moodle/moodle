@mod @mod_assign @javascript
Feature: Set availability dates for an assignment
  In order to control when a student can upload an assignment
  As a teacher
  I need be able to set availability dates for an assignment

  Background:
    Given I log in as "admin"
    And I set the following administration settings values:
      | Enable timed assignments | 1 |
    And the following "courses" exist:
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
    And the following "activity" exists:
      | activity                           | assign                 |
      | course                             | C1                     |
      | name                               | Assignment name        |
      | description                        | Assignment description |
      | assignsubmission_file_enabled      | 1                      |
      | assignsubmission_file_maxfiles     | 1                      |
      | assignsubmission_file_maxsizebytes | 0                      |
      | submissiondrafts                   | 0                      |
    Given the following "activity" exists:
      | activity                            | assign                               |
      | course                              | C1                                   |
      | name                                | Test late assignment with time limit |
      | assignsubmission_onlinetext_enabled | 1                                    |
      | assignsubmission_file_enabled       | 1                                    |
      | assignsubmission_file_maxfiles      | 1                                    |
      | assignsubmission_file_maxsizebytes  | 1000000                              |
      | submissiondrafts                    | 0                                    |
      | allowsubmissionsfromdate_enabled    | 0                                    |
      | duedate_enabled                     | 0                                    |
      | cutoffdate_enabled                  | 0                                    |
      | gradingduedate_enabled              | 0                                    |

  Scenario: Student cannot submit an assignment prior to the 'allow submissions from' date
    Given I am on the "Assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    # Set 'Allow submissions from' to tomorrow at noon.
    And I set the field "Allow submissions from" to "##tomorrow noon##"
    And I press "Save and return to course"
    And I log out

    When I am on the "Assignment name" Activity page logged in as student1
    Then "Add submission" "button" should not exist
    And the activity date in "Assignment name" should contain "Opens:"
    And the activity date in "Assignment name" should contain "##tomorrow noon##%A, %d %B %Y, %I:%M##"

  Scenario: Student can see the assignment's due date in the course calendar
    Given I am on the "Assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    # Set 'Allow submissions from' to the first day of this month at noon.
    And I set the field "Allow submissions from" to "##first day of this month noon##"
    # Set 'Due date' to the second day of this month at noon.
    And I set the field "Due date" to "##first day of this month noon +24 hours##"
    And I press "Save and return to course"
    And I turn editing mode on
    And I add the "Calendar" block
    And I log out

    And I am on the "C1" Course page logged in as student1
    When I hover over day "2" of this month in the mini-calendar block
    Then I should see "Assignment name is due"

  @_file_upload
  Scenario: Student can submit an assignment before the due date
    Given I am on the "Assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    # Set 'Allow submissions from' to now.
    And I set the field "Allow submissions from" to "##now##"
    # Set 'Due date' to 2 days 5 hours 30 minutes in the future.
    And I set the field "Due date" to "##+2 days 5 hours 30 minutes##"
    And I press "Save and return to course"
    And I log out

    When I am on the "Assignment name" Activity page logged in as student1
    And the activity date in "Assignment name" should contain "Due:"
    And the activity date in "Assignment name" should contain "##+2 days 5 hours 30 minutes##%A, %d %B %Y##"
    And I should see "2 days 5 hours" in the "Time remaining" "table_row"
    And "Add submission" "button" should exist
    And I press "Add submission"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    When I press "Save changes"
    Then I should see "Submitted for grading" in the "Submission status" "table_row"
    And I log out

    And I am on the "Assignment name" Activity page logged in as teacher1
    And I should see "1" in the "Submitted" "table_row"
    And I follow "View all submissions"
    And I should see "Submitted for grading" in the "Student 1" "table_row"

  @_file_upload
  Scenario: Student can submit an assignment after the due date and the submission is marked as late
    Given I am on the "Assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    # Set 'Allow submissions from' to 3 days ago.
    And I set the field "Allow submissions from" to "##3 days ago##"
    # Set 'Due date' to 2 days 5 hours 30 minutes ago.
    And I set the field "Due date" to "##2 days 5 hours 30 minutes ago##"
    # Set 'Cut-off date' to tomorrow at noon.
    And I set the field "Cut-off date" to "##tomorrow noon##"
    And I press "Save and return to course"
    And I log out

    And I am on the "Assignment name" Activity page logged in as student1
    And the activity date in "Assignment name" should contain "Due:"
    And the activity date in "Assignment name" should contain "##2 days 5 hours 30 minutes ago##%A, %d %B %Y##"
    And I should see "Assignment is overdue by: 2 days 5 hours" in the "Time remaining" "table_row"
    And "Add submission" "button" should exist
    And I press "Add submission"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    When I press "Save changes"
    Then I should see "Submitted for grading" in the "Submission status" "table_row"
    And I should see "Assignment was submitted 2 days 5 hours late" in the "Time remaining" "table_row"
    And I log out

    And I am on the "Assignment name" Activity page logged in as teacher1
    And I should see "1" in the "Submitted" "table_row"
    And I follow "View all submissions"
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "2 days 5 hours late" in the "Student 1" "table_row"

  @_file_upload
  Scenario: Student can submit an assignment before the time limit runs out
    Given I log in as "admin"
    And I change the window size to "large"
    And I set the following administration settings values:
      | Enable timed assignments | 1  |
    And I log out
    And I am on the "Assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    # Set 'Time limit' to 20 seconds.
    And I set the field "timelimit[enabled]" to "1"
    And I set the field "timelimit[number]" to "20"
    And I set the field "timelimit[timeunit]" to "seconds"
    And I press "Save and return to course"
    And I log out

    When I am on the "Assignment name" Activity page logged in as student1
    And I should see "20 secs" in the "Time limit" "table_row"
    And "Begin assignment" "link" should exist
    And I follow "Begin assignment"
    And I wait "1" seconds
    And "Begin assignment" "button" should exist
    And I press "Begin assignment"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    When I press "Save changes"
    Then I should see "Submitted for grading" in the "Submission status" "table_row"
    And I should see "secs under the time limit" in the "Time remaining" "table_row"

  @_file_upload
  Scenario: Assignment with time limit and due date shows how late assignment is submitted relative to due date
    Given I log in as "admin"
    And I change the window size to "large"
    And I set the following administration settings values:
      | Enable timed assignments | 1  |
    And I log out
    And I am on the "Assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    # Set 'Time limit' to 5 seconds.
    And I set the field "timelimit[enabled]" to "1"
    And I set the field "timelimit[number]" to "5"
    And I set the field "timelimit[timeunit]" to "seconds"
    # Set 'Due date' to 2 days 5 hours 30 minutes ago.
    And I set the field "Due date" to "##2 days 5 hours 30 minutes ago##"
    And I press "Save and display"
    And I should see "5 secs" in the "Time limit" "table_row"
    And I log out

    When I am on the "Assignment name" Activity page logged in as student1
    And "Begin assignment" "link" should exist
    And I follow "Begin assignment"
    And I wait "1" seconds
    And "Begin assignment" "button" should exist
    And I press "Begin assignment"
    And I wait "5" seconds
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    When I press "Save changes"
    Then I should see "Assignment was submitted 2 days 5 hours late" in the "Time remaining" "table_row"

  Scenario: Student cannot submit an assignment after the cut-off date
    Given I am on the "Assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    # Set 'Allow submissions from' to 3 days ago.
    And I set the field "Allow submissions from" to "##3 days ago##"
    # Set 'Due date' to 2 days 5 hours 30 minutes ago.
    And I set the field "Due date" to "##2 days 5 hours 30 minutes ago##"
    # Set 'Cut-off date' to yesterday at noon.
    And I set the field "Cut-off date" to "##yesterday noon##"
    And I press "Save and return to course"
    And I log out

    When I am on the "Assignment name" Activity page logged in as student1
    Then "Add submission" "button" should not exist
    And I log out

    And I am on the "Assignment name" Activity page logged in as teacher1
    And I should see "0" in the "Submitted" "table_row"
    And I follow "View all submissions"
    And I should see "No submission" in the "Student 1" "table_row"
    And I should see "Assignment is overdue by: 2 days 5 hours" in the "Student 1" "table_row"

  @_file_upload
  Scenario: Late submission will be calculated only when the student starts the assignment
    Given I am on the "Test late assignment with time limit" Activity page logged in as admin
    And I navigate to "Settings" in current page administration
    And I follow "Expand all"
    # Set 'Time limit' to 5 seconds.
    And I set the field "timelimit[enabled]" to "1"
    And I set the field "timelimit[number]" to "5"
    And I set the field "timelimit[timeunit]" to "seconds"
    And I press "Save and return to course"
    When I am on the "Test late assignment with time limit" Activity page logged in as student1
    And I wait "6" seconds
    And I click on "Begin assignment" "link"
    And I click on "Begin assignment" "button"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    Then I should see "Submitted for grading" in the "Submission status" "table_row"
    And I should see "under the time limit" in the "Time remaining" "table_row"
