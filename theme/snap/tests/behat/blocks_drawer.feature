@theme @theme_snap
Feature: Testing blocks_drawer in theme_snap

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |

  @javascript
  Scenario: The blocks drawer can be opened, closed and see the Navigation block
    Given I am logged in as "teacher1"
    And I am on site homepage
    Then "button[data-original-title='Toggle block drawer']" "css_element" should exist
    And I should not see "Dashboard" in the "Navigation" "block"
    And I click on "button[data-original-title='Toggle block drawer']" "css_element"
    And I should see "Dashboard" in the "Navigation" "block"

  @javascript
  Scenario: The drawers from snap feeds, settings block, and block drawer should open one at a time
    Given I am logged in as "admin"
    And the following config values are set as admin:
      | linkadmincategories | 0 |
    And I am on site homepage
    And "section.block_settings.state-visible" "css_element" should not exist
    And I click on "#admin-menu-trigger" "css_element"
    And ".drawer.show" "css_element" should not exist
    And "section.block_settings.state-visible" "css_element" should exist
    And I should see "Site administration" in the "#settingsnav" "css_element"
    Then I click on "#snap_feeds_side_menu_trigger" "css_element"
    And "#snap_feeds_side_menu" "css_element" should exist
    And ".drawer.show" "css_element" should not exist
    And "section.block_settings.state-visible" "css_element" should not exist
    Then I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I click on "button[data-original-title='Toggle block drawer']" "css_element"
    And I should see "Dashboard" in the "Navigation" "block"