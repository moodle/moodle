@mod @mod_scorm
Feature: Scorm availability
  In order to control when a SCORM activity is available to students
  As a teacher
  I need be able to set availability dates for the SCORM

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname  | email                |
      | student1 | Student    | 1         | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user      | course | role    |
      | student1  | C1     | student |
    And the following "activities" exist:
      | activity | course | name          | packagefilepath                                | timeopen      | timeclose     |
      | scorm    | C1     | Past SCORM    | mod/scorm/tests/packages/singlesco_scorm12.zip | ##yesterday## | ##yesterday## |
      | scorm    | C1     | Current SCORM | mod/scorm/tests/packages/singlesco_scorm12.zip | ##yesterday## | ##tomorrow##  |
      | scorm    | C1     | Future SCORM  | mod/scorm/tests/packages/singlesco_scorm12.zip | ##tomorrow##  | ##tomorrow##  |

  Scenario: Scorm activity with dates in the past should not be available.
    When I am on the "Past SCORM" "scorm activity" page logged in as "student1"
    Then the activity date in "Past SCORM" should contain "Opened:"
    And the activity date in "Past SCORM" should contain "##yesterday noon##%A, %d %B %Y, %I:%M##"
    And the activity date in "Past SCORM" should contain "Closed:"
    And the activity date in "Past SCORM" should contain "##yesterday noon##%A, %d %B %Y, %I:%M##"
    And "Enter" "button" should not exist
    And I should not see "Preview"
    And I am on the "Current SCORM" "scorm activity" page
    And the activity date in "Current SCORM" should contain "Opened:"
    And the activity date in "Current SCORM" should contain "##yesterday noon##%A, %d %B %Y, %I:%M##"
    And the activity date in "Current SCORM" should contain "Closes:"
    And the activity date in "Current SCORM" should contain "##tomorrow noon##%A, %d %B %Y, %I:%M##"
    And "Enter" "button" should exist
    And I should see "Preview"
    And I am on the "Future SCORM" "scorm activity" page
    And the activity date in "Future SCORM" should contain "Opens:"
    And the activity date in "Future SCORM" should contain "##tomorrow noon##%A, %d %B %Y, %I:%M##"
    And the activity date in "Future SCORM" should contain "Closes:"
    And the activity date in "Future SCORM" should contain "##tomorrow noon##%A, %d %B %Y, %I:%M##"
    And "Enter" "button" should not exist
    And I should not see "Preview"
