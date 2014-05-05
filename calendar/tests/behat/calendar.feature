@core @core_calendar
Feature: Perform basic calendar functionality
  In order to ensure the calendar works as expected
  As an admin
  I need to create calendar data

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    When I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Calendar" block

  Scenario: Create a site event
    And I create a calendar event with form data:
      | Type of event | site |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
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
    And I follow "Course 1"
    And I follow "This month"
    And I should see "Really awesome event!"
    And I log out
    And I log in as "student2"
    And I follow "This month"
    And I should not see "Really awesome event!"

  Scenario: Create a user event
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "This month"
    And I should not see "Really awesome event!"

  Scenario: Delete an event
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I click on "//div[@class='commands']//a[contains(@href, 'delete')]" "xpath_element"
    And I click on "Delete" "button"
    And I should not see "Really awesome event!"

  Scenario: Edit an event
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I click on "//div[@class='commands']//a[contains(@href, 'edit')]" "xpath_element"
    And I set the following fields to these values:
      | Event title | Mediocre event :( |
      | Description | Wait, this event isn't that great. |
    And I press "Save changes"
    And I should see "Mediocre event"

