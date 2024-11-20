@tool @tool_usertours
Feature: Prevent yours from being marked as complete
    In order to impart key information
    As an administrator
    I can prevent a user tour from being marked as complete

  Background:
    Given I log in as "admin"
    And I add a new user tour with:
      | Name               | First tour    |
      | Description        | My first tour |
      | Apply to URL match | FRONTPAGE     |
      | Tour is enabled    | 1             |
      | Show with backdrop | 1             |
      # 2 = tour::SHOW_TOUR_ON_EACH_PAGE_VISIT
      | Show tour | 2 |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content    | Content type |
      | Display in middle of page | Welcome | Welcome tour. | Manual       |

  @javascript
  Scenario: Ending the tour should not mark it as complete
    # Changing the window viewport to mobile so we will have the footer section.
    Given I am on site homepage
    And I should see "Welcome"
    And I press "Got it"
    And I should not see "Welcome"
    When I am on site homepage
    Then I should see "Welcome"
