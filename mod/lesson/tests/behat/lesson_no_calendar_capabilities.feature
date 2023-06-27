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
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Permissions" in current page administration
    And I override the system permissions of "Teacher" role with:
      | capability | permission |
      | moodle/calendar:manageentries | Prohibit |
    And I log out

  Scenario: Editing a lesson
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
      | Description | Test lesson description |
      | id_available_enabled | 1 |
      | id_available_day | 1 |
      | id_available_month | 1 |
      | id_available_year | 2017 |
      | id_deadline_enabled | 1 |
      | id_deadline_day | 1 |
      | id_deadline_month | 2 |
      | id_deadline_year | 2017 |
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test lesson name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_available_year | 2018 |
      | id_deadline_year | 2018 |
    And I press "Save and return to course"
    Then I should see "Test lesson name"
