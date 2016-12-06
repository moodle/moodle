@mod @mod_assign
Feature: Check that the assignment grade can be updated correctly
  In order to ensure that the grade is shown correctly in the grading table
  As a teacher
  I need to grade a student and ensure the grade is shown correctly

  @javascript
  Scenario: Update the grade for an assignment
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student10@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Use marking workflow | Yes |
    When I follow "Test assignment name"
    Then I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And "Student 1" row "Grade" column of "generaltable" table should contain "50.00"

  @javascript
  Scenario: Update the grade for a team assignment
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student10@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Use marking workflow | Yes |
      | Students submit in groups | Yes |
      | Group mode | No groups |
    When I follow "Test assignment name"
    Then I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the field "Grade out of 100" to "50"
    And I press "Save changes"
    And I press "Ok"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And "Student 1" row "Grade" column of "generaltable" table should contain "50.00"
