@mod @mod_data
Feature: Database with no calendar capabilites
  In order to allow work effectively
  As a teacher
  I need to be able to create databases even when I cannot edit calendar events

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

  Scenario: Editing a database
    Given I log in as "admin"
    And the following "activities" exist:
      | activity   | name                 | intro                       | course | section | idnumber |
      | data       | Test database name   | Test database description   | C1     | 1       | data1    |
    And I am on "Course 1" course homepage
    And I follow "Test database name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_timeavailablefrom_enabled | 1 |
      | id_timeavailablefrom_day | 1 |
      | id_timeavailablefrom_month | 1 |
      | id_timeavailablefrom_year | 2017 |
      | id_timeavailableto_enabled | 1 |
      | id_timeavailableto_day | 1 |
      | id_timeavailableto_month | 4 |
      | id_timeavailableto_year | 2017 |
      | id_timeviewfrom_enabled | 1 |
      | id_timeviewfrom_day | 1 |
      | id_timeviewfrom_month | 3 |
      | id_timeviewfrom_year | 2017 |
      | id_timeviewto_enabled | 1 |
      | id_timeviewto_day | 1 |
      | id_timeviewto_month | 4 |
      | id_timeviewto_year | 2017 |
    And I press "Save and return to course"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test database name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_timeavailablefrom_year | 2018 |
      | id_timeavailableto_year | 2018 |
      | id_timeviewfrom_year | 2018 |
      | id_timeviewto_year | 2018 |
    And I press "Save and return to course"
    Then I should see "Test database name"
