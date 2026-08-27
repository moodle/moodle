@mod @mod_assign
Feature: Assignment multimarking settings
  In order to allow managing assignments
  As a teacher
  I need to know the impact of changing assignment multimarking settings.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1                       |
    And the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | teacher2  | Teacher    | 2         | teacher2@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
      | student2  | Student    | 2         | student2@example.com  |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | teacher2  | C1      | editingteacher  |
      | student1  | C1      | student         |
      | student2  | C1      | student         |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |

  @javascript
  Scenario: Changing settings that recalculate grades from marks give a warning
    # Initially add grades.
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    And I go to "Student 1" "Test assignment name" activity advanced grading page
    And I set the field "Grade out of 100" to "40"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    And "Student 1" row "Grade" column of "generaltable" table should contain "40.00"

    # Require explicit confirmation of what to do with existing grades.
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Use marking workflow" to "Yes"
    And I set the field "Use marking allocation" to "Yes"
    And I set the field "Number of required markers" to "2"
    And I set the field "Number of optional markers" to "3"
    And I should see "Update agreed grades"
    And I press "Save and display"
    Then "#id_error_multimarkupdate" "css_element" should be visible
    And I set the field "Update agreed grades" to "Keep current agreed grades"
    And I press "Save and display"
    And I navigate to "Submissions" in current page administration
    And "Student 1" row "Grade" column of "generaltable" table should contain "40.00"

    # Change multi marking calculation method.
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the "Calculate grade" "select" should be disabled
    And I set the field "Update agreed grades" to "Recalculate agreed grades"
    And I set the field "Calculate grade" to "Average mark"
    And I press "Save and display"
    And I navigate to "Submissions" in current page administration
    And "Student 1" row "Grade" column of "generaltable" table should contain ""
