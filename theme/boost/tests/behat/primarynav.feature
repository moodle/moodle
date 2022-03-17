@javascript @theme_boost
Feature: Primary navigation
  To navigate in boost theme
  As a user
  I need to use the primary navigation

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |

  @javascript @theme_boost
  Scenario Outline: Admin sets defaulthomepage and verify the landing page and site home link
    Given I log in as "admin"
    And the following config values are set as admin:
      | defaulthomepage | <defaulthomepageset> |
    And I am on homepage
    And I should see "<homepage>" in the "a.nav-link.active:not([tabindex])" "css_element"
    And I should see "<sitehome>" in the "<linkelement>" "css_element"

    Examples:
      | defaulthomepageset | homepage    |  sitehome  |  linkelement                                                  |
      |   0                | Home        |   Home     |  a.nav-link.active:not([tabindex]):not([href*='redirect=0'])  |
      |   1                | Dashboard   |   Home     |  a.nav-link[tabindex='-1'][href$='redirect=0']                |
      |   3                | My courses  |   Home     |  a.nav-link[tabindex='-1'][href$='redirect=0']                |

  @javascript @theme_boost
  Scenario Outline: Admin sets defaulthomepage to user preference and verifies the landing page based on it
    Given I log in as "admin"
    And I navigate to "Appearance > Navigation" in site administration
    And I set the field "Start page for users" to "User preference"
    And I press "Save changes"
    And I follow "Preferences" in the user menu
    And I follow "Start page"
    And I set the field "Start page" to "<userpreference>"
    And I press "Save changes"
    And the following config values are set as admin:
      | defaulthomepage | 2 |
    And I log out
    And I log in as "admin"
    And I should see "<homepage>" in the "a.nav-link.active:not([tabindex])" "css_element"

    Examples:
      | userpreference | homepage    |
      |   Site         | Home        |
      |   Dashboard    | Dashboard   |
      |   My courses   | My courses  |

  @javascript @theme_boost
  Scenario: Users could use primary nav menu on mobile size screens
    Given I change window size to "mobile"
    And I am on the "My courses" page logged in as "user1"
    Then "Home" "link" should not be visible
    And "Side panel" "button" should exist
    And I click on "Side panel" "button"
    And I should see "Home" in the "theme_boost-drawers-primary" "region"

  @theme_boost
  Scenario: Guest users can only see the Home item in the primary navigation menu
    Given I log in as "guest"
    When I am on site homepage
    Then I should see "Home" in the ".primary-navigation" "css_element"
    And I should not see "Dashboard" in the ".primary-navigation" "css_element"
    And I should not see "My courses" in the ".primary-navigation" "css_element"
    And I should not see "Site administration" in the ".primary-navigation" "css_element"
