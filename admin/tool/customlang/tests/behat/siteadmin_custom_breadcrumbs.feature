@tool @tool_customlang @javascript
Feature: Verify the breadcrumbs in language customisation site administration pages
  Whenever I navigate to language customisation page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in language customisation page by visiting open language pack for editing and import custom strings pages
    Given I navigate to "Language > Language customisation" in site administration
    And I set the field "lng" to "en"
    When I click on "Open language pack for editing" "button"
    And I wait until the page is ready
    And "Language customisation" "text" should exist in the ".breadcrumb" "css_element"
    And "Language" "link" should exist in the ".breadcrumb" "css_element"
    And I press the "back" button in the browser
    And I click on "Import custom strings" "button"
    And I wait until the page is ready
    And "Language customisation" "text" should exist in the ".breadcrumb" "css_element"
    And "Language" "link" should exist in the ".breadcrumb" "css_element"
