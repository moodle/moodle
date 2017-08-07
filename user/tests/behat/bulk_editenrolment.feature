@core @core_user
Feature: Bulk enrolments
  In order to manage a course site
  As a teacher
  I need to be able to bulk edit enrolments

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | teacher1 | C1 | editingteacher |

  @javascript
  Scenario: Bulk edit enrolments
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I press "Select all"
    And I set the field "With selected users..." to "Edit selected user enrolments"
    And I set the field "Alter status" to "Suspended"
    And I press "Save changes"
    Then I should see "Suspended" in the "Teacher 1" "table_row"
    Then I should see "Suspended" in the "Student 1" "table_row"
    And I should see "Suspended" in the "Student 2" "table_row"

  @javascript
  Scenario: Bulk delete enrolments
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I press "Select all"
    And I set the field "With selected users..." to "Delete selected user enrolments"
    And I press "Unenrol users"
    Then I should not see "Student 1"
    And I should not see "Student 2"
    And I should not see "Teacher 1"
