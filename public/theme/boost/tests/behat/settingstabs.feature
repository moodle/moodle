@javascript @theme_boost
Feature: Administration nav tabs

  Scenario: See last opened tab in site admin when returning to the page
    Given I log in as "admin"
    And I am on site homepage
    And I click on "Site administration" "link"
    And I click on "Users" "link"
    And I click on "Browse list of users" "link"
    And I should see "Add a new user"
    When I press the "back" button in the browser
    Then I should see "Cohorts"

  Scenario: Navigate back to specific tab after search
    Given I log in as "admin"
    And I am on site homepage
    And I click on "Site administration" "link"
    And I set the field "Search" to "assignment"
    And I press "Search"
    # I should be redirected to the site admin tab with the complete list under it.
    # Testing the existence of at least one of the options in the node is sufficient.
    When I select "Users" from secondary navigation
    Then I should see "Browse list of users"

  Scenario: Selecting an admin settings page tab outside the secondary navigation still updates the URL anchor
    Given I log in as "admin"
    And I navigate to "Appearance > Themes" in site administration
    And I click on "Edit theme settings 'Boost'" "link"
    When I click on "Advanced settings" "link"
    Then the url should match "#theme_boost_advanced$"
    And I reload the page
    And "//a[@aria-selected = 'true' and normalize-space() = 'Advanced settings']" "xpath" should exist
