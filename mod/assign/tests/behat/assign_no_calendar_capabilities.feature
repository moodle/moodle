@mod @mod_assign
Feature: Assignment with no calendar capabilites
  In order to allow work effectively
  As a teacher
  I need to be able to create assignments even when I cannot edit calendar events

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

  Scenario: Editing an assignment
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name        | Test assignment name        |
      | Description            | Test assignment description |
      | Allow submissions from | ##1 January 2017##          |
      | Due date               | ##1 February 2017##         |
      | Cut-off date           | ##2 February 2017##         |
      | Remind me to grade by  | ##1 March 2017##            |
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Allow submissions from | ##1 January 2018##  |
      | Due date               | ##1 February 2018## |
      | Cut-off date           | ##2 February 2018## |
      | Remind me to grade by  | ##1 March 2018##    |
    And I press "Save and return to course"
    Then I should see "Test assignment name"
