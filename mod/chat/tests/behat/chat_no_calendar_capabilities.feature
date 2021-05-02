@mod @mod_chat
Feature: Chat with no calendar capabilites
  In order to allow work effectively
  As a teacher
  I need to be able to create chats even when I cannot edit calendar events

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
    And the following "activity" exists:
      | activity                      | chat                  |
      | course                        | C1                    |
      | idnumber                      | 0001                  |
      | name                          | Test chat name        |
      | intro                         | Test chat description |
      | section                       | 1                     |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Users > Permissions" in current page administration
    And I override the system permissions of "Teacher" role with:
      | capability | permission |
      | moodle/calendar:manageentries | Prohibit |
    And I am on "Course 1" course homepage
    And I follow "Test chat name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Repeat/publish session times | No repeats - publish the specified time only |
      | id_chattime_day | 1 |
      | id_chattime_month | 1 |
      | id_chattime_year | 2017 |
    And I press "Save and return to course"
    And I log out

  Scenario: Editing a chat
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test chat name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | id_chattime_year | 2018 |
    And I press "Save and return to course"
    Then I should see "Test chat name"
