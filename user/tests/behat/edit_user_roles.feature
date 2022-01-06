@core @core_user
Feature: Edit user roles
  In order to administer users in course
  As a teacher
  I need to be able to assign and unassign roles in the course

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher1  | Teacher   | 1        | teacher1@example.com  |
      | student1  | Student   | 1        | student1@example.com  |
      | student2  | Student   | 2        | student2@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | student1  | C1     | student        |
      | student2  | C1     | student        |

  @javascript
  Scenario: Assign roles on participants page
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 1's role assignments" "link"
    And I type "Non-editing teacher"
    And I press the enter key
    When I click on "Save changes" "link"
    Then I should see "Student, Non-editing teacher" in the "Student 1" "table_row"

  @javascript
  Scenario: Remove roles on participants page
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Student 1's role assignments" "link"
    And I click on "Student" "autocomplete_selection"
    When I click on "Save changes" "link"
    Then I should see "No roles" in the "Student 1" "table_row"
