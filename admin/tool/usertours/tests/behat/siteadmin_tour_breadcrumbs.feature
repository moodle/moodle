@tool @tool_usertours @javascript
Feature: Verify the breadcrumbs in user tours site administration pages
  Whenever I navigate to user tours page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in user tours, expand to explore, next step, create and import pages as admin
    Given I navigate to "Appearance > User tours" in site administration
    And I click on "Block drawer" "link"
    And "Block drawer" "text" should exist in the ".breadcrumb" "css_element"
    And "User tours" "link" should exist in the ".breadcrumb" "css_element"
    When I click on "Expand to explore" "link"
    Then "Expand to explore" "text" should exist in the ".breadcrumb" "css_element"
    And "Block drawer" "link" should exist in the ".breadcrumb" "css_element"
    And "User tours" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "New step" "link"
    And "New step" "text" should exist in the ".breadcrumb" "css_element"
    And "Block drawer" "link" should exist in the ".breadcrumb" "css_element"
    And "User tours" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I navigate to "Appearance > User tours" in site administration
    And I click on "Create a new tour" "link"
    And "Create a new tour" "text" should exist in the ".breadcrumb" "css_element"
    And "User tours" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Import tour" "link"
    And "Import tour" "text" should exist in the ".breadcrumb" "css_element"
    And "User tours" "link" should exist in the ".breadcrumb" "css_element"
