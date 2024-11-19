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
    When I set the field "Edit title" in the "My forum name" "activity" to "New forum name"
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
      | site_main_menu | System       | 1         | site-index      | side-pre      |
    And the following "activities" exist:
      | activity | course               | section | name          |
      | forum    | Acceptance test site | 0       | Visible forum |
      | forum    | Acceptance test site | 0       | My forum name |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    When I open "My forum name" actions menu
    And I choose "Availability > Make available but don't show on course page" in the open action menu
    Then I should see "Available but not shown on course page" in the "My forum name" "core_courseformat > Activity visibility"
    # Make sure that "Availability" dropdown in the edit menu has three options.
    And I open "My forum name" actions menu
    And I choose "Edit settings" in the open action menu
    And I expand all fieldsets
    And the "Availability" select box should contain "Show on course page"
    And the "Availability" select box should contain "Hide on course page"
    And the field "Availability" matches value "Make available but don't show on course page"
    And I press "Save and return to course"
    And I should see "Available but not shown on course page" in the "My forum name" "core_courseformat > Activity visibility"
    And I turn editing mode off
    And I should see "Available but not shown on course page" in the "My forum name" "core_courseformat > Activity visibility"
    And I log out
    And I should not see "My forum name" in the "Main menu" "block"
    And I should see "Visible forum" in the "Main menu" "block"

  @javascript
  Scenario: The move activity modal allow to move from the main menu block to the main content
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
    And I should see "My forum name" in the "block_site_main_menu_section" "region"
    And I should not see "My forum name" in the "region-main" "region"
    When I open "My forum name" actions menu
    And I click on "Move" "link" in the "My forum name" activity
    And I should see "My forum name" in the "Move activity" "dialogue"
    And I should see "Block" in the "Move activity" "dialogue"
    And I should see "Site" in the "Move activity" "dialogue"
    And I click on "Site" "link" in the "Move activity" "dialogue"
    Then I should see "My forum name" in the "region-main" "region"
    And I should not see "My forum name" in the "block_site_main_menu_section" "region"

  @javascript
  Scenario: The move activity modal allow to move from the main content to the main menu block
    Given the following "activity" exists:
      | activity | forum                |
      | course   | Acceptance test site |
      | name     | My forum name        |
      | idnumber | forum                |
      | section  | 1                    |
    And the following "blocks" exist:
      | blockname      | contextlevel | reference | pagetypepattern | defaultregion |
      | site_main_menu | System       | 1         | site-index      | side-pre      |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I should not see "My forum name" in the "block_site_main_menu_section" "region"
    And I should see "My forum name" in the "region-main" "region"
    When I open "My forum name" actions menu
    And I click on "Move" "link" in the "My forum name" activity
    And I should see "My forum name" in the "Move activity" "dialogue"
    And I should see "Block" in the "Move activity" "dialogue"
    And I should see "Site" in the "Move activity" "dialogue"
    And I click on "Block" "link" in the "Move activity" "dialogue"
    Then I should not see "My forum name" in the "region-main" "region"
    And I should see "My forum name" in the "block_site_main_menu_section" "region"

  @javascript
  Scenario: Admin can delete an activity in the main menu block
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
    And I should see "My forum name" in the "block_site_main_menu_section" "region"
    When I open "My forum name" actions menu
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete activity?" "dialogue"
    Then I should not see "My forum name" in the "block_site_main_menu_section" "region"

  @javascript
  Scenario: Admin can duplicate an activity in the main menu block
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
    And I should see "My forum name" in the "block_site_main_menu_section" "region"
    When I open "My forum name" actions menu
    And I choose "Duplicate" in the open action menu
    Then I should see "My forum name (copy)" in the "block_site_main_menu_section" "region"

  @javascript
  Scenario: Admin can move right and left an activity in the main menu block
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
    And I should see "My forum name" in the "block_site_main_menu_section" "region"
    When I open "My forum name" actions menu
    And "Move right" "link" should be visible
    And "Move left" "link" should not be visible
    And I choose "Move right" in the open action menu
    Then I open "My forum name" actions menu
    And "Move right" "link" should not be visible
    And "Move left" "link" should be visible
    And I choose "Move left" in the open action menu
    And I open "My forum name" actions menu
    And "Move right" "link" should be visible
    And "Move left" "link" should not be visible
