@tool @tool_usertours
Feature: Steps can be navigated within a tour
  In order to use a tour effectively
  As a user
  I can navigate its steps

  @javascript
  Scenario: Clicking on items in the page should not end the tour
    Given I log in as "admin"
    And I add a new user tour with:
      | Name                | Calendar tour |
      | Description         | Calendar tour |
      | Apply to URL match  | /my/% |
      | Tour is enabled     | 1 |
    And I add steps to the "Calendar tour" tour:
      | targettype   | Block        | Title             | Content |
      | Block        | Calendar     | Calendar events   | This is the calendar block |
    And I change window size to "large"
    And I follow "Dashboard" in the user menu
    And I wait until the page is ready
    And I should see "This is the calendar block"
    When I click on ".block_calendar_month .calendar-controls .next" "css_element"
    And I wait until the page is ready
    Then I should see "Calendar events"
