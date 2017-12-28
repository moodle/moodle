@tool @tool_usertours
Feature: Apply accessibility to a tour

  @javascript
  Scenario: Check tabbing working correctly.
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I log in as "admin"
    And I open the User tour settings page
    And I click on "Enable" "link" in the "Boost - course view" "table_row"
    And I am on "Course 1" course homepage
    # First dialogue of the tour, "Welcome". It has Close, Next and End buttons.
    # Nothing highlighted on the page. Initially whole dialogue focused.
    And I wait "1" seconds
    When I press tab
    Then the focused element is ".close" "css_element" in the "Welcome" "dialogue"
    When I press tab
    Then the focused element is "Next" "button" in the "Welcome" "dialogue"
    When I press tab
    Then the focused element is "End tour" "button" in the "Welcome" "dialogue"
    When I press tab
    # Here the focus loops round to the whole dialogue again.
    And I press tab
    Then the focused element is ".close" "css_element" in the "Welcome" "dialogue"
    # Check looping works properly going backwards too.
    When I press shift tab
    And I press shift tab
    Then the focused element is "End tour" "button" in the "Welcome" "dialogue"

    When I press "Next"
    # Now we are on the "Customisation" step, so Previous is also enabled.
    # Also, the "Course Header" section in the page is highlighted, and this
    # section contain breadcrumb Dashboard / Course 1 / C1 and setting drop down,
    # so the focus have to go though them and back to the dialogue.
    And I wait "1" seconds
    And I press tab
    Then the focused element is ".close" "css_element" in the "Customisation" "dialogue"
    When I press tab
    Then the focused element is "Previous" "button" in the "Customisation" "dialogue"
    When I press tab
    Then the focused element is "Next" "button" in the "Customisation" "dialogue"
    When I press tab
    Then the focused element is "End tour" "button" in the "Customisation" "dialogue"
    # We tab 3 times from "End Tour" button to header container, drop down then go to "Dashboard" link.
    When I press tab
    And I press tab
    And I press tab
    Then the focused element is "Dashboard" "link" in the ".breadcrumb" "css_element"
    When I press tab
    Then the focused element is "Courses" "link"
    When I press tab
    Then the focused element is "C1" "link"
    # Standing at final element of "Course Header" section, tab twice will lead our focus back to
    # whole dialog then to close button on dialog header.
    When I press tab
    And I press tab
    Then the focused element is ".close" "css_element" in the "Customisation" "dialogue"
    # Press shift-tab twice should lead us back to "C1" link.
    When I press shift tab
    And I press shift tab
    Then the focused element is "C1" "link"

    When I press "Next"
    # Now we are on the "Navigation" step, so Previous is also enabled.
    # Also, the "Side panel" button in the page is highlighted, and this comes
    # in the tab order after End buttons, and before focus loops back to the popup.
    And I wait "1" seconds
    And I press tab
    Then the focused element is ".close" "css_element" in the "Navigation" "dialogue"
    When I press tab
    Then the focused element is "Previous" "button" in the "Navigation" "dialogue"
    When I press tab
    Then the focused element is "Next" "button" in the "Navigation" "dialogue"
    When I press tab
    Then the focused element is "End tour" "button" in the "Navigation" "dialogue"
    When I press tab
    Then the focused element is "Side panel" "button"
    When I press tab
    # Here the focus loops round to the whole dialogue again.
    And I press tab
    Then the focused element is ".close" "css_element" in the "Navigation" "dialogue"
    When I press shift tab
    And I press shift tab
    Then the focused element is "Side panel" "button"
    When I press shift tab
    And the focused element is "End tour" "button" in the "Navigation" "dialogue"
