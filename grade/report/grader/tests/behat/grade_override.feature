@gradereport @gradereport_grader @gradereport_grade_override
Feature: As a teacher, I want to override a grade with a deduction and check the gradebook.
  The deducted mark should not affect the overridden grade.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "grade items" exist:
      | itemname        | grademin | grademax | course |
      | Manual grade 01 | 0       | 100       | C1     |
      | Manual grade 02 | 0       | 100       | C1     |
    When the following "grade grades" exist:
      | gradeitem       | user     | grade | deductedmark |
      | Manual grade 01 | student1 | 60    | 10           |
      | Manual grade 02 | student1 | 80    | 20           |

  @javascript
  Scenario: Override a grade with a deduction and check the gradebook
    Given I am on the "Course 1" course page logged in as "teacher1"
    And I navigate to "View > Grader report" in the course gradebook
    And the following should exist in the "user-grades" table:
      | -1-                | -2-                  | -3-       | -4-       | -5-       |
      | Student 1          | student1@example.com | 60        | 80        | 140       |
    And I turn editing mode on
    And I set the following fields to these values:
        | Student 1 Manual grade 01 grade             | 80 |
    And I click on "Save changes" "button"
    When I turn editing mode off
    Then the following should exist in the "user-grades" table:
      | -1-                | -2-                  | -3-       | -4-      | -5-       |
      | Student 1          | student1@example.com | 80        | 80       | 160       |
    When I turn editing mode on
    And I set the following fields to these values:
      | Student 1 Manual grade 02 grade             | 100 |
    And I click on "Save changes" "button"
    And I turn editing mode off
    Then the following should exist in the "user-grades" table:
      | -1-                | -2-                  | -3-       | -4-       | -5-       |
      | Student 1          | student1@example.com | 80        | 100       | 180       |
