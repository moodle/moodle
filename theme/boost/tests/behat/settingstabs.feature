@javascript @theme_boost
Feature: Administration nav tabs back
  When returning to the Administration page I want to see my last opened tab

  Scenario: See last opened tab in site admin
    Given I log in as "admin"
    And I am on site homepage
    And I click on "Site administration" "link"
    And I click on "Users" "link"
    And I click on "Browse list of users" "link"
    And I should see "New filter"
    When I press the "back" button in the browser
    Then I should see "Cohorts"