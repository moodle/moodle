@gradereport @gradereport_grader
Feature: We can sort grades/user fields on the grader report
  In order to manage grades on grader report
  As a teacher
  I need to be able to sort grades or user fields.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname  | lastname | email                |
      | teacher1 | Teacher    | 1        | teacher1@example.com |
      | student1 | StudentA   | 2        | d@example.com |
      | student2 | StudentB   | 4        | a@example.com |
      | student3 | StudentC   | 3        | c@example.com |
      | student4 | StudentD   | 1        | b@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | name                   | intro                   | assignsubmission_onlinetext_enabled | submissiondrafts |
      | assign   | C1     | 1       | Test assignment name 1 | Submit your online text | 1                                   | 0                |
    And the following "mod_assign > submissions" exist:
      | assign                 | user     | onlinetext                            |
      | Test assignment name 1 | student1 | This is a submission for assignment 1 |
      | Test assignment name 1 | student2 | This is a submission for assignment 1 |
      | Test assignment name 1 | student3 | This is a submission for assignment 1 |
      | Test assignment name 1 | student4 | This is a submission for assignment 1 |
    And the following "grade items" exist:
      | itemname     | grademin | grademax | course |
      | Manual grade | 20       | 40       | C1     |
    And the following "grade grades" exist:
      | gradeitem              | user     | grade |
      | Test assignment name 1 | student1 | 80    |
      | Test assignment name 1 | student2 | 40    |
      | Test assignment name 1 | student3 | 60    |
    And I log in as "teacher1"

  @javascript
  Scenario: Sort grades or user fields on grader report by using cell action menu
    When I am on "Course 1" course homepage with editing mode on
    And I navigate to "View > Grader report" in the course gradebook
    # Default sorting is lastname ascending.
    And "StudentD 1" "table_row" should appear before "StudentA 2" "table_row"
    And "StudentA 2" "table_row" should appear before "StudentC 3" "table_row"
    And "StudentC 3" "table_row" should appear before "StudentB 4" "table_row"
    # Sort by grades in descending order.
    And I click on grade item menu "Test assignment name 1" of type "gradeitem" on "grader" page
    And I choose "Descending" in the open action menu
    And I wait until the page is ready
    Then "StudentA 2" "table_row" should appear before "StudentC 3" "table_row"
    And "StudentC 3" "table_row" should appear before "StudentB 4" "table_row"
    And "StudentB 4" "table_row" should appear before "StudentD 1" "table_row"
    # Sort by grades in ascending order.
    And I click on grade item menu "Test assignment name 1" of type "gradeitem" on "grader" page
    And I choose "Ascending" in the open action menu
    And I wait until the page is ready
    Then "StudentD 1" "table_row" should appear before "StudentB 4" "table_row"
    And "StudentB 4" "table_row" should appear before "StudentC 3" "table_row"
    And "StudentC 3" "table_row" should appear before "StudentA 2" "table_row"
    # Sort by email in ascending order.
    And I click on user profile field menu "email"
    And I choose "Ascending" in the open action menu
    And I wait until the page is ready
    Then "StudentB 4" "table_row" should appear before "StudentD 1" "table_row"
    And "StudentD 1" "table_row" should appear before "StudentC 3" "table_row"
    And "StudentC 3" "table_row" should appear before "StudentA 2" "table_row"
    And I click on user profile field menu "email"
    # Sort by email in descending order.
    And I choose "Descending" in the open action menu
    And I wait until the page is ready
    Then "StudentA 2" "table_row" should appear before "StudentC 3" "table_row"
    And "StudentC 3" "table_row" should appear before "StudentD 1" "table_row"
    And "StudentD 1" "table_row" should appear before "StudentB 4" "table_row"
