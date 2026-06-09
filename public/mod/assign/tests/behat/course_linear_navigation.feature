@mod @mod_assign
Feature: Display the course linear navigation in the assignment pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in assignment pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name        | assignsubmission_onlinetext_enabled | submissiondrafts | duedate       |
      | assign   | C1     | Assignment1 | 1                                   | 0                | ##tomorrow##  |

  @javascript
  Scenario: As a student I should see the course linear navigation in assignment pages that allow it
    Given I am on the "Assignment1" "assign activity" page logged in as "student"
    Then the course linear navigation should be visible
    But I press "Add submission"
    And the course linear navigation should not be visible
    And I set the field "Online text" to "This is the submission text."
    And I press "Save changes"
    And the course linear navigation should be visible
    And I press "Edit submission"
    And the course linear navigation should not be visible

  @javascript
  Scenario: As a teacher I should see the course linear navigation in assignment pages that allow it
    Given the following "mod_assign > submissions" exist:
      | assign      | user    | onlinetext                       |
      | Assignment1 | student | I'm the student first submission |
    And the following "role capability" exists:
      | role                           | editingteacher |
      | mod/assign:editothersubmission | allow          |
    And I am on the "Assignment1" "assign activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    But I click on "Grade" "link" in the "region-main" "region"
    And I set the field "Grade out of 100" to "90"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    And the course linear navigation should not be visible
    And I open the action menu in "Student" "table_row"
    And I follow "Grant extension"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I open the action menu in "Student" "table_row"
    When I follow "Remove submission"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I navigate to "Overrides" in current page administration
    And the course linear navigation should not be visible
    And I press "Add user override"
    And the course linear navigation should not be visible
    And I press "Cancel"

  @javascript
  Scenario: As a teacher I should see the course linear navigation in activity pages that allow it
    # These features can be tested using any activity supporting them.
    # Added here because assignments are the first ones to add these exceptions.
    Given I am on the "Assignment1" "assign activity" page logged in as "teacher"
    And I follow "Advanced grading"
    And the course linear navigation should not be visible
    And I set the field "Change active grading method to" to "Rubric"
    And the course linear navigation should not be visible
    And I click on "Define new grading form from scratch" "link" in the "region-main" "region"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I set the field "Change active grading method to" to "Marking guide"
    And I click on "Define new grading form from scratch" "link" in the "region-main" "region"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I navigate to "Filters" in current page administration
    And the course linear navigation should not be visible
    And I navigate to "Permissions" in current page administration
    And the course linear navigation should not be visible
    And I set the field "Advanced role override" to "Student"
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I navigate to "Logs" in current page administration
    And the course linear navigation should not be visible
    And I navigate to "Backup" in current page administration
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I click on "Yes" "button" in the "Cancel backup" "dialogue"
    And I navigate to "Restore" in current page administration
    And I press "Manage activity backups"
    And the course linear navigation should not be visible
