@tool @tool_usertours @theme_iomad
Feature: Reset a tour for IOMAD
  In order to test a tour
  As an administrator
  I can reset the tour to force it to display again

  Background:
    Given I log in as "admin"
    And I add a new user tour with:
      | Name               | First tour    |
      | Description        | My first tour |
      | Apply to URL match | FRONTPAGE     |
      | Tour is enabled    | 1             |
      | Show with backdrop | 1             |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content    | Content type   |
      | Display in middle of page | Welcome | Welcome tour. | Manual         |

  @javascript
  Scenario: Reset the tour with desktop view
    # Changing the window size to large so we will have the footer button.
    Given I change window size to "large"
    And I am on site homepage
    And I should see "Welcome"
    And I press "Got it"
    And I should not see "Welcome"
    When I click on ".btn-footer-popover" "css_element" in the "#page-footer" "css_element"
    Then I should see "Reset user tour on this page"
    And I click on "Reset user tour on this page" "link"
    And I should see "Welcome"
