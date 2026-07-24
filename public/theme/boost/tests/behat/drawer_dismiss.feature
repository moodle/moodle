@theme @theme_boost
Feature: Drawers on small screens are dismissed by interaction outside them
  In order to keep the navbar usable while a drawer is open on small screens
  As a user
  I need any interaction outside the drawer, by mouse or keyboard, to close it

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "course" exists:
      | fullname     | Course 1 |
      | shortname    | C1       |
      | numsections  | 3        |
      | initsections | 1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "blocks" exist:
      | blockname    | contextlevel | reference | pagetypepattern | defaultregion |
      | online_users | Course       | C1        | course-view-*   | side-pre      |
    And I change window size to "mobile"

  @javascript
  Scenario: Interacting with the navbar closes the primary drawer
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I click on "Side panel" "button"
    And "theme_boost-drawers-primary" "region" should be visible
    When I click on "User menu" "button" in the "usernavigation" "region"
    Then "theme_boost-drawers-primary" "region" should not be visible

  @javascript
  Scenario: Keyboard focus leaving the primary drawer closes it
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I click on "Side panel" "button"
    And the focused element is "Close drawer" "button"
    And I press the shift tab key
    And the focused element is ".drawer-primary [data-region='site-home-link']" "css_element"
    # Tab out of the drawer
    When I press the shift tab key
    Then "theme_boost-drawers-primary" "region" should not be visible

  @javascript
  Scenario: Interacting with the navbar closes the course index drawer
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I click on "Open course index" "button"
    And "theme_boost-drawers-courseindex" "region" should be visible
    When I click on "User menu" "button" in the "usernavigation" "region"
    Then "theme_boost-drawers-courseindex" "region" should not be visible

  @javascript
  Scenario: Keyboard focus leaving the course index drawer closes it
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I click on "Open course index" "button"
    And the focused element is "Close course index" "button"
    And I press the shift tab key
    And I press the shift tab key
    And the focused element is "#theme_boost-drawers-courseindex .courseindex-link" "css_element"
    # Tab out of the drawer
    When I press the shift tab key
    Then "theme_boost-drawers-courseindex" "region" should not be visible

  @javascript
  Scenario: Interacting with the navbar closes the block drawer
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I click on "Open block drawer" "button"
    And "theme_boost-drawers-blocks" "region" should be visible
    When I click on "User menu" "button" in the "usernavigation" "region"
    Then "theme_boost-drawers-blocks" "region" should not be visible

  @javascript
  Scenario: Keyboard focus leaving the block drawer closes it
    Given I am on the "C1" "Course" page logged in as "teacher1"
    And I click on "Open block drawer" "button"
    And the focused element is "Close block drawer" "button"
    # Tab out of the drawer
    When I press the shift tab key
    Then "theme_boost-drawers-blocks" "region" should not be visible
