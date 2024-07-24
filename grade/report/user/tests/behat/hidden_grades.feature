@gradereport @gradereport_user
Feature: We don't show hidden grades for users without the 'moodle/grade:viewhidden' capability on user report
  In order to show user report in secure way
  As a teacher without the 'moodle/grade:viewhidden' capability
  I should not see hidden grades in the user report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student1   | 1        | student1@example.com |
      | student2 | Student2   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | name                   | intro                   | assignsubmission_onlinetext_enabled | submissiondrafts |
      | assign   | C1     | 1       | Test assignment name 1 | Submit your online text | 1                                   | 0                |
      | assign   | C1     | 1       | Test assignment name 2 | submit your online text | 1                                   | 0                |
      | assign   | C1     | 1       | Test assignment name 3 | submit your online text | 1                                   | 0                |
    # Hidden manual grade item.
    And the following "grade items" exist:
      | itemname     | grademin | grademax | course | hidden |
      | Manual grade | 20       | 40       | C1     | 1      |
    And the following "grade grades" exist:
      | gradeitem              | user     | grade |
      | Test assignment name 1 | student1 | 80    |
      | Test assignment name 1 | student2 | 70    |
      | Test assignment name 2 | student1 | 90    |
      | Test assignment name 2 | student2 | 60    |
      | Test assignment name 3 | student1 | 10    |
      | Test assignment name 3 | student2 | 50    |
      | Manual grade           | student1 | 30    |
      | Manual grade           | student2 | 40    |
    And I log in as "admin"
    # Need to show hidden items in order to check hidden grades. Also lets show totals if they contain hidden items.
    And the following config values are set as admin:
      | grade_report_user_showhiddenitems           | 2 |
      | grade_report_user_showtotalsifcontainhidden | 2 |
    And I am on "Course 1" course homepage with editing mode on
    # Hide assignment 2 activity.
    And I open "Test assignment name 2" actions menu
    And I choose "Hide" in the open action menu
    And I navigate to "View > Grader report" in the course gradebook
    # Hide grade.
    And I click on grade menu "Test assignment name 1" for user "student1"
    And I choose "Hide" in the open action menu
    # Hide assignment 3 grade item.
    And I set the following settings for grade item "Test assignment name 3" of type "gradeitem" on "grader" page:
      | Hidden          | 1 |

  @javascript
  Scenario: View user report containing hidden activities or grade items or grades with capabilities with student view
    Given I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    When I set the field "Search users" to "Student1"
    And I click on "Student1 1" "list_item"
    And I wait until the page is ready

    # Student View.
    And the following should exist in the "user-grade" table:
      | Grade item                 | Grade    |
      | Test assignment name 1     | -        |
      | Test assignment name 3     | -        |
      | Manual grade               | -        |
      | Course total               | 210      |
    And the following should not exist in the "user-grade" table:
      | Grade item                 |
      | Test assignment name 2     |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And I set the field "Search users" to "Student2"
    And I click on "Student2 2" "list_item"
    And I wait until the page is ready
    And the following should exist in the "user-grade" table:
      | Grade item                 | Grade    |
      | Test assignment name 1     | 70       |
      | Test assignment name 3     | -        |
      | Manual grade               | -        |
      | Course total               | 220      |
    And the following should not exist in the "user-grade" table:
      | Grade item                 |
      | Test assignment name 2     |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    Then "Hidden" "icon" should not exist in the "Course total" "table_row"

  @javascript
  Scenario: View user report containing hidden activities or grade items or grades with capabilities with teacher view
    Given I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    When I set the field "Search users" to "Student2"
    And I click on "Student2 2" "list_item"
    And I wait until the page is ready
    And I set the field "View report as" to "Myself"
    And the following should exist in the "user-grade" table:
      | Grade item                 | Grade  |
      | Test assignment name 1     | 70     |
      | Test assignment name 2     | 60     |
      | Test assignment name 3     | 50     |
      | Manual grade               | 40     |
      | Course total               | 220    |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And I set the field "Search users" to "Student1"
    And I click on "Student1 1" "list_item"
    And I wait until the page is ready
    And the following should exist in the "user-grade" table:
      | Grade item                 | Grade  |
      | Test assignment name 1     | 80     |
      | Test assignment name 2     | 90     |
      | Test assignment name 3     | 10     |
      | Manual grade               | 30     |
      | Course total               | 210    |
    And "Hidden" "icon" should exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should exist in the "Manual grade" "table_row"
    Then "Hidden" "icon" should not exist in the "Course total" "table_row"

  @javascript
  Scenario: View user report containing hidden activities or grade items or grades without capabilities with teacher view
    Given I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    And the following "role capability" exists:
      | role                    | editingteacher  |
      | moodle/grade:viewhidden | prohibit        |
    When I set the field "Search users" to "Student1"
    And I click on "Student1 1" "list_item"
    And I wait until the page is ready
    And I set the field "View report as" to "Myself"
    And the following should exist in the "user-grade" table:
      | Grade item                 | Grade  |
      | Test assignment name 1     | -      |
      | Test assignment name 2     | -      |
      | Test assignment name 3     | -      |
      | Manual grade               | -      |
      | Course total               | 210    |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And I set the field "Search users" to "Student2"
    And I click on "Student2 2" "list_item"
    And I wait until the page is ready
    And the following should exist in the "user-grade" table:
      | Grade item                 | Grade  |
      | Test assignment name 1     | 70     |
      | Test assignment name 2     | -      |
      | Test assignment name 3     | -      |
      | Manual grade               | -      |
      | Course total               | 220    |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 2" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    Then "Hidden" "icon" should not exist in the "Course total" "table_row"

  @javascript
  Scenario: View user report containing hidden activities or grade items or grades without capabilities with student view
    Given I am on the "Course 1" "grades > User report > View" page logged in as "teacher1"
    And the following "role capability" exists:
      | role                    | editingteacher  |
      | moodle/grade:viewhidden | prohibit        |
    When I set the field "Search users" to "Student2"
    And I click on "Student2 2" "list_item"
    And I wait until the page is ready
    And the following should exist in the "user-grade" table:
      | Grade item                 | Grade    |
      | Test assignment name 1     | 70       |
      | Test assignment name 3     | -        |
      | Manual grade               | -        |
      | Course total               | 220      |
    And the following should not exist in the "user-grade" table:
      | Grade item                 |
      | Test assignment name 2     |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    And "Hidden" "icon" should not exist in the "Course total" "table_row"

    And I set the field "Search users" to "Student1"
    And I click on "Student1 1" "list_item"
    And I wait until the page is ready
    And the following should exist in the "user-grade" table:
      | Grade item                 | Grade    |
      | Test assignment name 1     | -        |
      | Test assignment name 3     | -        |
      | Manual grade               | -        |
      | Course total               | 210      |
    And the following should not exist in the "user-grade" table:
      | Grade item                 |
      | Test assignment name 2     |
    And "Hidden" "icon" should not exist in the "Test assignment name 1" "table_row"
    And "Hidden" "icon" should not exist in the "Test assignment name 3" "table_row"
    And "Hidden" "icon" should not exist in the "Manual grade" "table_row"
    Then "Hidden" "icon" should not exist in the "Course total" "table_row"
