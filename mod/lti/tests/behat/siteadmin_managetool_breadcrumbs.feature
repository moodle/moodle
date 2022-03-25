@mod @mod_lti @javascript
Feature: Verify the breadcrumbs in manage tools site administration pages
  Whenever I navigate to manage tools page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in manage tools page as an admin
    Given I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And "Manage tools" "text" should exist in the ".breadcrumb" "css_element"
    And "External tool" "link" should exist in the ".breadcrumb" "css_element"
    And "Activity modules" "link" should exist in the ".breadcrumb" "css_element"
    When I click on "configure a tool manually" "link"
    Then "External tool configuration" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage tools" "link" should exist in the ".breadcrumb" "css_element"
    And "External tool" "link" should exist in the ".breadcrumb" "css_element"
    And "Activity modules" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Manage preconfigured tools" "link"
    And "Manage preconfigured tools" "text" should exist in the ".breadcrumb" "css_element"
    And "External tool" "link" should exist in the ".breadcrumb" "css_element"
    And "Activity modules" "link" should exist in the ".breadcrumb" "css_element"
    And I click on "Add preconfigured tool" "link"
    And "External tool configuration" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage tools" "link" should exist in the ".breadcrumb" "css_element"
    And "External tool" "link" should exist in the ".breadcrumb" "css_element"
    And "Activity modules" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And I click on "Manage external tool registrations" "link"
    And "Manage external tool registrations" "text" should exist in the ".breadcrumb" "css_element"
    And "External tool" "link" should exist in the ".breadcrumb" "css_element"
    And "Activity modules" "link" should exist in the ".breadcrumb" "css_element"
    And I click on "Configure a new external tool registration" "link"
    And "Edit preconfigured tool" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage external tool registrations" "link" should exist in the ".breadcrumb" "css_element"
    And "External tool" "link" should exist in the ".breadcrumb" "css_element"
    And "Activity modules" "link" should exist in the ".breadcrumb" "css_element"
