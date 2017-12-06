@mod @block_attendance @javascript
Feature: Test that teachers can add the attendance block and students can view reports.
    Background:
        Given the following "courses" exist:
            | fullname | shortname |
            | Course 1 | C1        |
        And the following "users" exist:
            | username | firstname | lastname | email                |
            | teacher1 | Teacher   | 1        | teacher1@example.com |
            | student1 | Student   | 1        | student1@example.com |
        And the following "course enrolments" exist:
            | course | user     | role           |
            | C1     | teacher1 | editingteacher |
            | C1     | student1 | student        |
        And the following "activities" exist:
            | activity   | name                        | intro                              | course               | idnumber    |
            | attendance | AttendanceTest1             | attendance description             | C1                   | attendance1 |

    Scenario: Teachers can add the attendance block
        When I log in as "teacher1"
        And I follow "Course 1"
        And I turn editing mode on
        And I add the "Attendance" block
        Then I should see "Take attendance"

    Scenario: Students can view their reports.
        When I log in as "teacher1"
        And I follow "Course 1"
        And I turn editing mode on
        And I add the "Attendance" block
        And I log out
        And I log in as "student1"
        And I follow "Course 1"
        Then I should see "Sessions completed"
