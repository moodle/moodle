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
