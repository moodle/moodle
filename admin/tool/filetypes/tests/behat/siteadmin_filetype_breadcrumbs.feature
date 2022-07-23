@tool @tool_filetypes @javascript
Feature: Verify the breadcrumbs in server filetypes site administration pages
  Whenever I navigate to file types page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  @tool @tool_filetypes
  Scenario: Verify the breadcrumbs in file types page by visiting edit page and delete page of a file type as an admin
    Given I navigate to "Server > File types" in site administration
    And I click on "Edit 3gp" "link"
    And "3gp" "text" should exist in the ".breadcrumb" "css_element"
    And "File types" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    When I click on "Delete 3gp" "link"
    Then "Delete a file type" "text" should exist in the ".breadcrumb" "css_element"
    And "File types" "link" should exist in the ".breadcrumb" "css_element"
