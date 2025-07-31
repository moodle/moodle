@core @core_grades @javascript
Feature: Asynchronous regrade on a large course

  Background:
    Given the following "courses" exist:
      | shortname | fullname      | idnumber |
      | C1        | Test course 1 | C1       |
      | C2        | Test course 2 | C2       |
    And the following "users" exist:
      | username  |
      | teacher1  |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | teacher1  | C2     | editingteacher |
    And "100" "users" exist with the following data:
      | username  | student[count]             |
      | firstname | Student                    |
      | lastname  | [count]                    |
      | email     | student[count]@example.com |
    And "100" "course enrolments" exist with the following data:
      | user   | student[count] |
      | course | C1             |
      | role   | student        |
    And the following "activity" exists:
      | activity | assign                  |
      | course   | C1                      |
      | idnumber | a1                      |
      | name     | Test assignment 1       |
      | grade    | 100                     |
      | intro    | Submit your online text |
    And "100" "grade grades" exist with the following data:
      | gradeitem | Test assignment 1 |
      | user      | student[count]    |
      | grade     | 80.00             |
    And the following "course enrolment" exists:
      | user   | student1 |
      | course | C2       |
      | role   | student  |
    And the following "activity" exists:
      | activity | assign                  |
      | course   | C2                      |
      | idnumber | a2                      |
      | name     | Test assignment 2       |
      | grade    | 100                     |
      | intro    | Submit your online text |
    And the following "grade grade" exists:
      | gradeitem | Test assignment 2 |
      | user      | student1          |
      | grade     | 80.00             |
    And I am on the "Test assignment 1" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Rescale existing grades" to "Yes"
    And I set the field "Maximum grade" to "50"
    And I press "Save and return to course"
    And I log out
    And I change the viewport size to "medium"

  Scenario Outline: Task indicator displays on all grade reports when a calculation is pending
    Given I am on the "Test course 2" "<report>" page logged in as "<user>"
    Then I should not see "The report will update automatically. You don't need to do anything."
    And <element> should exist
    When I am on the "Test course 1" "<report>" page logged in as "<user>"
    Then I should see "The report will update automatically. You don't need to do anything."
    And <element> should not exist

    Examples:
      | report                          | element                                             | user     |
      | grades > Grader report > View   | "user-grades" "table"                               | teacher1 |
      | grades > Overview report > View | "overview-grade" "table"                            | teacher1 |
      | grades > Single view > View     | "Search for a user to view all their grades" "text" | teacher1 |
      | grades > Grade summary > View   | "Summary" "table"                                   | teacher1 |
      | grades > User report > View     | "Search for a user to view their report" "text"     | teacher1 |
      | grades > User report > View     | "table.user-grade" "css_element"                    | student1 |

  Scenario Outline: Gradebook settings can be accessed when a regrade is pending
    Given I am on the "Test course 2" "<page>" page logged in as "teacher1"
    Then I should see "<text>"
    And I should not see "The report will update automatically. You don't need to do anything."
    Given I am on the "Test course 1" "<page>" page logged in as "teacher1"
    Then I should see "<text>"
    And I should not see "The report will update automatically. You don't need to do anything."

    Examples:
      | page                           | text             |
      | grades > Gradebook setup       | Aggregation      |
      | grades > Course grade settings | General settings |

  Scenario: Task indicator displays on user profile grade reports when a grade calculation is pending
    Given I log in as "student1"
    When I follow "Grades" in the user menu
    And I follow "Test course 2"
    Then "table.user-grade" "css_element" should exist
    Then I should not see "The report will update automatically. You don't need to do anything."
    When I follow "Grades" in the user menu
    And I follow "Test course 1"
    Then "table.user-grade" "css_element" should not exist
    Then I should see "The report will update automatically. You don't need to do anything."

  Scenario: Task indicator progresses and redirects when the task is run.
    When I am on the "Test course 1" "grades > Grader report > View" page logged in as teacher1
    And I should see "The report will update automatically. You don't need to do anything."
    And I should not see "0.0%"
    And "user-grades" "table" should not exist
    And I run all adhoc tasks
    # Progress bar should update.
    And I wait until "Recalculating grades" "text" exists
    And I should see "100%"
    # The page should reload after a short delay.
    Then I wait until "Recalculating grades" "text" does not exist
    And I set the field "Search users" to "Student 1"
    And "user-grades" "table" should exist
    And "40.00" "text" should exist in the "student1@example.com" "table_row"

  Scenario: Making changes on course with less than 100 grades performs the regrade synchronously, no indicator is shown.
    Given I am on the "Test assignment 2" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Rescale existing grades" to "Yes"
    And I set the field "Maximum grade" to "50"
    And I press "Save and return to course"
    When I am on the "Test course 2" "grades > Grader report > View" page
    Then I should not see "The report will update automatically. You don't need to do anything."
    And "user-grades" "table" should exist

  Scenario: Editing weights triggers a regrade, but further edits are possible
    Given I run all adhoc tasks
    And I am on the "Test course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I should not see "The report will update automatically. You don't need to do anything."
    And I am on the "Test course 1" "grades > Gradebook setup" page
    When I set the field "Override weight of Test assignment 1" to "1"
    And I press "Save changes"
    And I am on the "Test course 1" "grades > Grader report > View" page
    And I should see "The report will update automatically. You don't need to do anything."
    And I am on the "Test course 1" "grades > Gradebook setup" page
    And I should not see "The report will update automatically. You don't need to do anything."
