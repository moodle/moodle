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
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
      | Course 2 | C2 | topics |
      | Course 3 | C3 | topics |
      | Course 4 | C4 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student3 | C1 | student |
      | teacher1 | C1 | teacher |
      | admin    | C1 | editingteacher |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | teacher1 | G1 |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Calendar" block
    And I log out

  @javascript
  Scenario: Create a site event
    Given I log in as "admin"
    And I create a calendar event with form data:
      | Type of event | site |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Full calendar"
    And I should see "Really awesome event!"
    And I log out
    And I log in as "student2"
    And I follow "Full calendar"
    And I should see "Really awesome event!"

  @javascript
  Scenario: Create a course event
    Given I log in as "teacher1"
    And I create a calendar event with form data:
      | Type of event | course |
      | Course        | Course 1 |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I follow "Full calendar"
    And I click on "Really awesome event!" "link"
    And "Course 1" "link" should exist in the "Really awesome event!" "dialogue"
    And I click on "Close" "button" in the "Really awesome event!" "dialogue"
    And I log out
    And I log in as "student2"
    And I follow "Full calendar"
    Then I should not see "Really awesome event!"

  @javascript
  Scenario: Create a group event
    Given I log in as "teacher1"
    And I follow "Full calendar"
    And I set the field "course" to "C1"
    And I create a calendar event:
      | Type of event | group |
      | Group         | Group 1 |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event |
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I follow "Full calendar"
    Then I follow "Really awesome event!"

  @javascript
  Scenario: Create a user event
    Given I log in as "teacher1"
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    And I follow "Full calendar"
    Then I should not see "Really awesome event!"

  @javascript
  Scenario: Delete an event
    Given I log in as "teacher1"
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
    And I am on "Course 1" course homepage
    When I follow "Full calendar"
    And I click on "Really awesome event!" "link"
    And I click on "Delete" "button" in the "Really awesome event!" "dialogue"
    And I click on "Delete event" "button"
    And I wait to be redirected
    Then I should not see "Really awesome event!"

  @javascript
  Scenario: Edit an event
    Given I log in as "teacher1"
    And I create a calendar event with form data:
      | Type of event | user |
      | Event title | Really awesome event! |
      | Description | Come join this awesome event, sucka! |
      | Location | Cube office |
    And I am on "Course 1" course homepage
    When I follow "Full calendar"
    And I click on "Really awesome event!" "link"
    And ".location-content" "css_element" should exist
    And I should see "Cube office"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | Event title | Mediocre event :( |
      | Description | Wait, this event isn't that great. |
      | Location | |
    And I press "Save"
    And I should see "Mediocre event"
    And I click on "Mediocre event :(" "link"
    Then I should see "Mediocre event"
    And ".location-content" "css_element" should not exist

  @javascript
  Scenario: Module events editing
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And the following "activities" exist:
      | activity | course | idnumber | name        | intro                   | timeopen      | timeclose     |
      | choice   | C1     | choice1  | Test choice | Test choice description | ##today## | ##today##  |
    When I follow "Full calendar"
    And I set the field "course" to "C1"
    Then I should see "Test choice opens"
    And I should see "Test choice closes"
    When I click on "Test choice opens" "link"
    Then "Delete" "button" should not exist in the "Test choice opens" "dialogue"
    And "Edit" "button" should not exist in the "Test choice opens" "dialogue"
    And I should see "Course event"
    When I click on "Go to activity" "link"
    And I wait to be redirected
    Then I should see "Test choice"
    And I am on "Course 1" course homepage
    And I follow "Full calendar"
    When I click on "Test choice closes" "link"
    Then "Delete" "button" should not exist in the "Test choice closes" "dialogue"
    And "Edit" "button" should not exist in the "Test choice closes" "dialogue"
    And I should see "Course event"
    When I click on "Go to activity" "link"
    And I wait to be redirected
    Then I should see "Test choice"

  @javascript
  Scenario: Attempt to create event without fill required fields should display validation errors
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Full calendar"
    And I click on "New event" "button"
    When I click on "Save" "button"
    Then I should see "Required" in the "Event title" "form_row"
    And I am on homepage
    And I follow "Full calendar"
    And I click on "New event" "button"
    And I set the field "Event title" to "Really awesome event!"
    And I set the field "Type of event" to "Course"
    When I click on "Save" "button"
    And I should see "Select a course" in the "Course" "form_row"

  @javascript
  Scenario: Default event type selection in the event form
    Given I log in as "teacher1"
    When I am viewing site calendar
    And I click on "New event" "button"
    Then the field "Type of event" matches value "User"
    And I click on "Close" "button" in the "New event" "dialogue"
    And I set the field "course" to "C1"
    When I click on "New event" "button"
    Then the field "Type of event" matches value "Course"

  @javascript
  Scenario: Admin can only see all courses if calendar_adminseesall setting is enabled.
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I am viewing site calendar
    And I click on "New event" "button"
    And I set the field "Type of event" to "Course"
    When I expand the "Course" autocomplete
    Then "Course 1" "autocomplete_suggestions" should exist
    And "Course 2" "autocomplete_suggestions" should not exist
    And "Course 3" "autocomplete_suggestions" should not exist
    And I click on "Close" "button" in the "New event" "dialogue"
    And I am on site homepage
    And I navigate to "Appearance > Calendar" in site administration
    And I set the field "Admins see all" to "1"
    And I press "Save changes"
    And I am viewing site calendar
    And I click on "New event" "button"
    And I set the field "Type of event" to "Course"
    When I expand the "Course" autocomplete
    Then "Course 1" "autocomplete_suggestions" should exist
    And "Course 2" "autocomplete_suggestions" should exist
    And "Course 3" "autocomplete_suggestions" should exist

  @javascript
  Scenario: Students can only see user event type by default.
    Given I log in as "student1"
    And I am viewing site calendar
    When I click on "New event" "button"
    Then I should see "User" in the "div#fitem_id_staticeventtype" "css_element"
    And I am on "Course 1" course homepage
    And I follow "Full calendar"
    When I click on "New event" "button"
    Then I should see "User" in the "div#fitem_id_staticeventtype" "css_element"
    And I click on "Close" "button" in the "New event" "dialogue"
    And I log out
    Given I log in as "admin"
    And I navigate to "Appearance > Calendar" in site administration
    And I set the field "Admins see all" to "1"
    And I press "Save changes"
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Full calendar"
    When I click on "New event" "button"
    Then I should see "User" in the "div#fitem_id_staticeventtype" "css_element"

  @javascript @accessibility
  Scenario: The calendar page must be accessible
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Full calendar"
    Then the page should meet accessibility standards
    And the page should meet "wcag131, wcag143, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag143, wcag412" extra tests

  @javascript
  Scenario: The calendar page should be responsive
    Given I log in as "admin"
    And I am viewing site calendar
    And I create a calendar event:
      | Type of event  | site      |
      | Event title    | Event 1:1 |
      | timestart[day] | 1         |
    When I change viewport size to "1200x1000"
    Then I should see "Event 1:1"
    And I change viewport size to "600x1000"
    # We need to give the browser a couple seconds to re-render the page after the screen has been resized.
    And I wait "1" seconds
    And I should not see "Event 1:1"
    And I hover over day "1" of this month in the full calendar page
    And I should see "Event 1:1"

  @javascript
  Scenario: Admin can create and edit course events if calendar_adminseesall setting is disabled
    Given I log in as "admin"
    And the following config values are set as admin:
      | calendar_adminseesall | 0 |
    And I am on "Course 4" course homepage with editing mode on
    And I add the "Upcoming events" block
    And I click on "Go to calendar..." "link" in the "Upcoming events" "block"
    And I click on "New event" "button"
    And I should see "Course" in the "Type of event" "select"
    And "Course 4" "autocomplete_selection" should exist
    And I set the field "Event title" to "Test course event"
    And I click on "Save" "button"
    And I should see "Test course event"
    When I click on "Edit" "link" in the "region-main" "region"
    Then the field "Event title" matches value "Test course event"
    And the field "Type of event" matches value "Course"
    And "Course 4" "autocomplete_selection" should exist

  @javascript
  Scenario: Changing the event type should clear previous data
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Full calendar"
    And I set the field "course" to "C1"
    And I press "New event"
    And I set the following fields to these values:
      | Event title | Group 1 event |
      | Type of event | Group       |
    And I press "Save"
    And I am on "Course 1" course homepage
    And I follow "Full calendar"
    And I click on "Group 1 event" "link"
    And I should see "Group event"
    And I should see "Group 1"
    When I click on "Edit" "button"
    And I set the following fields to these values:
      | Event title | My own user event |
      | Type of event | user |
    And I press "Save"
    And I click on "My own user event" "link"
    Then I should see "User event"
    And I should not see "Group 1"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | Event title | Site event |
      | Type of event | site |
    And I press "Save"
    And I click on "Site event" "link"
    And I should see "Site event"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | Event title | Course 1 event |
      | Type of event | course |
    And I expand the "Course" autocomplete
    And I click on "Course 1" item in the autocomplete list
    And I press "Save"
    And I click on "Course 1 event" "link"
    And I should see "Course event"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | Event title | Category event |
      | Type of event | category |
    And I press "Save"
    And I click on "Category event" "link"
    And I should see "Category event"
