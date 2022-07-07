@core @core_user
Feature: View course participants groups
  In order to know who is on a course
  As a teacher
  I need to be able to view the participants groups on a course

  Background:
    Given the following "users" exist:
      | username   | firstname | lastname | email                  |
      | teacher1x  | Teacher   | 1x       | teacher1x@example.com  |
      | student1x  | Student   | 1x       | student1x@example.com  |
      | student2x  | Student   | 2x       | student2x@example.com  |
      | student3x  | Student   | 3x       | student3x@example.com  |
      | student4x  | Student   | 4x       | student4x@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format | groupmode |
      | Course 1 | C1        | topics | 1         |
    And the following "course enrolments" exist:
      | user      | course | role            |
      | teacher1x  | C1     | editingteacher |
      | student1x  | C1     | student        |
      | student2x  | C1     | student        |
      | student3x  | C1     | student        |
      | student4x  | C1     | student        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group A | C1     | G1       |
      | Group B | C1     | G2       |
    And the following "group members" exist:
      | user      | group |
      | student1x | G1    |
      | student2x | G1    |
      | student3x | G2    |
      | student4x | G2    |

  Scenario: User should not be able to see other groups in separated group mode
    Given I log in as "student1x"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    Then I should see "Group A"
    And I should see "Student 1x"
    And I should see "Student 2x"
    And I should not see "Group B"
    And I should not see "Student 3x"
    And I should not see "Student 4x"

  @javascript
  Scenario: User should be able to see other groups in visible group mode
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Group mode" to "Visible groups"
    And I press "Save and display"
    And I log out
    And I log in as "student1x"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    Then I should see "Group A"
    And I should see "Student 1x"
    And I should see "Student 2x"
    And I set the field "type" in the "Filter 1" "fieldset" to "Groups"
    And I set the field "Type or select..." in the "Filter 1" "fieldset" to "Group B"
    And I click on "Apply filters" "button"
    And I should see "Student 3x"
    And I should see "Student 4x"

  Scenario: User should be able to see all users in no groups mode
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Group mode" to "No groups"
    And I press "Save and display"
    And I log out
    And I log in as "student1x"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    Then I should see "Group A"
    And I should see "Student 1x"
    And I should see "Student 2x"
    And I should see "Group B"
    And I should see "Student 3x"
    And I should see "Student 4x"
    And I should see "Teacher 1x"
    And I should see "No groups"
