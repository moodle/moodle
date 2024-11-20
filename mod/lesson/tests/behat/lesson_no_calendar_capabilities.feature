@mod @mod_lesson
Feature: Lesson with no calendar capabilites
  In order to allow work effectively
  As a teacher
  I need to be able to create lessons even when I cannot edit calendar events

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    Given the following "activity" exists:
      | activity      | lesson                  |
      | course        | C1                      |
      | idnumber      | 0001                    |
      | name          | Test lesson name        |
    And I log in as "admin"
    And I am on the "Course 1" "permissions" page
    And I override the system permissions of "Teacher" role with:
      | capability | permission |
      | moodle/calendar:manageentries | Prohibit |

  Scenario: Editing a lesson
    Given I am on the "Test lesson name" "lesson activity editing" page logged in as admin
    And I set the following fields to these values:
      | id_available_enabled | 1 |
      | id_available_day | 1 |
      | id_available_month | 1 |
      | id_available_year | 2017 |
      | id_deadline_enabled | 1 |
      | id_deadline_day | 1 |
      | id_deadline_month | 2 |
      | id_deadline_year | 2017 |
    And I press "Save and return to course"
    When I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | id_available_year | 2018 |
      | id_deadline_year | 2018 |
    And I press "Save and return to course"
    Then I should see "Test lesson name"
