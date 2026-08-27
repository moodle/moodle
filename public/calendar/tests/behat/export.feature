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
    And "Calendar" "link" should exist in the ".breadcrumb" "css_element"
    And "Import or export calendars" "text" should exist in the ".breadcrumb" "css_element"
    And I click on "Export calendar" "button"
    And "Calendar" "link" should exist in the ".breadcrumb" "css_element"
    And "Import or export calendars" "link" should exist in the ".breadcrumb" "css_element"
    And "Export calendar" "text" should exist in the ".breadcrumb" "css_element"
    Then I should see "All events"
    And I should see "Events related to courses"
    And I should see "Events related to groups"
    And I should see "My personal events"

  @javascript
  Scenario: Export calendar in ics format
    And I follow "Full calendar"
    And I press "New event"
    And I set the following fields to these values:
      | Event title         | My event |
      | id_timestart_hour   | 13       |
      | id_timestart_minute | 00       |
    And I press "Save"
    When I click on "Import or export calendars" "link"
    And I click on "Export calendar" "button"
    And I set the field "All events" to "1"
    And I set the field "Recent and next 60 days" to "1"
    And I press "Export"
    And I should see "SUMMARY:My event"
    # We need to split the step in two because Bennu library use days with leading zero and moodle removes it.
    And I should see "##today##DTSTART:%Y%m##"
    And I should see "##today##%dT050000Z##"

  @javascript
  Scenario: Exporting from a course calendar only exports that course's events
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 2 | C2       | topics |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C2     | student |
    And the following "events" exist:
      | name     | eventtype | course | timestart    |
      | C1 event | course    | C1     | ##tomorrow## |
      | C2 event | course    | C2     | ##tomorrow## |
    And the following "events" exist:
      | name       | eventtype | timestart    |
      | Site event | site      | ##tomorrow## |
    And I am viewing site calendar
    And I set the field "List of courses" to "Course 1"
    And I should see "Calendar: Course 1" in the ".page-header-headings" "css_element"
    And I click on "Import or export calendars" "link"
    And I click on "Export calendar" "button"
    And I set the field "All events" to "1"
    And I set the field "Recent and next 60 days" to "1"
    When I press "Export"
    Then I should see "SUMMARY:C1 event"
    # Site events are shown in a course calendar, so they belong in a course export too.
    And I should see "SUMMARY:Site event"
    # The other course's events must not leak into a course-scoped export.
    And I should not see "SUMMARY:C2 event"

  Scenario: A course export URL keeps site events but excludes other courses
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 2 | C2        | topics |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C2     | student |
    And the following "events" exist:
      | name     | eventtype | course | timestart    |
      | C1 event | course    | C1     | ##tomorrow## |
      | C2 event | course    | C2     | ##tomorrow## |
    And the following "events" exist:
      | name       | eventtype | timestart    |
      | Site event | site      | ##tomorrow## |
    When I visit the calendar export URL for "student1" user and "C1" course
    Then I should see "SUMMARY:C1 event"
    # Site events are shown in a course calendar, so they belong in a course export too.
    And I should see "SUMMARY:Site event"
    And I should not see "SUMMARY:C2 event"

  Scenario: Exporting a course the user cannot access does not fall back to their other courses
    Given the following "categories" exist:
      | name       | category | idnumber |
      | Category 3 | 0        | CAT3     |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 3 | C3        | CAT3     | topics |
    And the following "events" exist:
      | name     | eventtype | course | timestart    |
      | C1 event | course    | C1     | ##tomorrow## |
    And the following "events" exist:
      | name              | eventtype | category | timestart    |
      | Category 3 event  | category  | CAT3     | ##tomorrow## |
    And the following "events" exist:
      | name       | eventtype | timestart    |
      | Site event | site      | ##tomorrow## |
    # The user is enrolled in C1 (see background) but not in C3, so an export URL scoped to C3 must
    # not silently fall back to exporting every course the user is enrolled in. Losing access to the
    # course must apply to its category events too, not just to the course events themselves.
    When I visit the calendar export URL for "student1" user and "C3" course
    Then I should see "SUMMARY:Site event"
    And I should not see "SUMMARY:C1 event"
    And I should not see "SUMMARY:Category 3 event"

  Scenario: Exporting a course the user can access without being enrolled includes its events
    Given the following "categories" exist:
      | name       | category | idnumber |
      | Category 4 | 0        | CAT4     |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 4 | C4        | CAT4     | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | Category     | CAT4      |
    And the following "events" exist:
      | name     | eventtype | course | timestart    |
      | C4 event | course    | C4     | ##tomorrow## |
    # The manager can see the C4 calendar without being enrolled in it, so exporting that calendar
    # has to include its events rather than being driven purely by the user's own enrolments.
    When I visit the calendar export URL for "manager1" user and "C4" course
    Then I should see "SUMMARY:C4 event"

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
