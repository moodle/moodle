@format @format_singleactivity
Feature: Courses can be created in Single Activity mode
  In order to create a single activity course
  As a manager
  I need to create courses and set default values on them

  Scenario: Create a course as a custom course creator
    Given the following "users" exist:
      | username  | firstname | lastname | email          |
      | kevin  | Kevin   | the        | kevin@example.com |
    And the following "roles" exist:
      | shortname | name    | archetype |
      | creator   | Creator |           |
    And the following "system role assigns" exist:
      | user   | role    | contextlevel |
      | kevin  | creator | System       |
    And the following "role capability" exists:
      | role                           | creator |
      | moodle/course:create           | allow   |
      | moodle/course:update           | allow   |
      | moodle/course:manageactivities | allow   |
      | moodle/course:viewparticipants | allow   |
      | moodle/role:assign             | allow   |
      | mod/quiz:addinstance           | allow   |
    When I log in as "kevin"
    And I am on site homepage
    And I press "Add a new course"
    And I set the following fields to these values:
      | Course full name  | My first course |
      | Course short name | myfirstcourse |
      | Format | Single activity |
    And I press "Update format"
    Then I should see "Quiz" in the "Type of activity" "field"
    And I should not see "Forum" in the "Type of activity" "field"
    And I press "Save and display"
    And I should see "New Quiz"
