@block @block_site_main_menu
Feature: Edit activities in main menu block
  In order to use main menu block
  As an admin
  I need to add and edit activities there

  @javascript
  Scenario: Edit name of acitivity in-place in site main menu block
    Given the following "activity" exists:
      | activity | forum                |
      | course   | Acceptance test site |
      | name     | My forum name        |
      | idnumber | forum                |
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | site_main_menu | System       | 1         | site-index      | side-pre      |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    When I set the field "Edit title" in the "My forum name" "block_site_main_menu > Activity" to "New forum name"
    Then I should not see "My forum name"
    And I should see "New forum name"
    And I follow "New forum name"
    And I should not see "My forum name"
    And I should see "New forum name"

  @javascript
  Scenario: Activities in main menu block can be made available but not visible on a course page
    Given the following config values are set as admin:
      | allowstealth | 1 |
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | site_main_menu | System       | 1         | site-index      | side-post     |
    And the following "activities" exist:
      | activity | course               | section | name          |
      | forum    | Acceptance test site | 0       | Visible forum |
      | forum    | Acceptance test site | 0       | My forum name |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And "My forum name" activity in site main menu block should have "Hide" editing icon
    And "My forum name" activity in site main menu block should not have "Show" editing icon
    And "My forum name" activity in site main menu block should not have "Make available" editing icon
    And "My forum name" activity in site main menu block should not have "Make unavailable" editing icon
    And I open "My forum name" actions menu in site main menu block
    And I click on "Hide" "link" in the "My forum name" activity in site main menu block
    And "My forum name" activity in site main menu block should be hidden
    And "My forum name" activity in site main menu block should not have "Hide" editing icon
    And "My forum name" activity in site main menu block should have "Show" editing icon
    And "My forum name" activity in site main menu block should have "Make available" editing icon
    And "My forum name" activity in site main menu block should not have "Make unavailable" editing icon
    And I open "My forum name" actions menu in site main menu block
    And I click on "Make available" "link" in the "My forum name" activity in site main menu block
    And "My forum name" activity in site main menu block should be available but hidden from course page
    And "My forum name" activity in site main menu block should not have "Hide" editing icon
    And "My forum name" activity in site main menu block should have "Show" editing icon
    And "My forum name" activity in site main menu block should not have "Make available" editing icon
    And "My forum name" activity in site main menu block should have "Make unavailable" editing icon
    # Make sure that "Availability" dropdown in the edit menu has three options.
    And I open "My forum name" actions menu in site main menu block
    And I click on "Edit settings" "link" in the "My forum name" activity in site main menu block
    And I expand all fieldsets
    And the "Availability" select box should contain "Show on course page"
    And the "Availability" select box should contain "Hide on course page"
    And the field "Availability" matches value "Make available but don't show on course page"
    And I press "Save and return to course"
    And "My forum name" activity in site main menu block should be available but hidden from course page
    And I turn editing mode off
    And "My forum name" activity in site main menu block should be available but hidden from course page
    And I log out
    And I should not see "My forum name" in the "Main menu" "block"
    And I should see "Visible forum" in the "Main menu" "block"
