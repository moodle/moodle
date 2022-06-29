@core @core_user @javascript
Feature: Verify the breadcrumbs in users account and cohort site administration pages
  Whenever I navigate to pages under users tab in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  @core @core_user
  Scenario: Verify the breadcrumbs in users tab as an admin
    Given I navigate to "Users > Accounts > Add a new user" in site administration
    And "Add a new user" "text" should exist in the ".breadcrumb" "css_element"
    And "Accounts" "link" should exist in the ".breadcrumb" "css_element"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And "System cohorts" "text" should exist in the ".breadcrumb" "css_element"
    And "Accounts" "link" should exist in the ".breadcrumb" "css_element"
    When I click on "All cohorts" "link"
    Then "All cohorts" "text" should exist in the ".breadcrumb" "css_element"
    And "Accounts" "link" should exist in the ".breadcrumb" "css_element"
    And I click on "Add new cohort" "link"
    And "Add new cohort" "text" should exist in the ".breadcrumb" "css_element"
    And "Cohorts" "link" should exist in the ".breadcrumb" "css_element"
    And "Accounts" "link" should exist in the ".breadcrumb" "css_element"
    And I click on "Upload cohorts" "link"
    And "Upload cohorts" "text" should exist in the ".breadcrumb" "css_element"
    And "Cohorts" "link" should exist in the ".breadcrumb" "css_element"
    And "Accounts" "link" should exist in the ".breadcrumb" "css_element"
