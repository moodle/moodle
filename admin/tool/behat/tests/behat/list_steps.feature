@tool @tool_behat
Feature: List the system steps definitions
  In order to create new tests
  As a tests writer
  I need to list and filter the system steps definitions

  Background:
    Given I am on homepage
    And I log in as "admin"
    And I expand "Site administration" node
    And I expand "Development" node
    And  I follow "Acceptance testing"

  @javascript
  Scenario: Accessing the list
    Then I should see "Steps definitions"
    And I should not see "There aren't steps definitions matching this filter"

  @javascript
  Scenario: Filtering by type
    Given I select "Then. Checkings to ensure the outcomes are the expected ones" from "Type"
    When I press "Filter"
    Then I should see "Checks, that page contains specified text."
    And I should not see "Opens Moodle homepage."

  @javascript
  Scenario: Filtering by keyword
    Given I fill in "Contains" with "homepage"
    When I press "Filter"
    Then I should see "Opens Moodle homepage."

