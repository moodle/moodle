@mod @mod_folder
Feature: Display the course linear navigation in the folder pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in folder pages

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
      | activity | course | name   |
      | folder   | C1     | Folder |

  Scenario: As a student I should see the course linear navigation in folder pages that allow it
    Given I am on the "Folder" "folder activity" page logged in as "student"
    Then the course linear navigation should be visible

  @javascript
  Scenario: As a teacher I should not see the course linear navigation in folder editing page
    Given I am on the "Folder" "folder activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    And I click on "Edit" "button" in the "region-main" "region"
    And the course linear navigation should not be visible
