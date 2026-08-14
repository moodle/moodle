@theme_boost
Feature: Light and dark colour modes
  In order to read the site comfortably
  As a user
  I need to be able to switch Boost between a light and a dark colour scheme

  Background:
    Given the following config values are set as admin:
      | enablecolourmodes | 1 | theme_boost |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |

  @javascript
  Scenario: A user can switch the site to dark mode
    # Pinned with a preference rather than left to the site default, so that the scenario still starts from light
    # when the whole run is started with --colourmode=dark.
    Given the following "user preferences" exist:
      | user     | preference             | value |
      | student1 | theme_boost_colourmode | light |
    And I log in as "student1"
    And the "data-bs-theme" attribute of "html" "css_element" should contain "light"
    When I click on "Colour mode: Light" "button"
    And I click on "Dark" "button" in the "#colourmode-action-menu" "css_element"
    Then the "data-bs-theme" attribute of "html" "css_element" should contain "dark"
    And the "data-colourmode" attribute of "html" "css_element" should contain "dark"
    # The choice is remembered on the next page load.
    And I am on site homepage
    And the "data-bs-theme" attribute of "html" "css_element" should contain "dark"

  @javascript
  Scenario: A user can switch back to light mode
    Given the following "user preferences" exist:
      | user     | preference             | value |
      | student1 | theme_boost_colourmode | dark  |
    And I log in as "student1"
    And the "data-bs-theme" attribute of "html" "css_element" should contain "dark"
    When I click on "Colour mode: Dark" "button"
    And I click on "Light" "button" in the "#colourmode-action-menu" "css_element"
    Then the "data-bs-theme" attribute of "html" "css_element" should contain "light"
    And the "data-colourmode" attribute of "html" "css_element" should contain "light"

  @javascript
  Scenario: The chosen colour mode is kept on the pages seen after logging out
    # A run started with --colourmode renders every logged out page in that mode, so it cannot exercise this.
    Given the run is not using a colour mode
    And the following config values are set as admin:
      | defaultcolourmode | light | theme_boost |
    And the following "user preferences" exist:
      | user     | preference             | value |
      | student1 | theme_boost_colourmode | dark  |
    And I log in as "student1"
    And the "data-bs-theme" attribute of "html" "css_element" should contain "dark"
    When I log out
    Then the "data-bs-theme" attribute of "html" "css_element" should contain "dark"
    # And again, because logging in a second time is what used to lose it: core clears the whole of local storage
    # when it sees a new login, which took the copy with it.
    And I log in as "student1"
    And I log out
    And the "data-bs-theme" attribute of "html" "css_element" should contain "dark"

  Scenario: The colour mode switcher is hidden when colour modes are turned off
    # A run started with --colourmode turns colour modes on for the whole run, so it cannot exercise them turned off.
    Given the run is not using a colour mode
    And the following config values are set as admin:
      | enablecolourmodes | 0    | theme_boost |
      | defaultcolourmode | dark | theme_boost |
    And the following "user preferences" exist:
      | user     | preference             | value |
      | student1 | theme_boost_colourmode | dark  |
    When I log in as "student1"
    Then "Colour mode" "button" should not exist
    And the "data-bs-theme" attribute of "html" "css_element" should not be set
