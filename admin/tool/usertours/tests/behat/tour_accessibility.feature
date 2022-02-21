@tool @tool_usertours
Feature: Apply accessibility to a tour
  Background:
    Given I log in as "admin"
    And I add a new user tour with:
      | Name                | First tour |
      | Description         | My first tour |
      | Apply to URL match  | FRONTPAGE |
      | Tour is enabled     | 1 |
      | Show with backdrop  | 1 |
    And I add steps to the "First tour" tour:
      | targettype                | Title   | id_content    | Content type   |
      | Display in middle of page | Welcome | Welcome tour. | Manual |
    And I add steps to the tour:
      | targettype | targetvalue_selector | Title     | id_content | Content type   |
      | Selector   | .usermenu            | User menu | Next page  | Manual |
      | Selector   | .navbar-brand        | Page 2    | Next page  | Manual |
    And I add steps to the tour:
      | targettype                | Title  | id_content  | Content type   |
      | Display in middle of page | Page 3 | Final page. | Manual |

  @javascript
  Scenario: Check tabbing working correctly.
    Given I am on site homepage
    And I wait "1" seconds
    And I should see "Welcome"
    # First dialogue of the tour, "Welcome". It has Next and End buttons.
    # Nothing highlighted on the page. Initially whole dialogue focused.
    When I press tab
    Then the focused element is "Next" "button" in the "Welcome" "dialogue"
    When I press tab
    Then the focused element is "Skip tour" "button" in the "Welcome" "dialogue"
    When I press tab
    # Here the focus loops round to the whole dialogue again.
    And I press tab
    Then the focused element is "Next" "button" in the "Welcome" "dialogue"
    # Check looping works properly going backwards too.
    When I press shift tab
    And I press shift tab
    Then the focused element is "Skip tour" "button" in the "Welcome" "dialogue"

    When I press "Next"
    # Now we are on the "User menu" step, so Previous is also enabled.
    # Also, the user menu section in the page is highlighted, and this
    # section contain a hyperlink so the focus have to go though and back to the dialogue.
    And I wait "1" seconds
    And I press tab
    Then the focused element is "Next" "button" in the "User menu" "dialogue"
    When I press tab
    Then the focused element is "Skip tour" "button" in the "User menu" "dialogue"
    # We tab 3 times from "Skip Tour" button to header container, drop down then go to "Dashboard" link.
    When I press tab
    Then the focused element is ".usermenu" "css_element"
    When I press tab
    Then the focused element is "User menu" "button" in the ".usermenu" "css_element"
    When I press tab
    And I press tab
    Then the focused element is "Next" "button" in the "User menu" "dialogue"
    # Press shift-tab twice should lead us back to the user menu button.
    When I press shift tab
    And I press shift tab
    Then the focused element is "User menu" "button" in the ".usermenu" "css_element"

  @javascript
  Scenario: Aria tags should not exist
    And I am on site homepage
    When I click on "Next" "button"
    And I click on "Next" "button"
    Then ".navbar-brand[aria-describedby^='tour-step-tool_usertours']" "css_element" should exist
    And ".navbar-brand[tabindex]" "css_element" should exist
    When I click on "Next" "button"
    Then ".navbar-brand[aria-describedby^='tour-step-tool_usertours']" "css_element" should not exist
    And ".navbar-brand[tabindex]:not([tabindex='-1'])" "css_element" should not exist
    When I click on "End tour" "button"
    Then ".navbar-brand[aria-describedby^='tour-step-tool_usertours']" "css_element" should not exist
    And ".navbar-brand[tabindex]:not([tabindex='0'])" "css_element" should not exist
