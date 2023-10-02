@core @core_grades @gradereport_user @javascript
Feature: We can use the user report
  As a user
  I browse to the User report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |

  Scenario: Verify we can view a user grade report with no users enrolled.
    When I am on the "Course 1" "grades > User report > View" page logged in as "admin"
    Then I should see "There are no students enrolled in this course."

  Scenario: Teacher sees his last viewed user report when navigating back to the gradebook user report.
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    And I should see "Search for a user to view their report" in the "region-main" "region"
    And I click on "Student 1" in the "user" search widget
    And I should see "Student 1" in the "region-main" "region"
    When I am on the "Course 1" "grades > User report > View" page
    Then I should not see "Search for a user to view their report" in the "region-main" "region"
    And I should see "Student 1" in the "region-main" "region"
    And I am on the "Course 1" "grades > User report > View" page logged in as "teacher2"
    And I should see "Search for a user to view their report" in the "region-main" "region"

  Scenario: Teacher sees his last viewed user report if the user is a part of the the current group.
    Given the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group 1 | C1     | G1       | 1             |
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
    And the following "group members" exist:
      | user     | group |
      | student2 | G1    |
    And I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    And I click on "Student 2" in the "user" search widget
    And I navigate to "View > Grader report" in the course gradebook
    And I click on "Group 1" in the "group" search widget
    When I navigate to "View > User report" in the course gradebook
    Then I should see "Student 2" in the "region-main" "region"
    And I should not see "Search for a user to view their report" in the "region-main" "region"

  Scenario: Teacher does not see the last viewed user if the user is not a part of the the current group.
    Given the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group 1 | C1     | G1       | 1             |
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
    And the following "group members" exist:
      | user     | group |
      | student2 | G1    |
    And I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    And I click on "Student 1" in the "user" search widget
    And I navigate to "View > Grader report" in the course gradebook
    And I click on "Group 1" in the "group" search widget
    When I navigate to "View > User report" in the course gradebook
    Then I should see "Search for a user to view their report" in the "region-main" "region"
    And I should not see "Student 1" in the "region-main" "region"

  Scenario: Teacher does not see his last viewed user report if the user is no longer enrolled in the course.
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    And I click on "Student 1" in the "user" search widget
    And I should see "Student 1" in the "region-main" "region"
    And I navigate to course participants
    And I click on "Unenrol" "icon" in the "Student 1" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    And I am on "Course 1" course homepage
    When I navigate to "View > User report" in the course gradebook
    Then I should see "Search for a user to view their report" in the "region-main" "region"
    And I should not see "Student 1" in the "region-main" "region"
