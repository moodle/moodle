@core @tool @tool_dataprivacy @javascript
Feature: Verify the breadcrumbs in different privacy site administration pages
  Whenever I navigate to data registry page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in data registry page as an admin
    Given I navigate to "Users > Privacy and policies > Data registry" in site administration
    And "Data registry" "text" should exist in the ".breadcrumb" "css_element"
    And "Privacy and policies" "link" should exist in the ".breadcrumb" "css_element"
    When I click on "Set defaults" "link"
    Then "Set defaults" "text" should exist in the ".breadcrumb" "css_element"
    And "Data registry" "link" should exist in the ".breadcrumb" "css_element"
    And "Privacy and policies" "link" should exist in the ".breadcrumb" "css_element"
    And I navigate to "Users > Privacy and policies > Data registry" in site administration
    And I click on "Edit" "link"
    And I choose "Categories" in the open action menu
    And "Edit categories" "text" should exist in the ".breadcrumb" "css_element"
    And "Data registry" "link" should exist in the ".breadcrumb" "css_element"
    And "Privacy and policies" "link" should exist in the ".breadcrumb" "css_element"
    And I click on "Back" "link"
    And I click on "Edit" "link"
    And I choose "Purposes" in the open action menu
    And "Edit purposes" "text" should exist in the ".breadcrumb" "css_element"
    And "Data registry" "link" should exist in the ".breadcrumb" "css_element"
    And "Privacy and policies" "link" should exist in the ".breadcrumb" "css_element"
