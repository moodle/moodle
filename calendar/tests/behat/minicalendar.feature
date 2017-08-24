@core @core_calendar
Feature: Open calendar popup
  In order to view calendar information
  As a user
  I need to interact with the calendar

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And I log in as "admin"

  @javascript
  Scenario: I view calendar details of a day with multiple events
    Given I follow "This month"
    And I create a calendar event:
      | Type of event     | site |
      | Event title       | Event 1:1 |
      | timestart[day]    | 1  |
    And I create a calendar event:
      | Type of event     | site |
      | Event title       | Event 1:2 |
      | timestart[day]    | 1  |
    And I reload the page
    When I hover over day "1" of this month in the calendar
    Then I should see "Event 1:1"
    And I should see "Event 1:2"
    And I follow "Home"
    And I hover over day "1" of this month in the calendar
    And I should see "Event 1:1"
    And I should see "Event 1:2"

  @javascript
  Scenario: I view calendar details for today
    Given I follow "This month"
    And I create a calendar event:
      | Type of event     | site |
      | Event title       | Today's event |
    When I hover over today in the calendar
    Then I should see "Today's event"
    And I follow "Home"
    And I hover over today in the calendar
    And I should see "Today's event"
