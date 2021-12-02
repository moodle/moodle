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
    And I should see "<homepage>" in the "//a[contains(@class,'nav-link active') and contains(., '<homepage>')]" "xpath_element"
    And I should see "<sitehome>" in the "<linkelement>" "xpath_element"

    Examples:
      | defaulthomepageset | homepage    |  sitehome  |   linkelement                                                                                               |
      |   0                | Home        |   Home     |  //a[contains(@class, 'nav-link active') and contains(@tabindex, 0) and not(contains(@href, 'redirect=0'))] |
      |   1                | Dashboard   |   Home     |  //a[contains(@class, 'nav-link') and contains(@tabindex, 0) and (contains(@href, 'redirect=0'))]          |
      |   3                | My courses  |   Home     |  //a[contains(@class, 'nav-link') and contains(@tabindex, 0) and (contains(@href, 'redirect=0'))]          |

  @javascript @theme_boost
  Scenario Outline: Admin sets defaulthomepage to user preference and verifies the landing page based on it
    Given I log in as "admin"
    And I navigate to "Appearance > Navigation" in site administration
    And I set the field "Home page for users" to "User preference"
    And I press "Save changes"
    And I follow "Preferences" in the user menu
    And I follow "Home page"
    And I set the field "Home page" to "<userpreference>"
    And I press "Save changes"
    And the following config values are set as admin:
      | defaulthomepage | 2 |
    And I log out
    And I log in as "admin"
    And I should see "<homepage>" in the "//a[contains(@class,'nav-link active') and contains(., '<homepage>')]" "xpath_element"

    Examples:
      | userpreference | homepage    |
      |   Site         | Home        |
      |   Dashboard    | Dashboard   |
      |   My courses   | My courses  |
