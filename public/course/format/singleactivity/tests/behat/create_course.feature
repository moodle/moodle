@format @format_singleactivity @block_site_main_menu
Feature: Courses can be created in Single Activity mode
  In order to create a single activity course
  As a manager
  I need to create courses and set default values on them

  Scenario: Create a course using Single Activity format as a custom course creator
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
    When I log in as "kevin"
    And I am on site homepage
    And I press "Add a new course"
    And I set the following fields to these values:
      | Course full name  | My first course |
      | Course short name | C1              |
      | Format            | Single activity |
    And I press "Update format"
    Then I should see "Quiz" in the "Type of activity" "field"
    And I should see "Forum" in the "Type of activity" "field"
    # Check that not all the activity types are in the dropdown.
    And I should not see "Text and media" in the "Type of activity" "field"
    And I should not see "Subsection" in the "Type of activity" "field"
    And I should not see "Question bank" in the "Type of activity" "field"
    And I set the field "Type of activity" to "Assignment"
    And I press "Save and display"
    And I should see "New Assignment"
    And I set the field "Assignment name" to "My assignment"
    And I press "Save and return to course"
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | site_main_menu | Course       | C1        |             *   | site-pre      |
    And I should see "My first course" in the "page-header" "region"
    And I should see "My assignment" in the "Additional activities" "block"
