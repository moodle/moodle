@tool @tool_monitor @javascript
Feature: Verify the breadcrumbs in different event monitor site administration pages
  Whenever I navigate to event monitoring page in site administration
  As an admin
  The breadcrumbs should be visible
  Whenever I land on pages to add a new rule, edit rule or delete rule
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in event monitoring page by visiting add a new rule, edit a rule and delete rule pages as an admin
    Given I navigate to "Reports > Event monitoring rules" in site administration
    And I click on "Enable" "link"
    When I click on "Add a new rule" "link"
    Then "Add a new rule" "text" should exist in the ".breadcrumb" "css_element"
    And "Event monitoring rules" "link" should exist in the ".breadcrumb" "css_element"
    And I set the field "Rule name" to "Testing1"
    And I set the field "Area to monitor" to "Core"
    And I set the field "Event" to "Allow role override"
    And I press "Save changes"
    And I click on "Edit rule" "link"
    And "Edit rule" "text" should exist in the ".breadcrumb" "css_element"
    And "Event monitoring rules" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Delete rule" "link"
    And "Delete rule" "text" should exist in the ".breadcrumb" "css_element"
    And "Event monitoring rules" "link" should exist in the ".breadcrumb" "css_element"
