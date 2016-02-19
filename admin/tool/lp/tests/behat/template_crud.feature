@tool @javascript @tool_lp @tool_lp_template
Feature: Manage plearning plan templates
  As a learning plan admin
  In order to perform CRUD operations on learning plan template
  I need to create, update and delete learning plan temlate

  Background:
    Given I log in as "admin"
    And I am on site homepage
    When I expand "Site administration" node
    Then I should see "Learning plans"

  Scenario: Create a new learning plan template
    Given I follow "Learning plans"
    And I should see "List of learning plan templates"
    And I click on "Add new learning plan template" "button"
    And I should see "Add new learning plan template"
    And I set the field "Name" to "Science template"
    And I set the field "Description" to "Here description of learning plan template"
    When I press "Save changes"
    Then I should see "Learning plan template created"
    And I click on "Continue" "button"
    And I should see "Science template"

  Scenario: Read a learning plan template
    Given the following lp "templates" exist:
      | shortname | description |
      | Science template Year-2 | science template description |
    And I follow "Learning plans"
    And I should see "Science template Year-2"
    When I click on "Science template Year-2" "link"
    Then I should see "Science template Year-2"
    And I should see "Template competencies"

  Scenario: Edit a learning plan template
    Given the following lp "templates" exist:
      | shortname | description |
      | Science template Year-3 | science template description |
    And I follow "Learning plans"
    And I should see "Science template Year-3"
    And I click on "Edit" of edit menu in the "Science template Year-3" row
    And the field "Name" matches value "Science template Year-3"
    And I set the field "Name" to "Science template Year-3 Edited"
    When I press "Save changes"
    Then I should see "Learning plan template updated"
    And I click on "Continue" "button"
    And I should see "Science template Year-3 Edited"

  Scenario: Delete a learning plan template
    Given the following lp "templates" exist:
      | shortname | description |
      | Science template Year-4 | science template description |
    And I follow "Learning plans"
    And I should see "Science template Year-4"
    And I click on "Delete" of edit menu in the "Science template Year-4" row
    And "Confirm" "dialogue" should be visible
    And "Delete" "button" should exist in the "Confirm" "dialogue"
    And "Cancel" "button" should exist in the "Confirm" "dialogue"
    And I click on "Cancel" "button"
    And I click on "Edit" "link" in the "Science template Year-4" "table_row"
    And I click on "Delete" of edit menu in the "Science template Year-4" row
    And "Confirm" "dialogue" should be visible
    When I click on "Delete" "button"
    Then I should not see "Science template Year-4"
