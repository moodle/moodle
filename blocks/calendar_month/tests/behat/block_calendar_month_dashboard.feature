@block @block_calendar_month
Feature: View a site event on the dashboard
  In order to view a site event
  As a student
  I can view the event in the calendar

  @javascript
  Scenario: View a site event in the calendar block on the dashboard
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | S1 |
    And I log in as "admin"
    And I create a calendar event with form data:
      | id_eventtype | Site |
      | id_name | Site Event |
    And I log out
    When I log in as "student1"
    And I hover over today in the calendar
    Then I should see "Site Event"
