@block @block_calendar_month
Feature: Enable the calendar block in a course
  In order to enable the calendar block in a course
  As a teacher
  I can add the calendar block to a course

  @javascript
  Scenario: View a global event in the calendar block in a course
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    When I log in as "admin"
    And I create a calendar event with form data:
      | id_eventtype | Site |
      | id_name | Site Event |
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Calendar" block
    And I hover over today in the calendar
    And I should see "Site Event"
