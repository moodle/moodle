@core @core_calendar @javascript
Feature: Perform calendar filter actions
  In order to test the calendar filters with preselected options
  As a user
  I need to filter calendar events by certain criteria

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "categories" exist:
      | name          | idnumber      | category |
      | Year          | year          |          |
      | Department C1 | department-c1 | year     |
    And the following "courses" exist:
      | fullname | shortname | format | category      |
      | Course 1 | C1        | topics | department-c1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
    And the following "events" exist:
      | name       | eventtype |
      | Site event | site      |
    And the following "events" exist:
      | name      | eventtype | course |
      | C1 event  | course    | C1     |
    And the following "events" exist:
      | name        | eventtype | category      |
      | Dep1a event | category  | department-c1 |
    And the following "events" exist:
      | name         | eventtype | group | course |
      | Group1 event | group     | G1    | C1     |

  Scenario: Teacher of a Course can see his events and filter them
    Given I log in as "teacher1"
    When I follow "Calendar" in the user menu
    Then I should see "C1 event"
    And I should see "Dep1a event"
    And I should see "Group1 event"
    And I click on "Hide category events" "link"
    And I should see "C1 event"
    And I should not see "Dep1a event"
    And I should see "Group1 event"
    And I click on "Hide course events" "link"
    And I should not see "C1 event"
    And I should not see "Dep1a event"
    And I should see "Group1 event"

  Scenario: Teacher of a Course can see only non filtered events from user preferences calendar_savedflt
    Given I log in as "teacher1"
    And the following "user preferences" exist:
      | user      | preference          | value |
      | teacher1  | calendar_persistflt | 1     |
      | teacher1  | calendar_savedflt   | 13    |
    When I follow "Calendar" in the user menu
    Then I should not see "C1 event"
    And I should see "Dep1a event"
    And I should not see "Group1 event"

  Scenario: Teacher of a Course can see all events because session is used and not user preference calendar_savedflt
    Given I log in as "teacher1"
    And the following "user preferences" exist:
      | user      | preference          | value |
      | teacher1  | calendar_persistflt | 0     |
      | teacher1  | calendar_savedflt   | 13    |
    When I follow "Calendar" in the user menu
    Then I should see "C1 event"
    And I should see "Dep1a event"
    And I should see "Group1 event"
