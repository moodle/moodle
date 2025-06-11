@core @core_grades @javascript
Feature: Average grades are displayed in the gradebook
    In order to check the expected results are displayed
    As an admin
    I need to assign grades and check that they display correctly in the gradebook.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
    # Teacher 1 is user without preferences set, Teacher 2 is user with preferences set
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
      | student5 | Student   | 5        | student5@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
    And the following "grade item" exists:
      | course   | C1            |
      | itemname | Manual item 1 |
    And the following "grade grades" exist:
      | gradeitem     | user     | grade | hidden |
      | Manual item 1 | student1 | 10.00 | 0      |
      | Manual item 1 | student2 | 20.00 | 0      |
      | Manual item 1 | student3 | 30.00 | 0      |
      | Manual item 1 | student4 | 40.00 | 1      |
      | Manual item 1 | student5 | 50.00 | 0      |
    And the following "course enrolments" exist:
      | user     | course | role    | status    |
      | student2 | C1     | student | suspended |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I press "Delete" action in the "Student 5" report row
    And I click on "Delete" "button" in the "Delete user" "dialogue"
    # Enable averages
    And I am on the "Course 1" "grades > course grade settings" page
    And I set the following fields to these values:
      | Show average | Show |
    And I press "Save changes"

  Scenario: Grade averages display correctly in the gradebook according to various settings
    # Check the admin grade table
    Given I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    # Average is (10 + 30 + 40)/3 = 26.67 for manual and total since hidden items are included on grader report
    And the following should exist in the "user-grades" table:
      | -1-                | -2-       | -3-       |
      | Overall average    | 26.67     | 26.67     |
    And I am on the "Course 1" "grades > Grade summary > View" page
    # Average is (10 + 20 + 30)/3 = 30.00 for manual since
    # 1. Hidden items are NOT included on grader report.
    # 2. There is a bug when we have suspended users in the course so here Student 2 is included.
    #    So the average is not write when preference is either not set or set to 0.
    # Possibly this should be changed later to match grader report.
    And I should see "30.00" in the "Manual item 1" "table_row"

    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher2"
    And the following "user preferences" exist:
      | user     | preference                        | value |
      | teacher2 | grade_report_showonlyactiveenrol  | 1     |
    And I am on the "Course 1" "grades > Grader report > View" page
    And the following should exist in the "user-grades" table:
      | -1-                | -2-       | -3-       |
      | Overall average    | 26.67     | 26.67     |
    And I am on the "Course 1" "grades > Grade summary > View" page
    # Average is (10 + 30)/2 = 20.00 for manual (when preference is set to 1 set average is correct).
    And I should see "20.00" in the "Manual item 1" "table_row"
    And the following "user preferences" exist:
      | user     | preference                        | value |
      | teacher2 | grade_report_showonlyactiveenrol  | 0     |
    And I am on the "Course 1" "grades > Grader report > View" page
    And the following should exist in the "user-grades" table:
      | -1-                | -2-       | -3-       |
      | Overall average    | 25.00     | 25.00     |
    And I am on the "Course 1" "grades > Grade summary > View" page
    # Average is (10 + 30)/2 = 20.00 for manual (when preference is set to 0 set average is NOT correct).
    And I should see "20.00" in the "Manual item 1" "table_row"

    # Check the user grade table
    When I am on the "Course 1" "grades > user > View" page logged in as "student1"
    # Average of manual item is (10 + 30)/2 = 20.00 since hidden items are not included on user report.
    # But total is calculated and its settings allow using hidden grades so it will stay the same.
    Then the following should exist in the "user-grade" table:
      | Grade item              | Calculated weight | Grade  | Range | Percentage | Average | Contribution to course total |
      | Manual item 1           | 100.00 %          | 10.00  | 0–100 | 10.00 %    | 20.00   | 10.00 %                      |
      | Course total            | -                 | 10.00  | 0–100 | 10.00 %    | 26.67   | -                            |

    # Default grade_report_showonlyactiveenrol is 1 so change that
    And the following config values are set as admin:
      | grade_report_showonlyactiveenrol  | 0  |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher2"
    And the following should exist in the "user-grades" table:
      | -1-                | -2-       | -3-       |
      | Overall average    | 25.00     | 25.00     |
    And I am on the "Course 1" "grades > Grade summary > View" page
    And I should see "20.00" in the "Manual item 1" "table_row"
    And the following "user preferences" exist:
      | user     | preference                        | value |
      | teacher2 | grade_report_showonlyactiveenrol  | 1     |
    And I am on the "Course 1" "grades > Grader report > View" page
    And the following should exist in the "user-grades" table:
      | -1-                | -2-       | -3-       |
      | Overall average    | 26.67     | 26.67     |
    And I am on the "Course 1" "grades > Grade summary > View" page
    And I should see "20.00" in the "Manual item 1" "table_row"

    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And the following should exist in the "user-grades" table:
      | -1-                | -2-       | -3-       |
      | Overall average    | 25.00     | 25.00     |
    And I am on the "Course 1" "grades > Grade summary > View" page
    And I should see "20.00" in the "Manual item 1" "table_row"

    And I am on the "Course 1" "grades > user > View" page logged in as "student1"
    And the following should exist in the "user-grade" table:
      | Grade item              | Calculated weight | Grade  | Range | Percentage | Average | Contribution to course total |
      | Manual item 1           | 100.00 %          | 10.00  | 0–100 | 10.00 %    | 20.00   | 10.00 %                      |
      | Course total            | -                 | 10.00  | 0–100 | 10.00 %    | 26.67   | -                            |
