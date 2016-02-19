@tool @javascript @tool_lp @tool_lp_framework
Feature: Manage competency frameworks
  As a competency framework admin
  In order to perform CRUD operations on competency framework
  I need to create, update and delete competency framework

  Background:
    Given I log in as "admin"
    And I am on site homepage
    When I expand "Site administration" node
    Then I should see "Competency Frameworks"

  Scenario: Create a new framework
    Given I follow "Competency Frameworks"
    And I should see "List of competency frameworks"
    And I click on "Add new competency framework" "button"
    And I should see "General"
    And I should see "Taxonomies"
    And I set the field "Name" to "Science Year-1"
    And I set the field "Id number" to "Comp-frm-1"
    And I press "Save changes"
    And I should see "You must configure the scale by selecting default and proficient values"
    And "Configure scales" "button" should be visible
    And I press "Configure scales"
    And I click on "#tool_lp_scale_default_1" "css_element"
    And I click on "#tool_lp_scale_proficient_1" "css_element"
    And I click on "//input[@value='Close']" "xpath_element"
    When I press "Save changes"
    Then I should see "Competency framework created"
    And I click on "Continue" "button"
    And I should see "Science Year-1"

  Scenario: Read a framework
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Science Year-2 | sc-y-2 |
    And I follow "Competency Frameworks"
    And I should see "Science Year-2"
    When I click on "Science Year-2" "link"
    Then I should see "Science Year-2"

  Scenario: Edit a framework
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Science Year-3 | sc-y-3 |
    And I follow "Competency Frameworks"
    And I should see "Science Year-3"
    And I click on "Edit" of edit menu in the "Science Year-3" row
    And the field "Name" matches value "Science Year-3 "
    And I set the field "Name" to "Science Year-3 Edited"
    When I press "Save changes"
    Then I should see "Competency framework updated"
    And I click on "Continue" "button"
    And I should see "Science Year-3 Edited"
    And I should see "sc-y-3"

  Scenario: Delete a framework
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Science Year-4 | sc-y-4 |
    And I follow "Competency Frameworks"
    And I should see "Science Year-4"
    And I should see "sc-y-4"
    And I click on "Delete" of edit menu in the "Science Year-4" row
    And "Confirm" "dialogue" should be visible
    And "Delete" "button" should exist in the "Confirm" "dialogue"
    And "Cancel" "button" should exist in the "Confirm" "dialogue"
    And I click on "Cancel" "button"
    And I click on "Edit" "link" in the "Science Year-4" "table_row"
    And I click on "Delete" of edit menu in the "Science Year-4" row
    And "Confirm" "dialogue" should be visible
    When I click on "Delete" "button"
    Then I should not see "Science Year-4"
    And I should not see "sc-y-4"