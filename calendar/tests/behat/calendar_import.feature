@core @core_calendar @_file_upload @javascript
Feature: Import and edit calendar events
  In order to manipulate imported calendar events
  As an user
  I need to import calendar events then edit them.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Import then edit a calendar event.
    Given I log in as "teacher1"
    And I view the calendar for "1" "2016"
    And I click on "Import or export calendars" "link"
    And I press "Import calendar"
    And I set the following fields to these values:
      | Calendar name  | Test Import |
      | Import from    | Calendar file (.ics) |
      | Type of event  | User |
    And I upload "calendar/tests/fixtures/import.ics" file to "Calendar file (.ics)" filemanager
    And I press "Import calendar"
    And I should see "2 events were imported"
    And I view the calendar for "2" "2017"
    And I should see "February 2017"
    And I should see "Event on 2-15-2017"
    And I should see "Event on 2-25-2017"
    And I click on "Event on 2-15-2017" "link"
    And I should see "Some place"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | Event title    | Event on 2-20-2017 |
      | Description    | Event on 2-20-2017 |
      | timestart[day] | 20 |
    And I press "Save"
    When I view the calendar for "2" "2017"
    Then I should see "Event on 2-20-2017"
    And I should see "Event on 2-25-2017"
    And I should not see "Event on 2-15-2017"
    And I click on "Import or export calendars" "link"
    And I click on "Delete" "link"
    And I should see "Are you sure you want to delete the \"Test Import\" calendar subscription?" in the ".modal .modal-body" "css_element"
    And I click on "Yes" "button" in the ".modal.show" "css_element"
    And I view the calendar for "2" "2017"
    And I should not see "Event on 2-25-2017"
    And I should not see "Event on 2-20-2017"

  Scenario: Import events using different event types.
    Given I log in as "admin"
    And I view the calendar for "1" "2016"
    And I click on "Import or export calendars" "link"
    And I press "Import calendar"
    And I set the following fields to these values:
      | Calendar name  | Test Import |
      | Import from    | Calendar file (.ics) |
      | Type of event  | User |
    And I upload "calendar/tests/fixtures/import.ics" file to "Calendar file (.ics)" filemanager
    And I press "Import calendar"
    And I should see "User events"
    And I press "Import calendar"
    And I set the following fields to these values:
      | Calendar name  | Test Import |
      | Import from    | Calendar file (.ics) |
      | Type of event  | Category             |
      | Category       | Category 1           |
    And I upload "calendar/tests/fixtures/import.ics" file to "Calendar file (.ics)" filemanager
    And I press "Import calendar"
    And I should see "Category events"
    And I press "Import calendar"
    And I set the following fields to these values:
      | Calendar name  | Test Import |
      | Import from    | Calendar file (.ics) |
      | Type of event  | Site             |
    And I upload "calendar/tests/fixtures/import.ics" file to "Calendar file (.ics)" filemanager
    And I press "Import calendar"
    And I should see "Site events"
