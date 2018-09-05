@core
Feature: Navigate action menu
  In order to navigate an action menu
  As a user
  I need to be able to use the keyboard

  @javascript
  Scenario: The menu does not close on keyboard navigation
    When I log in as "admin"
    # Click to open the user menu.
    And I click on ".usermenu a.toggle-display" "css_element" in the ".usermenu" "css_element"
    # The menu should now be visible.
    Then ".usermenu [role='menu']" "css_element" should be visible
    # Press down arrow.
    And I press key "40" in "#actionmenuaction-1" "css_element"
    # The menu should still be visible.
    And ".usermenu [role='menu']" "css_element" should be visible

  @javascript
  Scenario: The menu closes when it clicked outside
    When I log in as "admin"
    # Click to open the user menu.
    And I click on ".usermenu a.toggle-display" "css_element" in the ".usermenu" "css_element"
    # The menu should now be visible.
    Then ".usermenu [role='menu']" "css_element" should be visible
    # Click outside the menu.
    And I click on "adminsearchquery" "field"
    # The menu should now be hidden.
    And ".usermenu [role='menu']" "css_element" should not be visible
