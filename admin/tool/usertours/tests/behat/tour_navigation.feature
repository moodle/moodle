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

  @javascript
  Scenario: End tour button text for one step tours
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
    Then I should see "Got it!"

  @javascript
  Scenario: End tour button text for multiple step tours
    Given I log in as "admin"
    And I add a new user tour with:
      | Name                | First tour |
      | Description         | My first tour |
      | Apply to URL match  | /my/% |
      | Tour is enabled     | 1 |
    And I add steps to the "First tour" tour:
      | targettype                  | Title             | Content |
      | Display in middle of page   | Welcome           | Welcome to your personal learning space. We'd like to give you a quick tour to show you some of the areas you may find helpful |
    And I add steps to the "First tour" tour:
      | targettype                  | targetvalue_block | Title             | Content |
      | Block                       | Course overview   | Course overview   | This area shows you what's happening in some of your courses  |
      | Block                       | Calendar          | Calendar          | This is the Calendar. All of your assignments and due dates can be found here |
    When I am on homepage
    Then I should see "End tour"
    And I should see "Next"
    And I click on "Next" "button"
    And I should see "End tour"
