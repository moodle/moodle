@tool @tool_behat
Feature: List the system steps definitions
  In order to create new tests
  As a tests writer
  I need to list and filter the system steps definitions

  Background:
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Development > Acceptance testing" in site administration

  @javascript
  Scenario: Accessing the list
    Then I should see "Step definitions"
    And I should not see "There aren't steps definitions matching this filter"
    And I should not see "the following \"ELEMENT_STRING\" exist:"
    And "entities1" "select" should exist
    And the "entities1" select box should contain "users"
    And the "entities1" select box should contain "mod_assign > submissions"

  @javascript
  Scenario: Filtering by type
    Given I set the field "Type" to "Then. Checkings to ensure the outcomes are the expected ones"
    When I press "Filter"
    Then I should see "Checks, that page contains specified text."
    And I should not see "Opens Moodle homepage."

  @javascript
  Scenario: Filtering by keyword
    Given I set the field "Contains" to "homepage"
    When I press "Filter"
    Then I should see "Opens Moodle homepage."

  @javascript
  Scenario: Filtering by the multiple words pattern
    Given I set the field "Contains" to "should exist"
    When I press "Filter"
    Then I should not see "There aren't steps definitions matching this filter"
    And I should see "Checks the provided element and selector type exists in the current page."
    And I should see "Checks that an element and selector type exists in another element and selector type on the current page."

  @javascript
  Scenario: Get required fields
    Given I set the field "entities1" to "users"
    And I should see "| username |"
    When I set the field "entities1" to "mod_quiz > user overrides"
    Then I should not see "| username |"
    And I should see "| quiz | user |"
