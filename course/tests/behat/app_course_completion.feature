@core @core_course @app @javascript
Feature: Check course completion feature.
  In order to track the progress of the course on mobile device
  As a student
  I need to be able to update the activity completion status.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |

  Scenario: Complete the activity manually by clicking at the completion checkbox.
    Given the following "activities" exist:
      | activity | name         | course | idnumber | completion | completionview |
      | forum    | First forum  | C1     | forum1   | 1          | 0              |
      | forum    | Second forum | C1     | forum2   | 1          | 0              |
    When I enter the app
    And I log in as "student1"
    And I press "Course 1" near "Recently accessed courses" in the app
    # Set activities as completed.
    And I should see "0%"
    And I press "Not completed: First forum. Select to mark as complete." in the app
    And I should see "50%"
    And I press "Not completed: Second forum. Select to mark as complete." in the app
    And I should see "100%"
    # Set activities as not completed.
    And I press "Completed: First forum. Select to mark as not complete." in the app
    And I should see "50%"
    And I press "Completed: Second forum. Select to mark as not complete." in the app
    And I should see "0%"
