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
      |   Home         | Home        |
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

  Scenario: Dashboard is not displayed in the primary navigation when it is disabled
    Given the following config values are set as admin:
      | enabledashboard | 0 |
    When I am on the "My courses" page logged in as "user1"
    Then I should not see "Dashboard"
    And the following config values are set as admin:
      | enabledashboard | 1 |
# We need to reload the page to skip the "Welcome, xxxx!" and display the real page title.
    And I reload the page
    And I should see "Dashboard"

  Scenario: Start page when default home is dashboard but dashboard is disabled
    Given the following config values are set as admin:
      | enabledashboard | 0 |
# 1 = Dashboard.
      | defaulthomepage | 1 |
    When I log in as "admin"
# We need to reload the page to skip the "Welcome, xxxx!" and display the real page title.
    And I reload the page
    Then I should not see "Dashboard" in the "page-header" "region"
    And I should see "My courses" in the "page-header" "region"
    And I log out
# Check dashboard is displayed when it's re-enabled.
    And the following config values are set as admin:
      | enabledashboard | 1 |
    And I log in as "admin"
# We need to reload the page to skip the "Welcome, xxxx!" and display the real page title.
    And I reload the page
    And I should see "Dashboard" in the "page-header" "region"
    And I should not see "My courses" in the "page-header" "region"

  Scenario: Start page when default home is user preference set to dashboard but dashboard is disabled
    Given the following config values are set as admin:
      | enabledashboard | 0 |
# 2 = User preference.
      | defaulthomepage | 2 |
# 1 = Dashboard.
    And the following "user preferences" exist:
      | user      | preference                       | value |
      | admin     | user_home_page_preference        | 1     |
    When I log in as "admin"
# We need to reload the page to skip the "Welcome, xxxx!" and display the real page title.
    And I reload the page
    Then I should not see "Dashboard"
    And I should see "My courses" in the "page-header" "region"
    And I log out
# Check dashboard is displayed when it's re-enabled.
    And the following config values are set as admin:
      | enabledashboard | 1 |
    And I log in as "admin"
# We need to reload the page to skip the "Welcome, xxxx!" and display the real page title.
    And I reload the page
    And I should see "Dashboard" in the "page-header" "region"
    And I should not see "My courses" in the "page-header" "region"
