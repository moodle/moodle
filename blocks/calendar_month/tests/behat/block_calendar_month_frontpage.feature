@block @block_calendar_month
Feature: Enable the calendar block on the site front page
  In order to enable the calendar block on the site front page
  As an admin
  I can add the calendar block on the site front page

  @javascript
  Scenario: View a site event in the calendar block on the front page
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | S1 |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Calendar" block
    And I create a calendar event with form data:
      | id_eventtype | Site |
      | id_name | Site Event |
    And I log out
    When I log in as "student1"
    And I am on site homepage
    And I hover over today in the mini-calendar block
    Then I should see "Site Event"
