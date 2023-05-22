@core @core_calendar @javascript
Feature: Limit displayed upcoming events
  In order to filter what is displayed on the calendar
  As a user
  I need to interact with the calendar

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
    And the following "blocks" exist:
      | blockname         | contextlevel | reference | pagetypepattern | defaultregion |
      | calendar_month    | Course       | C1        | course-view-*   | side-pre      |
      | calendar_upcoming | Course       | C1        | course-view-*   | side-pre      |
    And I log in as "teacher1"

  Scenario: I view calendar details for a future event
    Given I am on "Course 1" course homepage with editing mode on
    And I follow "Full calendar"
    And I click on "a.next" "css_element"
    And I click on "a.next" "css_element"
    When I create a calendar event:
      | Type of event     | course |
      | Course            | Course 1 |
      | Event title       | Two months away event |
    And I am on "Course 1" course homepage
    Then I should not see "Two months away event"
    And I am on site homepage
    And I follow "Preferences" in the user menu
    And I follow "Calendar preferences"
    When I set the following fields to these values:
      | Upcoming events look-ahead | 3 months |
    And I press "Save changes"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    Then I should see "Two months away event"
