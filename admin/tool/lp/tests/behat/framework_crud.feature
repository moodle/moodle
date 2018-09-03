@tool @javascript @tool_lp @tool_lp_framework
Feature: Manage competency frameworks
  As a competency framework admin
  In order to perform CRUD operations on competency framework
  I need to create, update and delete competency framework

  Background:
    Given I log in as "admin"
    And I am on site homepage

  Scenario: Create a new framework
    Given I navigate to "Competencies > Competency frameworks" in site administration
    And I should see "List of competency frameworks"
    And I click on "Add new competency framework" "button"
    And I should see "General"
    And I should see "Taxonomies"
    And I set the field "Name" to "Science Year-1"
    And I set the field "ID number" to "Comp-frm-1"
    And I press "Save changes"
    And I should see "The scale needs to be configured by selecting default and proficient items."
    And "Configure scales" "button" should be visible
    And I press "Configure scales"
    And I click on "//input[@data-field='tool_lp_scale_default_1']" "xpath_element"
    And I click on "//input[@data-field='tool_lp_scale_proficient_1']" "xpath_element"
    And I click on "//input[@value='Close']" "xpath_element"
    When I press "Save changes"
    Then I should see "Competency framework created"
    And I should see "Science Year-1"

  Scenario: Read a framework
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Science Year-2 | sc-y-2 |
    And I navigate to "Competencies > Competency frameworks" in site administration
    And I should see "Science Year-2"
    When I click on "Science Year-2" "link"
    Then I should see "Science Year-2"

  Scenario: Edit a framework
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Science Year-3 | sc-y-3 |
    And I navigate to "Competencies > Competency frameworks" in site administration
    And I should see "Science Year-3"
    And I click on "Edit" of edit menu in the "Science Year-3" row
    And the field "Name" matches value "Science Year-3 "
    And I set the field "Name" to "Science Year-3 Edited"
    When I press "Save changes"
    Then I should see "Competency framework updated"
    And I should see "Science Year-3 Edited"
    And I should see "sc-y-3"

  Scenario: Delete a framework
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Science Year-4 | sc-y-4 |
    And I navigate to "Competencies > Competency frameworks" in site administration
    And I should see "Science Year-4"
    And I should see "sc-y-4"
    And I click on "Delete" of edit menu in the "Science Year-4" row
    And "Confirm" "dialogue" should be visible
    And "Delete" "button" should exist in the "Confirm" "dialogue"
    And "Cancel" "button" should exist in the "Confirm" "dialogue"
    And I click on "Cancel" "button"
    And I click on "Delete" of edit menu in the "Science Year-4" row
    And "Confirm" "dialogue" should be visible
    When I click on "Delete" "button"
    Then I should not see "Science Year-4"
    And I should not see "sc-y-4"

  Scenario: Edit a framework with competencies in user competency
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Science Year-5 | sc-y-5 |
    And the following lp "competencies" exist:
      | shortname | framework |
      | Comp1 | sc-y-5 |
      | Comp2 | sc-y-5 |
    And the following lp "plans" exist:
      | name | user | description |
      | Plan Science-5 | admin | Plan description |
    And the following lp "plancompetencies" exist:
      | plan | competency |
      | Plan Science-5 | Comp1 |
      | Plan Science-5 | Comp2 |
    And the following lp "usercompetencies" exist:
      | user | competency |
      | admin | Comp1 |
      | admin | Comp2 |
    And I navigate to "Competencies > Competency frameworks" in site administration
    And I should see "Science Year-5"
    And I click on "Edit" of edit menu in the "Science Year-5" row
    And the field "Name" matches value "Science Year-5 "
    And I set the field "Name" to "Science Year-5 Edited"
    And the "scaleid" "select" should be readonly
    When I press "Save changes"
    Then I should see "Competency framework updated"
    And I should see "Science Year-5 Edited"
    And I should see "sc-y-5"

  Scenario: Edit a framework with competencies in user competency plan
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Science Year-6 | sc-y-6 |
    And the following lp "competencies" exist:
      | shortname | framework |
      | Comp1 | sc-y-6 |
      | Comp2 | sc-y-6 |
    And the following lp "plans" exist:
      | name | user | description |
      | Plan Science-6 | admin | Plan description |
    And the following lp "plancompetencies" exist:
      | plan | competency |
      | Plan Science-6 | Comp1 |
      | Plan Science-6 | Comp2 |
    And the following lp "usercompetencyplans" exist:
      | user | competency | plan |
      | admin | Comp1 | Plan Science-6 |
      | admin | Comp2 | Plan Science-6 |
    And I navigate to "Competencies > Competency frameworks" in site administration
    And I should see "Science Year-6"
    And I click on "Edit" of edit menu in the "Science Year-6" row
    And the field "Name" matches value "Science Year-6 "
    And I set the field "Name" to "Science Year-6 Edited"
    And the "scaleid" "select" should be readonly
    When I press "Save changes"
    Then I should see "Competency framework updated"
    And I should see "Science Year-6 Edited"
    And I should see "sc-y-6"
