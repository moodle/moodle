@block @block_calendar_month
Feature: Enable the calendar block in a course and test it's functionality
  In order to enable the calendar block in a course
  As a teacher
  I can add the calendar block to a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
      | student2 | Student | 2 | student2@example.com | S2 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |

  Scenario: Add the block to a the course
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    When I add the "Calendar" block
    Then I should see "Events key" in the "Calendar" "block"

  @javascript
  Scenario: View a global event in the calendar block
    Given I log in as "admin"
    And I create a calendar event with form data:
      | id_eventtype | Site |
      | id_name | Site Event |
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Calendar" block
    And I hover over today in the calendar
    Then I should see "Site Event"

  @javascript
  Scenario: Filter site events in the calendar block
    Given I log in as "admin"
    And I create a calendar event with form data:
      | id_eventtype | Site |
      | id_name | Site Event |
    And I log out
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Calendar" block
    And I create a calendar event with form data:
      | id_eventtype | Course |
      | id_name | Course Event |
    And I follow "Course 1"
    And I follow "Hide global events"
    And I hover over today in the calendar
    Then I should not see "Site Event"
    And I should see "Course Event"

  @javascript
  Scenario: View a course event in the calendar block
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Calendar" block
    And I create a calendar event with form data:
      | id_eventtype | Course |
      | id_name | Course Event |
    When I follow "Course 1"
    And I hover over today in the calendar
    Then I should see "Course Event"

  @javascript
  Scenario: Filter course events in the calendar block
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Calendar" block
    And I create a calendar event with form data:
      | id_eventtype | Course |
      | id_name | Course Event |
    And I follow "Course 1"
    And I create a calendar event with form data:
      | id_eventtype | User |
      | id_name | User Event |
    When I am on homepage
    And I follow "Course 1"
    And I follow "Hide course events"
    And I hover over today in the calendar
    Then I should not see "Course Event"
    And I should see "User Event"

  @javascript
  Scenario: View a user event in the calendar block
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Calendar" block
    And I create a calendar event with form data:
      | id_eventtype | User |
      | id_name | User Event |
    When I am on homepage
    And I follow "Course 1"
    And I hover over today in the calendar
    Then I should see "User Event"

  @javascript
  Scenario: Filter user events in the calendar block
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Calendar" block
    And I create a calendar event with form data:
      | id_eventtype | Course |
      | id_name | Course Event |
    And I follow "Course 1"
    And I create a calendar event with form data:
      | id_eventtype | User |
      | id_name | User Event |
    When I am on homepage
    And I follow "Course 1"
    And I follow "Hide user events"
    And I hover over today in the calendar
    Then I should not see "User Event"
    And I should see "Course Event"

  @javascript
  Scenario: View a group event in the calendar block
    Given the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group   |
      | student1 | G1 |
      | student2 | G2 |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Edit settings" node in "Course administration"
    And I set the following fields to these values:
      | id_groupmode | Separate groups |
      | id_groupmodeforce | Yes |
    And I press "Save and display"
    And I turn editing mode on
    And I add the "Calendar" block
    And I create a calendar event with form data:
      | id_eventtype | Group |
      | id_groupid | Group 1 |
      | id_name | Group Event |
    And I log out
    Then I log in as "student1"
    And I follow "Course 1"
    And I hover over today in the calendar
    And I should see "Group Event"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I hover over today in the calendar
    And I should not see "Group Event"

  @javascript
  Scenario: Filter group events in the calendar block
    Given the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "group members" exist:
      | user     | group   |
      | student1 | G1 |
      | student2 | G2 |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Edit settings" node in "Course administration"
    And I set the following fields to these values:
      | id_groupmode | Separate groups |
      | id_groupmodeforce | Yes |
    And I press "Save and display"
    And I turn editing mode on
    And I add the "Calendar" block
    And I create a calendar event with form data:
      | id_eventtype | Course |
      | id_name | Course Event 1 |
    And I follow "Course 1"
    And I create a calendar event with form data:
      | id_eventtype | Group |
      | id_groupid | Group 1 |
      | id_name | Group Event 1 |
    And I log out
    Then I log in as "student1"
    And I follow "Course 1"
    And I follow "Hide group events"
    And I hover over today in the calendar
    And I should not see "Group Event 1"
    And I should see "Course Event 1"
