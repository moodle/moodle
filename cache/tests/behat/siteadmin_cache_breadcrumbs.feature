@core @core_cache @javascript
Feature: Verify the breadcrumbs in different cache site administration pages
  Whenever I navigate to caching configuration page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in caching configuration, add assistance, edit mappings and edit sharings page as an admin
    Given I navigate to "Plugins > Caching > Configuration" in site administration
    And "Configuration" "text" should exist in the ".breadcrumb" "css_element"
    And "Caching" "link" should exist in the ".breadcrumb" "css_element"
    When I click on "Add instance" "link"
    Then "Add cache store" "text" should exist in the ".breadcrumb" "css_element"
    And "Configuration" "link" should exist in the ".breadcrumb" "css_element"
    And "Caching" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Edit mappings" "link"
    And "Edit definition mapping" "text" should exist in the ".breadcrumb" "css_element"
    And "Configuration" "link" should exist in the ".breadcrumb" "css_element"
    And "Caching" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Edit sharing" "link"
    And "Edit definition sharing" "text" should exist in the ".breadcrumb" "css_element"
    And "Configuration" "link" should exist in the ".breadcrumb" "css_element"
    And "Caching" "link" should exist in the ".breadcrumb" "css_element"
