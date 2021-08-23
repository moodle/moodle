@core @core_calendar
Feature: Export calendar events
  In order to be able to use my calendar events outside of Moodle
  As a user
  I need to export calendar events in iCalendar format

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
    And I log in as "student1"

  Scenario: Viewing calendar export options
    Given I follow "Full calendar"
    When I click on "Import or export calendars" "link"
    And I click on "Export calendar" "button"
    Then I should see "All events"
    And I should see "Events related to courses"
    And I should see "Events related to groups"
    And I should see "My personal events"

  Scenario: Generating calendar URL for all events
    Given I follow "Full calendar"
    And I click on "Import or export calendars" "link"
    And I click on "Export calendar" "button"
    And I set the field "All events" to "1"
    And I set the field "Recent and next 60 days" to "1"
    When I click on "Get calendar URL" "button"
    Then the "value" attribute of "Calendar URL" "field" should contain "&preset_what=all&"

  Scenario: Generating calendar URL for course events
    Given I follow "Full calendar"
    And I click on "Import or export calendars" "link"
    And I click on "Export calendar" "button"
    And I set the field "Events related to courses" to "1"
    And I set the field "Recent and next 60 days" to "1"
    When I click on "Get calendar URL" "button"
    Then the "value" attribute of "Calendar URL" "field" should contain "&preset_what=courses&"

  Scenario: Generating calendar URL for group events
    Given I follow "Full calendar"
    And I click on "Import or export calendars" "link"
    And I click on "Export calendar" "button"
    And I set the field "Events related to groups" to "1"
    And I set the field "Recent and next 60 days" to "1"
    When I click on "Get calendar URL" "button"
    Then the "value" attribute of "Calendar URL" "field" should contain "&preset_what=groups&"

  Scenario: Generating calendar URL for category events
    Given I follow "Full calendar"
    And I click on "Import or export calendars" "link"
    And I click on "Export calendar" "button"
    And I set the field "Events related to categories" to "1"
    And I set the field "Recent and next 60 days" to "1"
    When I click on "Get calendar URL" "button"
    Then the "value" attribute of "Calendar URL" "field" should contain "&preset_what=categories&"

  Scenario: Generating calendar URL for user events
    Given I follow "Full calendar"
    And I click on "Import or export calendars" "link"
    And I click on "Export calendar" "button"
    And I set the field "My personal events" to "1"
    And I set the field "Recent and next 60 days" to "1"
    When I click on "Get calendar URL" "button"
    Then the "value" attribute of "Calendar URL" "field" should contain "&preset_what=user&"
