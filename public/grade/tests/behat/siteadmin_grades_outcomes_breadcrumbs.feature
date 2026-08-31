@core @core_grades @javascript
Feature: Verify the breadcrumbs in grade outcomes site administration pages
  Whenever I navigate to advanced features page in site administration
  As an admin
  The breadcrumbs should be visible

  Background:
    Given I log in as "admin"

  Scenario: Verify the breadcrumbs in grades outcomes by adding a new outcome, visiting editing page and visiting delete page as an admin
    Given I navigate to "Advanced features" in site administration
    And I click on "Enable learning outcomes" "checkbox"
    And I press "Save changes"
    And I navigate to "Grades > Manage learning outcomes" in site administration
    And I click on "Add a new learning outcome" "button"
    And "Add a learning outcome" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage learning outcomes" "link" should exist in the ".breadcrumb" "css_element"
    And I set the field "Full name" to "Outcome test"
    And I set the field "Short name" to "outcome_test"
    And I press "Save changes"
    When I click on "Edit" "link"
    Then "Edit learning outcome" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage learning outcomes" "link" should exist in the ".breadcrumb" "css_element"
    And I press "Cancel"
    And I click on "Delete" "link"
    And "Delete learning outcome" "text" should exist in the ".breadcrumb" "css_element"
    And "Manage learning outcomes" "link" should exist in the ".breadcrumb" "css_element"
