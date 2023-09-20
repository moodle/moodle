Feature: Locking Grade Items and Categories in Gradebook
  In order to ensure that grade items and categories can be securely locked in the gradebook,
  As a teacher,
  I need to perform locking actions and verify the locking status.

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username  | firstname | lastname  |
      | teacher1  | Teacher   | 1         |
      | student1  | Student   | 1         |
      | student2  | Student   | 2         |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | student1  | C1     | student        |
      | student2  | C1     | student        |
    And the following "grade categories" exist:
      | fullname   | course |
      | Category 1 | C1     |
    And the following "grade items" exist:
      | itemname       | course | gradecategory |
      | Manual grade 1 | C1     | Category 1    |
      | Manual grade 2 | C1     | Category 1    |
    And the following "grade items" exist:
      | itemname       | course |
      | Manual grade 3 | C1     |

  @javascript
  Scenario: Locking and unlocking a grade item preserves individual student locks
    Given I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    And I change window size to "large"
    When I click on "Manual grade 1" "core_grades > grade_actions" in the "Student 1" "table_row"
    And I choose "Lock" in the open action menu
    And I click on grade item menu "Manual grade 1" of type "gradeitem" on "grader" page
    And I choose "Lock" in the open action menu
    And I click on grade item menu "Manual grade 1" of type "gradeitem" on "grader" page
    And I choose "Unlock" in the open action menu
    Then "Locked" "icon" should exist in the "Student 1" "table_row"
    And "Locked" "icon" should not exist in the "Student 2" "table_row"

  @javascript
  Scenario: Locking and unlocking a grade item through editing form preserves individual student locks
    Given I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    And I change window size to "large"
    When I click on "Manual grade 1" "core_grades > grade_actions" in the "Student 1" "table_row"
    And I choose "Edit grade" in the open action menu
    And I set the field "Locked" to "1"
    And I press "Save changes"
    And I click on grade item menu "Manual grade 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I set the field "Locked" to "1"
    And I press "Save changes"
    And I click on grade item menu "Manual grade 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I set the field "Locked" to "0"
    And I press "Save changes"
    Then "Locked" "icon" should exist in the "Student 1" "table_row"
    And "Locked" "icon" should not exist in the "Student 2" "table_row"
