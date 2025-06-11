@theme @theme_snap @theme_snap_sidebar_menu
Feature: Testing sidebarmenu in theme_snap

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |

  @javascript
  Scenario: The sidebar menu should be opened by default
    Given I am logged in as "admin"
    And I am on site homepage
    And ".snap-sidebar-menu.show" "css_element" should exist

  @javascript
  Scenario: The sidebar menu should be opened and closed by clicking on the trigger button
    Given I am logged in as "admin"
    And I am on site homepage
    And ".snap-sidebar-menu.show" "css_element" should exist
    And I click on ".snap-sidebar-menu-trigger" "css_element"
    And ".snap-sidebar-menu.show" "css_element" should not exist
    And ".snap-sidebar-menu" "css_element" should exist

  @javascript
  Scenario: The drawers in the sidebar menu should close when a header dropdown/popover is opened
    Given I am logged in as "admin"
    And I am on site homepage
    And ".snap-sidebar-menu.show" "css_element" should exist
    And I click on "#admin-menu-trigger" "css_element"
    And ".block_settings.state-visible" "css_element" should exist
    Then I click on ".usermenu .dropdown-toggle" "css_element"
    And ".block_settings.state-visible" "css_element" should not exist
    And I click on "button[data-original-title='Toggle block drawer']" "css_element"
    And ".drawer.show" "css_element" should exist
    And I click on "#nav-intellicart-popover-container" "css_element"
    And ".drawer.show" "css_element" should not exist
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And "#snap_feeds_side_menu.state-visible" "css_element" should exist
    And I click on "#nav-notification-popover-container" "css_element"
    And "#snap_feeds_side_menu.state-visible" "css_element" should not exist