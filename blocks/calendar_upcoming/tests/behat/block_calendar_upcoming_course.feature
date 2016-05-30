@block  @block_calendar_upcoming
Feature: Enable the upcoming events block in a course
  In order to enable the calendar block in a course
  As a teacher
  I can view the event in the upcoming events block

  Scenario: View a global event in the upcoming events block in a course
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
      | id_name | My Site Event |
    And I log out
    When I log in as "teacher1"
    Then I should see "My Site Event" in the "Upcoming events" "block"
