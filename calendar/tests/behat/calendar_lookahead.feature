@core @core_calendar
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
    And I log in as "teacher1"

  Scenario: I view calendar details for a future event
    Given I follow "C1"
    And I turn editing mode on
    And I add the "Calendar" block
    And I add the "Upcoming events" block
    And I follow "This month"
    And I click on "a.next" "css_element"
    And I click on "a.next" "css_element"
    And I create a calendar event:
      | Type of event     | course |
      | Event title       | Two months away event |
    When I follow "C1"
    Then I should not see "Two months away event"
    And I am on site homepage
    And I follow "Preferences" in the user menu
    And I follow "Calendar preferences"
    And I set the following fields to these values:
      | Upcoming events look-ahead | 3 months |
    And I press "Save changes"
    And I wait to be redirected
    And I am on site homepage
    And I follow "Course 1"
    And I should see "Two months away event"
