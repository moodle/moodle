@core @tool @tool_analytics @javascript
Feature: Verify the breadcrumbs in analytics site administration pages
  Whenever I navigate to analytics page in site administration to create, import, edit or restore models
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in analytics models page by visiting the create model, import model, restore model and edit page
    Given I navigate to "Analytics > Analytics models" in site administration
    And I click on "New model" "link"
    When I click on "Create model" "link"
    Then "Create model" "text" should exist in the ".breadcrumb" "css_element"
    And "Analytics model" "link" should exist in the ".breadcrumb" "css_element"
    And "Analytics" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    # Testing import model page
    And I click on "New model" "link"
    And I click on "Import model" "link"
    And "Import model" "text" should exist in the ".breadcrumb" "css_element"
    And "Analytics model" "link" should exist in the ".breadcrumb" "css_element"
    And "Analytics" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    # Testing restore defaults
    And I click on "New model" "link"
    And I click on "Restore default models" "link"
    And "Restore default models" "text" should exist in the ".breadcrumb" "css_element"
    And "Analytics model" "link" should exist in the ".breadcrumb" "css_element"
    And "Analytics" "link" should exist in the ".breadcrumb" "css_element"
    And I click on "Back" "link"
    # Testing edit page
    And I click on "Actions" "link"
    And I click on "Edit" "link"
    And "Edit \"Courses at risk of not starting\" model" "text" should exist in the ".breadcrumb" "css_element"
    And "Analytics model" "link" should exist in the ".breadcrumb" "css_element"
    And "Analytics" "link" should exist in the ".breadcrumb" "css_element"
