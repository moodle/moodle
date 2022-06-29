@core_grades @javascript
Feature: Verify the breadcrumbs in grade scales site administration pages
  Whenever I navigate to grade scales page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  @core_grades
  Scenario: Verify the breadcrumbs in scales page as an admin
    Given I navigate to "Grades > Scales" in site administration
    And I click on "Add a new scale" "button"
    And "Add a scale" "text" should exist in the ".breadcrumb" "css_element"
    And "Scales" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    When I click on "Edit" "link"
    Then "Edit scale" "text" should exist in the ".breadcrumb" "css_element"
    And "Scales" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Delete" "link"
    And "Delete scale" "text" should exist in the ".breadcrumb" "css_element"
    And "Scales" "link" should exist in the ".breadcrumb" "css_element"
