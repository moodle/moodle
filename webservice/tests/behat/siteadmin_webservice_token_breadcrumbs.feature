@core @core_admin @core_webservice @javascript
Feature: Verify the breadcrumbs in webservice tokens site administration pages
  Whenever I navigate to manage tokens page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in manage tokens page as an admin
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | student1 | John      | Doe      | s1@example.com |
    And I log in as "admin"
    And I navigate to "Server > Web services > Manage tokens" in site administration
    And I click on "Create token" "button"
    And "Create token" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage tokens" "link" should exist in the ".breadcrumb" "css_element"
    And "Web services" "link" should exist in the ".breadcrumb" "css_element"
    And I set the field "User" to "John Doe"
    And I press "Save changes"
    When I press "Delete" action in the "John Doe" report row
    Then "Delete token" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage tokens" "link" should exist in the ".breadcrumb" "css_element"
    And "Web services" "link" should exist in the ".breadcrumb" "css_element"
