@core @core_course
Feature: Reset course
  In order to reuse a course
  As a teacher
  I need to clear all user data and reset the course to its initial state

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1        | topics | 2           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity                            | assign               |
      | course                              | C1                   |
      | name                                | Test assignment name |
      | assignsubmission_onlinetext_enabled | 1                    |
      | assignsubmission_file_enabled       | 0                    |
      | submissiondrafts                    | 0                    |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |

  Scenario: Reset course select and deselect buttons
    When I am on the "Course 1" "reset" page logged in as "teacher1"
    Then I should see "Collapse all"
    And the field "Events" matches value "1"
    And the field "All notes" matches value "1"
    And the field "All comments" matches value "0"
    And the field "Completion data" matches value "0"
    And the field "All submissions" matches value "1"
    And the field "All user overrides" matches value "1"
    And the field "All group overrides" matches value "1"
    # Check fields are unchecked when Deselect all is clicked
    And I click on "Deselect all" "button"
    And the field "Events" matches value "0"
    And the field "All notes" matches value "0"
    And the field "All comments" matches value "0"
    And the field "Completion data" matches value "0"
    And the field "All submissions" matches value "0"
    And the field "All user overrides" matches value "0"
    And the field "All group overrides" matches value "0"
    # Check default fields are checked again when Select default is clicked
    And I click on "Select default" "button"
    And the field "Events" matches value "1"
    And the field "All notes" matches value "1"
    And the field "All comments" matches value "0"
    And the field "Completion data" matches value "0"
    And the field "All submissions" matches value "1"
    And the field "All user overrides" matches value "1"
    And the field "All group overrides" matches value "1"

  @javascript
  Scenario: Reset course with default settings
    Given I am on the "Course 1" "reset" page logged in as "teacher1"
    When I click on "Reset course" "button"
    And I click on "Reset course" "button" in the "Reset course?" "dialogue"
    Then I should see "Done" in the "All notes" "table_row"
    And I should see "Done" in the "All local role assignments" "table_row"
    And I should see "Done" in the "All submissions" "table_row"
    And I click on "Continue" "button"
    # Check that you're redirected to the course page.
    And I should not see "Reset course"
    # Check the course has been reset.
    And I navigate to course participants
    And I should see "Teacher 1"
    And I should not see "Student 1"
    And I am on the "Test assignment name" "assign activity" page
    And I should see "0" in the "Submitted" "table_row"
