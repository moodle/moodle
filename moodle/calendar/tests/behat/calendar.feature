@core @core_calendar
Feature: Perform basic calendar functionality
  In order to ensure the calendar works as expected
  As an admin
  I need to create calendar data

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Calendar" block

  Scenario: Create a site event
    And I create a calendar event with form data:
      | Type of event | site |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "This month"
    And I should see "Really awesome event!"
    And I log out
    And I log in as "student2"
    And I follow "This month"
    And I should see "Really awesome event!"

  Scenario: Create a course event
    And I create a calendar event with form data:
      | Type of event | course |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "This month"
    And I should see "Really awesome event!"
    And I log out
    And I log in as "student2"
    And I follow "This month"
    And I should not see "Really awesome event!"

  Scenario: Create a group event
    And I create a calendar event with form data:
      | Type of event | group |
      | Group | Group 1 |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "This month"
    And I follow "Really awesome event!"
    And "Group 1" "text" should exist in the ".eventlist" "css_element"
    And I log out
    And I log in as "student3"
    And I follow "This month"
    And I should not see "Really awesome event!"

  Scenario: Create a user event
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "This month"
    And I should not see "Really awesome event!"

  Scenario: Delete an event
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I click on "Delete event" "link" in the ".event div.commands" "css_element"
    And I click on "Delete" "button"
    And I should not see "Really awesome event!"

  Scenario: Edit an event
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I click on "Edit event" "link" in the ".event div.commands" "css_element"
    And I set the following fields to these values:
      | Event title | Mediocre event :( |
      | Description | Wait, this event isn't that great. |
    And I press "Save changes"
    And I should see "Mediocre event"
