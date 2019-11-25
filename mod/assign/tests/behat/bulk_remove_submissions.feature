@mod @mod_assign
Feature: Bulk remove submissions
  In order to reset the assignment submission of multiple students
  As a teacher with the capability to edit submissions
  I need to be able to remove student submissions by bulk

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |

  @javascript
  Scenario: Bulk remove submissions should remove the data that was submitted
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability                     | permission |
      | mod/assign:editothersubmission | Allow      |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I set the field "selectall" to "1"
    When I set the field "operation" to "Remove submission"
    And I click on "Go" "button" confirming the dialogue
    Then I should not see "I'm the student1 submission"
    And I should not see "I'm the student2 submission"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should not see "I'm the student1 submission"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should not see "I'm the student2 submission1"

  @javascript
  Scenario: Bulk remove submissions should be unavailable if the user is missing the editing submission capability
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I set the field "selectall" to "1"
    Then I should not see "Remove submission" in the "Choose operation" "select"

  @javascript
  Scenario: Notification should be displayed when non-group users are selected for submission bulk removal
            in separate group mode
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability                     | permission |
      | mod/assign:editothersubmission | Allow      |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | groupmode | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I set the field "selectall" to "1"
    When I set the field "operation" to "Remove submission"
    And I click on "Go" "button" confirming the dialogue
    Then I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I should see "The submission of Student 1 cannot be removed"
    And I should see "The submission of Student 2 cannot be removed"

  @javascript
  Scenario: Bulk remove submission when group users are added to the bulk
            removing submissions process in separate group mode
    Given the following "group members" exist:
      | user     | group   |
      | student1 | G1 |
      | student2 | G1 |
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability                     | permission |
      | mod/assign:editothersubmission | Allow      |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | groupmode | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I set the field "selectall" to "1"
    When I set the field "operation" to "Remove submission"
    And I click on "Go" "button" confirming the dialogue
    Then I should not see "I'm the student1 submission"
    And I should not see "I'm the student2 submission"
