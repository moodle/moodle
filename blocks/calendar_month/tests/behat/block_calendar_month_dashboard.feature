@block @block_calendar_month
Feature: View a site event on the dashboard
  In order to view a site event
  As a student
  I can view the event in the calendar

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | S1 |
    And I log in as "admin"
    And I create a calendar event with form data:
      | id_eventtype | Site |
      | id_name | Site Event |
    And I log out

  @javascript
  Scenario: View a site event in the calendar block on the dashboard
    Given I log in as "student1"
    When I hover over today in the mini-calendar block
    Then I should see "Site Event"

  @javascript
  Scenario: The calendar block on the dashboard should be responsive
    Given I log in as "student1"
    When I change viewport size to "1200x1000"
    Then I should see "Site Event"
    And I change viewport size to "600x1000"
    # We need to give the browser a couple seconds to re-render the page after the screen has been resized.
    And I wait "1" seconds
    And I should not see "Site Event"
    And I hover over today in the mini-calendar block
    And I should see "Site Event"
