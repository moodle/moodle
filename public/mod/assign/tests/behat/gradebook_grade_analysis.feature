@mod @mod_assign @javascript
Feature: Link to the assignment grader from the gradebook grader report
  In order to quickly view submissions pertaining to student grades in the gradebook
  As a teacher
  I need to be able to link directly to the grader pane from the grader report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Assignment introduction |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | maxattempts                         | -1                      |
      | attemptreopenmethod                 | manual                  |
      | hidegrader                          | 1                       |
      | submissiondrafts                    | 0                       |
    And the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | student1  | C1      | student         |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                       |
      | Test assignment name  | student1  | I'm the student first submission |

  Scenario: Perform grade analysis via the User report as a teacher
    Given I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I navigate to "View > User report" in the course gradebook
    And I click on "Student 1" in the "Search users" search combo box
    When I open the action menu in "Test assignment name" "table_row"
    And "Grade analysis" "link" should exist
    And I follow "Grade analysis"
    Then I should see "Submitted for grading"
    And I should see "Current grade in gradebook"

  Scenario: Perform grade analysis via the User report as a student
    Given I am on the "Course 1" "grades > User report > View" page logged in as "student1"
    When I open the action menu in "Test assignment name" "table_row"
    Then "Grade analysis" "link" should exist
    And I follow "Grade analysis"
    And I should see "Assignment introduction"
    And I should see "Submitted for grading"
