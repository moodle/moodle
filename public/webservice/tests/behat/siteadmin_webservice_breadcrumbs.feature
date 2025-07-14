@core @core_admin @core_webservice @javascript
Feature: Verify the breadcrumbs in external webservice site administration pages
  Whenever I navigate to external webservice page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in external services page
    Given I navigate to "Server > Web services > External services" in site administration
    And "External services" "text" should exist in the ".breadcrumb" "css_element"
    And "Web services" "link" should exist in the ".breadcrumb" "css_element"
    When I click on "Edit" "link"
    Then "Edit external service" "text" should exist in the ".breadcrumb" "css_element"
    And "External services" "link" should exist in the ".breadcrumb" "css_element"
    And "Web services" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Add" "link"
    And I set the field "Name" to "function to test"
    And "Add external service" "text" should exist in the ".breadcrumb" "css_element"
    And "External services" "link" should exist in the ".breadcrumb" "css_element"
    And "Web services" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Add service"
    And "Functions" "text" should exist in the ".breadcrumb" "css_element"
    And "External services" "link" should exist in the ".breadcrumb" "css_element"
    And "Web services" "link" should exist in the ".breadcrumb" "css_element"
    And I navigate to "Server > Web services > External services" in site administration
    And I click on "Delete" "link"
    And "External services" "text" should exist in the ".breadcrumb" "css_element"
    And "Web services" "link" should exist in the ".breadcrumb" "css_element"
